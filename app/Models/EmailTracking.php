<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_recipient_id',
        'campaign_result_id',
        'event_type',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'device',
        'browser',
        'os',
    ];

    public function emailRecipient(): BelongsTo
    {
        return $this->belongsTo(EmailRecipient::class);
    }

    public function campaignResult(): BelongsTo
    {
        return $this->belongsTo(CampaignResult::class);
    }
}
