<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\EmailList;
use App\Models\EmailRecipient;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Campaigns extends Component
{
    // Progress tracking properties
    public $runningCampaignId = null;
    public $progressPercentage = 0;
    public $progressMessage = '';
    public $emailsProcessed = 0;
    public $totalEmails = 0;

    #[Layout('layouts.app')]
    public function render()
    {
        $campaigns = Campaign::query()
            ->where('user_id', '=', (int) auth()->id())
            ->with(['emailTemplate', 'emailList'])
            ->latest()
            ->paginate(10);

        // Check if any campaign is running or scheduled to trigger UI polling
        $anyRunning = Campaign::query()
            ->where('user_id', '=', (int) auth()->id())
            ->whereIn('status', ['running', 'scheduled'])
            ->exists();

        return view('livewire.campaigns', [
            'campaigns' => $campaigns,
            'anyRunning' => $anyRunning,
        ]);
    }


    public function startCampaign($campaignId)
    {
        $campaign = Campaign::query()
            ->where('id', '=', (int) $campaignId)
            ->where('user_id', '=', (int) auth()->id())
            ->first();

        if (!$campaign || ($campaign->status !== 'pending' && $campaign->status !== 'failed')) {
            session()->flash('error', 'Campania nu poate fi pornită!');
            return;
        }

        $now = now();
        $isScheduled = $campaign->scheduled_at && $campaign->scheduled_at->isAfter($now);

        // Update campaign status
        $campaign->update([
            'status' => $isScheduled ? 'scheduled' : 'running',
            'started_at' => $isScheduled ? null : $now,
            'error_message' => null
        ]);

        // Associate all recipients with this campaign and reset their status to pending
        $campaign->emailList->emailRecipients()->update([
            'campaign_id' => $campaign->id,
            'status' => 'pending',
            'sent_at' => null
        ]);

        // Dispatch background job
        if ($isScheduled) {
            \App\Jobs\ProcessCampaignJob::dispatch($campaign->id)->delay($campaign->scheduled_at);
            session()->flash('message', 'Campania a fost programată pentru ' . $campaign->scheduled_at->format('d.m.Y H:i') . '!');
        } else {
            \App\Jobs\ProcessCampaignJob::dispatch($campaign->id);
            session()->flash('message', 'Campania a fost adăugată în coada de procesare!');
        }
    }

    protected function resetProgress()
    {
        $this->runningCampaignId = null;
        $this->progressPercentage = 0;
        $this->progressMessage = '';
        $this->emailsProcessed = 0;
        $this->totalEmails = 0;
    }

    public function pollRefresh()
    {
        // This method is called by Livewire polling
        // We can add any additional logic here if needed
        return;
    }

    protected function sendEmailToRecipient($recipient, $campaign)
    {
        // Get the campaign and template
        $template = $campaign->emailTemplate;

        // Personalize the email content
        $content = $this->personalizeContent($template->content, $recipient);
        $subject = $this->personalizeContent($template->subject, $recipient);

        // Add tracking pixel to HTML content
        if ($template->is_html) {
            $trackingPixel = $this->generateTrackingPixel($recipient->tracking_token);
            $content .= $trackingPixel;
        }

        // Send the email synchronously
        Mail::html($content, function ($message) use ($recipient, $subject, $template) {
            $message->to($recipient->email, $recipient->name)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));

            if ($template->attachment_path) {
                $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($template->attachment_path);
                if (file_exists($fullPath)) {
                    $message->attach($fullPath, [
                        'as' => $template->attachment_name,
                    ]);
                }
            }
        });

        // Update recipient status
        $recipient->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    protected function personalizeContent($content, $recipient)
    {
        $replacements = [
            '{name}' => $recipient->name,
            '{email}' => $recipient->email,
            '{date}' => now()->format('Y-m-d'),
            '{campaign_name}' => $recipient->campaign->name,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    protected function generateTrackingPixel($trackingToken)
    {
        $trackingUrl = url("/track/{$trackingToken}");
        return "<img src=\"{$trackingUrl}\" width=\"1\" height=\"1\" style=\"display:none;\" alt=\"\">";
    }

    public function pauseCampaign($campaignId)
    {
        $campaign = Campaign::query()
            ->where('id', '=', (int) $campaignId)
            ->where('user_id', '=', (int) auth()->id())
            ->first();

        if ($campaign && $campaign->status === 'running') {
            $campaign->update(['status' => 'paused']);
            session()->flash('message', 'Campanie oprită temporar!');
        }
    }

    public function deleteCampaign($campaignId)
    {
        $campaign = Campaign::query()
            ->where('id', '=', (int) $campaignId)
            ->where('user_id', '=', (int) auth()->id())
            ->first();

        if ($campaign) {
            Campaign::destroy($campaign->id);
            session()->flash('message', 'Campanie ștearsă cu succes!');
        }

        $this->dispatch('campaignDeleted');
    }

    public function getStatusColor($status)
    {
        switch ($status) {
            case 'pending':
                return 'bg-gray-100 text-gray-800';
            case 'running':
                return 'bg-green-100 text-green-800';
            case 'scheduled':
                return 'bg-purple-100 text-purple-800';
            case 'completed':
                return 'bg-blue-100 text-blue-800';
            case 'failed':
                return 'bg-red-100 text-red-800';
            case 'paused':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}
