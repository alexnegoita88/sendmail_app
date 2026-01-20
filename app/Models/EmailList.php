<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'file_type',
        'total_emails',
        'valid_emails',
        'invalid_emails',
        'invalid_emails_details',
        'status',
        'error_message',
        'user_id',
    ];

    protected $casts = [
        'invalid_emails_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function emailRecipients(): HasMany
    {
        return $this->hasMany(EmailRecipient::class);
    }
}
