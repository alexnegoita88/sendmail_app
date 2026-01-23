<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_list_id',
        'email',
        'name',
    ];

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(EmailList::class);
    }

    public function campaignResults(): HasMany
    {
        return $this->hasMany(CampaignResult::class, 'email_recipient_id');
    }

    public function emailTrackings(): HasMany
    {
        return $this->hasMany(EmailTracking::class);
    }
}
