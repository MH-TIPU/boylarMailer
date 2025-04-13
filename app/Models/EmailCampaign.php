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
        'template_id',
        'status',
        'sent_count',
        'open_count',
        'click_count',
        'bounce_count',
        'settings',
        'scheduled_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the template associated with the campaign
     */
    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
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
