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

        return view('livewire.campaigns', [
            'campaigns' => $campaigns,
        ]);
    }


    public function startCampaign($campaignId)
    {
        $campaign = Campaign::query()
            ->where('id', '=', (int) $campaignId)
            ->where('user_id', '=', (int) auth()->id())
            ->first();

        if (!$campaign || $campaign->status !== 'pending') {
            session()->flash('error', 'Campania nu poate fi pornită!');
            return;
        }

        // Initialize progress tracking
        $this->runningCampaignId = $campaignId;
        $this->progressPercentage = 0;
        $this->progressMessage = 'Inițializare campanie...';
        $this->emailsProcessed = 0;

        // Force Livewire to re-render and show progress bar
        $this->dispatch('campaign-progress-started');

        // Update campaign status to running
        $campaign->update([
            'status' => 'running',
            'started_at' => now()
        ]);

        // Get all recipients from the email list and associate them with this campaign
        $allRecipients = $campaign->emailList->emailRecipients()->get();
        $this->totalEmails = $allRecipients->count();

        if ($allRecipients->isEmpty()) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            $this->resetProgress();
            session()->flash('message', 'Campania a fost finalizată - lista de emailuri este goală!');
            return;
        }

        // Associate all recipients with this campaign and reset their status to pending
        $campaign->emailList->emailRecipients()->update([
            'campaign_id' => $campaign->id,
            'status' => 'pending',
            'sent_at' => null
        ]);

        // Now get the recipients that are associated with this campaign
        $recipients = $campaign->emailRecipients()->where('status', '=', 'pending')->get();

        // Send emails synchronously with progress updates
        $emailsSent = 0;
        $emailsFailed = 0;

        foreach ($recipients as $index => $recipient) {
            try {
                // Update progress before sending
                $this->emailsProcessed = $index;
                $this->progressPercentage = intval((($index) / $this->totalEmails) * 100);
                $this->progressMessage = "Trimitere email către {$recipient->email}...";

                // Send email directly using SendEmailJob logic
                $this->sendEmailToRecipient($recipient, $campaign);
                $emailsSent++;

                // Update progress after successful send
                $this->emailsProcessed = $index + 1;
                $this->progressPercentage = intval((($index + 1) / $this->totalEmails) * 100);
                $this->progressMessage = "Email trimis către {$recipient->email} ✅";

                // Small delay between emails to avoid rate limiting issues
                usleep(100000); // 0.1 seconds

            } catch (\Exception $e) {
                $emailsFailed++;
                $this->progressMessage = "Eroare la {$recipient->email} ❌";
                Log::error("Failed to send email to {$recipient->email}: " . $e->getMessage());
            }
        }

        // Final progress update
        $this->progressPercentage = 100;
        $this->progressMessage = 'Finalizare campanie...';

        // Update campaign final status
        $campaign->update([
            'status' => 'completed',
            'completed_at' => now(),
            'emails_sent' => $emailsSent,
            'emails_failed' => $emailsFailed
        ]);

        // Reset progress after a short delay
        $this->resetProgress();

        session()->flash('message', "Campanie finalizată! Trimise: {$emailsSent}, Eșuate: {$emailsFailed}");
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
