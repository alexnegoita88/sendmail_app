<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use App\Models\EmailRecipient;
use App\Models\CampaignResult;
use App\Models\RateLimitLog;
use App\Jobs\SendEmailJob;

class ProcessCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignId;

    /**
     * Create a new job instance.
     */
    public function __construct($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("ProcessCampaignJob: Starting campaign processing for ID: {$this->campaignId}");

        $campaign = Campaign::find($this->campaignId, ['*']);

        if (!$campaign) {
            Log::error("ProcessCampaignJob: Campaign not found: {$this->campaignId}");
            return;
        }

        Log::info("ProcessCampaignJob: Found campaign {$campaign->name} with status: {$campaign->status}");

        // Update campaign status to running
        $campaign->update([
            'status' => 'running',
            'started_at' => now()
        ]);

        Log::info("ProcessCampaignJob: Updated campaign {$campaign->name} status to running");

        // Get pending results for this campaign
        Log::info("ProcessCampaignJob: Getting pending results for campaign {$campaign->name}");
        $results = $campaign->campaignResults()->where('status', '=', 'pending')->get();

        Log::info("ProcessCampaignJob: Found {$results->count()} pending results for campaign {$campaign->name}");

        if ($results->isEmpty()) {
            Log::info("ProcessCampaignJob: No pending results found for campaign {$campaign->name}");
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            return;
        }

        $emailsSent = 0;
        $currentTime = now();

        Log::info("ProcessCampaignJob: Starting to dispatch email jobs for campaign {$campaign->name}");

        foreach ($results as $result) {
            Log::info("ProcessCampaignJob: Processing result ID {$result->id} for campaign {$campaign->name}");

            // Check rate limiting
            $limit = config('app.email_rate_limit', env('EMAIL_RATE_LIMIT', 50));
            if (!$this->checkRateLimit($limit)) {
                Log::warning("ProcessCampaignJob: Rate limit exceeded for campaign {$campaign->name}, releasing job back to queue");
                // Rate limit exceeded, delay the job
                $this->release(60); // Release back to queue after 60 seconds
                return;
            }

            // Dispatch email sending job
            Log::info("ProcessCampaignJob: Dispatching SendEmailJob for result ID {$result->id}");
            SendEmailJob::dispatch($result->id);

            $emailsSent++;

            Log::info("ProcessCampaignJob: Dispatched {$emailsSent} email jobs so far for campaign {$campaign->name}");

            // Rate limiting: wait between emails
            if ($emailsSent % $limit === 0) {
                // After every batch, wait for the minute to reset
                Log::info("ProcessCampaignJob: Waiting 1 second after batch of {$limit} emails for campaign {$campaign->name}");
                sleep(1);
            } else {
                // Small delay between emails
                usleep(100000); // 0.1 seconds
            }
        }

        // Update campaign completion
        Log::info("ProcessCampaignJob: Campaign {$campaign->name} completed, updating status");
        $campaign->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        Log::info("Campaign {$campaign->name} completed. Sent {$emailsSent} emails.");
    }

    /**
     * Check if we can send more emails based on rate limiting
     */
    protected function checkRateLimit(int $limit): bool
    {
        $now = now();
        $minuteStart = $now->copy()->startOfMinute();
        $minuteEnd = $now->copy()->endOfMinute();

        // Check if we have a rate limit log for this minute
        $rateLimit = RateLimitLog::query()
            ->where('type', '=', 'email_sending')
            ->whereBetween('created_at', [$minuteStart, $minuteEnd])
            ->first();

        if (!$rateLimit) {
            // Create new rate limit log
            RateLimitLog::query()->create([
                'type' => 'email_sending',
                'count' => 1,
                'reset_at' => $minuteEnd
            ]);
            return true;
        }

        // Check if we've reached the limit
        if ($rateLimit->count >= $limit) {
            return false;
        }

        // Increment count
        $rateLimit->increment('count', 1, []);
        return true;
    }
}
