<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignEmail;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'template_id',
        'subscriber_list_id',
        'user_id',
        'status',
        'scheduled_at',
        'sent_at',
        'stats',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'stats' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function subscriberList()
    {
        return $this->belongsTo(SubscriberList::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'warning',
            'scheduled' => 'info',
            'sending' => 'primary',
            'sent' => 'success',
            'failed' => 'error',
            default => 'default',
        };
    }

    /**
     * Get the scheduled emails for this campaign
     */
    public function scheduledEmails(): HasMany
    {
        return $this->hasMany(ScheduledEmail::class);
    }

    /**
     * Send emails for the campaign
     */
    public function sendEmails()
    {
        $template = $this->template;
        $leads = Lead::all(); // Fetch all leads (can be filtered based on campaign settings)

        foreach ($leads as $lead) {
            Mail::to($lead->email)->queue(new CampaignEmail($template, $this, $lead));
        }

        $this->update(['status' => 'sent']);
    }
}
