<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailRecipient;
use App\Models\EmailTracking;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailRecipientId;

    /**
     * Create a new job instance.
     */
    public function __construct($emailRecipientId)
    {
        $this->emailRecipientId = $emailRecipientId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("SendEmailJob: Starting job for recipient ID: {$this->emailRecipientId}");

        $recipient = EmailRecipient::find($this->emailRecipientId, ['*']);

        if (!$recipient) {
            Log::error("SendEmailJob: Recipient not found with ID: {$this->emailRecipientId}");
            return;
        }

        Log::info("SendEmailJob: Found recipient {$recipient->email} with status: {$recipient->status}");

        if ($recipient->status === 'sent') {
            Log::warning("SendEmailJob: Email already sent to {$recipient->email}");
            return;
        }

        try {
            // Get the campaign and template
            Log::info("SendEmailJob: Getting campaign and template for recipient {$recipient->email}");
            $campaign = $recipient->campaign;
            $template = $campaign->emailTemplate;

            Log::info("SendEmailJob: Campaign: {$campaign->name}, Template: {$template->name}");

            // Personalize the email content
            Log::info("SendEmailJob: Personalizing content for {$recipient->email}");
            $content = $this->personalizeContent($template->content, $recipient);
            $subject = $this->personalizeContent($template->subject, $recipient);

            // Add tracking pixel to HTML content
            if ($template->is_html) {
                Log::info("SendEmailJob: Adding tracking pixel for {$recipient->email}");
                $trackingPixel = $this->generateTrackingPixel($recipient->tracking_token);
                $content .= $trackingPixel;
            }

            // Send the email
            Log::info("SendEmailJob: Attempting to send email to {$recipient->email}");
            Log::info("SendEmailJob: Content length: " . strlen($content));
            Log::info("SendEmailJob: Subject: {$subject}");

            $result = Mail::html($content, function ($message) use ($recipient, $subject, $template) {
                $message->to($recipient->email, $recipient->name)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));

                if ($template->attachment_path) {
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($template->attachment_path);
                    Log::info("SendEmailJob: Attaching file: " . $fullPath);
                    if (file_exists($fullPath)) {
                        $message->attach($fullPath, [
                            'as' => $template->attachment_name,
                        ]);
                        Log::info("SendEmailJob: File attached successfully to message object.");
                    } else {
                        Log::error("SendEmailJob: Attachment file NOT FOUND at: " . $fullPath);
                    }
                }
            });

            Log::info("SendEmailJob: Mail::html() returned: " . ($result ? 'true' : 'false'));

            Log::info("SendEmailJob: Email sent successfully to {$recipient->email}");

            // Update recipient status
            $recipient->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Log success
            Log::info("Email sent successfully to {$recipient->email} for campaign {$campaign->name}");

            // Update campaign stats
            $campaign->increment('emails_sent');

        } catch (\Exception $e) {
            Log::error("SendEmailJob: Exception occurred while sending email to {$recipient->email}: " . $e->getMessage());
            Log::error("SendEmailJob: Exception trace: " . $e->getTraceAsString());

            // Update recipient status to failed
            $recipient->update([
                'status' => 'failed',
                'sent_at' => now()
            ]);

            // Update campaign stats
            $campaign->increment('emails_failed');

            // Log error
            Log::error("Failed to send email to {$recipient->email}: " . $e->getMessage());
        }
    }

    /**
     * Personalize email content with recipient data
     */
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

    /**
     * Generate tracking pixel HTML
     */
    protected function generateTrackingPixel($trackingToken)
    {
        $trackingUrl = url("/track/{$trackingToken}");
        return "<img src=\"{$trackingUrl}\" width=\"1\" height=\"1\" style=\"display:none;\" alt=\"\">";
    }
}
