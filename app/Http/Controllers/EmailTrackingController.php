<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CampaignResult;
use App\Models\EmailRecipient;
use App\Models\EmailTracking;

class EmailTrackingController extends Controller
{
    /**
     * Track email open via tracking pixel
     */
    public function trackOpen($token)
    {
        $result = CampaignResult::query()->where('tracking_token', '=', $token)->first();

        if (!$result) {
            return response('', 404)->header('Content-Type', 'image/gif');
        }

        // Update result status to opened if not already
        if ($result->status !== 'opened') {
            $result->update([
                'status' => 'opened',
                'opened_at' => now(),
                'ip_address' => $this->getIpAddress(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            // Update campaign stats
            $result->campaign->increment('emails_opened', 1, []);
        }

        // Create tracking record
        EmailTracking::query()->create([
            'email_recipient_id' => $result->email_recipient_id,
            'campaign_result_id' => $result->id,
            'event_type' => 'opened',
            'ip_address' => $this->getIpAddress(),
            'user_agent' => request()->header('User-Agent'),
            'country' => $this->getCountry(),
            'city' => $this->getCity(),
            'device' => $this->getDevice(),
            'browser' => $this->getBrowser(),
            'os' => $this->getOS(),
        ]);

        // Return 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Get client IP address
     */
    protected function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }

    /**
     * Get country from IP (simplified - would use a service like GeoIP in production)
     */
    protected function getCountry()
    {
        // In production, use a service like GeoIP2, MaxMind, or similar
        return 'Unknown';
    }

    /**
     * Get city from IP (simplified)
     */
    protected function getCity()
    {
        // In production, use a service like GeoIP2, MaxMind, or similar
        return 'Unknown';
    }

    /**
     * Get device type from User-Agent
     */
    protected function getDevice()
    {
        $userAgent = request()->header('User-Agent');

        if (preg_match('/Mobile|Android|iPhone|iPad|BlackBerry|IEMobile|Opera Mini/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Get browser from User-Agent
     */
    protected function getBrowser()
    {
        $userAgent = request()->header('User-Agent');

        if (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/Internet Explorer|MSIE/i', $userAgent)) {
            return 'Internet Explorer';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Get operating system from User-Agent
     */
    protected function getOS()
    {
        $userAgent = request()->header('User-Agent');

        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac OS/i', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iOS/i', $userAgent)) {
            return 'iOS';
        } else {
            return 'Unknown';
        }
    }
}
