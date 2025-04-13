<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use Illuminate\Support\Str;
use App\Models\Lead;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $campaign;
    public $lead;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailTemplate $template, EmailCampaign $campaign, Lead $lead)
    {
        $this->template = $template;
        $this->campaign = $campaign;
        $this->lead = $lead;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Campaign Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'template' => $this->template,
                'campaign' => $this->campaign,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $content = $this->replaceMergeTags($this->template->content, $this->lead);

        return $this->view('emails.campaign')
                    ->with(['content' => $content])
                    ->subject($this->campaign->subject);
    }

    private function replaceMergeTags($content, $lead)
    {
        $tags = [
            '{{first_name}}' => $lead->first_name,
            '{{last_name}}' => $lead->last_name,
            '{{email}}' => $lead->email,
            '{{company}}' => $lead->company,
            '{{unsubscribe_link}}' => route('unsubscribe', ['email' => $lead->email]),
        ];

        return strtr($content, $tags);
    }
}
