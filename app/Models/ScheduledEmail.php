<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_campaign_id',
        'lead_id',
        'status',
        'scheduled_at',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
    ];

    /**
     * Get the campaign this scheduled email belongs to
     */
    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'email_campaign_id');
    }

    /**
     * Get the lead this scheduled email is for
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
