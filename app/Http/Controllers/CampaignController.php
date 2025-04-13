<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\ScheduledEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns.
     */
    public function index()
    {
        $campaigns = EmailCampaign::paginate(10);

        $stats = [
            'total' => EmailCampaign::count(),
            'sent' => EmailCampaign::where('status', 'sent')->count(),
            'open_rate' => EmailCampaign::avg('open_rate'),
            'click_rate' => EmailCampaign::avg('click_rate'),
        ];

        return view('campaigns.index', compact('campaigns', 'stats'));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create()
    {
        $templates = EmailTemplate::all();
        return view('campaigns.create', compact('templates'));
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(Request $request)
    {
        Log::info('Campaign store request received', ['save_type' => $request->input('save_type')]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'preview_text' => 'nullable|string|max:150',
            'content' => 'required|string',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to' => 'nullable|email|max:255',
            'track_opens' => 'nullable|boolean',
            'track_clicks' => 'nullable|boolean',
        ]);

        Log::error('Validation failed', ['errors' => $validated]);

        $saveType = $request->input('save_type', 'draft');
        $status = $saveType === 'draft' ? 'draft' : 'pending';

        Log::info('Attempting to create campaign', ['data' => $validated]);

        Log::info('Store method called', ['request' => $request->all()]);

        try {
            $campaign = EmailCampaign::create([
                'name' => $validated['name'],
                'subject' => $validated['subject'],
                'preview_text' => $validated['preview_text'] ?? null,
                'content' => $validated['content'],
                'from_name' => $validated['from_name'],
                'from_email' => $validated['from_email'],
                'reply_to' => $validated['reply_to'] ?? null,
                'track_opens' => $validated['track_opens'] ?? false,
                'track_clicks' => $validated['track_clicks'] ?? false,
                'status' => $status,
            ]);

            Log::info('Campaign saved successfully', ['campaign_id' => $campaign->id]);
        } catch (\Exception $e) {
            Log::error('Error saving campaign', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save campaign. Please try again.');
        }

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified campaign.
     */
    public function show(EmailCampaign $campaign)
    {
        $scheduledEmails = $campaign->scheduledEmails()->paginate(10);
        $stats = [
            'sent' => $campaign->sent_count,
            'opened' => $campaign->open_count,
            'clicked' => $campaign->click_count,
            'bounced' => $campaign->bounce_count,
        ];
        
        return view('campaigns.show', compact('campaign', 'scheduledEmails', 'stats'));
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(EmailCampaign $campaign)
    {
        $templates = EmailTemplate::all();
        return view('campaigns.edit', compact('campaign', 'templates'));
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(Request $request, EmailCampaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'template_id' => 'required|exists:email_templates,id',
            'scheduled_at' => 'nullable|date',
            'settings' => 'nullable|array',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(EmailCampaign $campaign)
    {
        $campaign->scheduledEmails()->delete();
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Show the audience selection form.
     */
    public function selectAudience(EmailCampaign $campaign)
    {
        // Get lead counts by status
        $leadsByStatus = Lead::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        // Get lead counts by source
        $leadsBySource = Lead::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get();
            
        return view('campaigns.select-audience', compact('campaign', 'leadsByStatus', 'leadsBySource'));
    }

    /**
     * Schedule the campaign for the selected audience.
     */
    public function scheduleForAudience(Request $request, EmailCampaign $campaign)
    {
        $validated = $request->validate([
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'exists:leads,id',
            'status_filter' => 'nullable|array',
            'source_filter' => 'nullable|array',
            'scheduled_at' => 'required|date',
        ]);

        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Get leads based on filters
            $query = Lead::query();
            
            // Apply specific lead IDs if provided
            if (!empty($validated['lead_ids'])) {
                $query->whereIn('id', $validated['lead_ids']);
            }
            
            // Apply status filter if provided
            if (!empty($validated['status_filter'])) {
                $query->whereIn('status', $validated['status_filter']);
            }
            
            // Apply source filter if provided
            if (!empty($validated['source_filter'])) {
                $query->whereIn('source', $validated['source_filter']);
            }
            
            $leads = $query->get();
            
            // Update campaign scheduled time
            $campaign->scheduled_at = $validated['scheduled_at'];
            $campaign->status = 'scheduled';
            $campaign->save();
            
            // Create scheduled emails for each lead
            foreach ($leads as $lead) {
                ScheduledEmail::create([
                    'email_campaign_id' => $campaign->id,
                    'lead_id' => $lead->id,
                    'status' => 'scheduled',
                    'scheduled_at' => $validated['scheduled_at'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('campaigns.show', $campaign)
                ->with('success', 'Campaign scheduled for ' . $leads->count() . ' leads.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to schedule campaign: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Pause a scheduled campaign.
     */
    public function pause(EmailCampaign $campaign)
    {
        if ($campaign->status == 'scheduled' || $campaign->status == 'sending') {
            $campaign->status = 'paused';
            $campaign->save();
            
            return redirect()->route('campaigns.show', $campaign)
                ->with('success', 'Campaign paused successfully.');
        }
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('error', 'Campaign cannot be paused in its current state.');
    }

    /**
     * Resume a paused campaign.
     */
    public function resume(EmailCampaign $campaign)
    {
        if ($campaign->status == 'paused') {
            $campaign->status = $campaign->scheduled_at > now() ? 'scheduled' : 'sending';
            $campaign->save();
            
            return redirect()->route('campaigns.show', $campaign)
                ->with('success', 'Campaign resumed successfully.');
        }
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('error', 'Campaign cannot be resumed in its current state.');
    }

    /**
     * Cancel a campaign.
     */
    public function cancel(EmailCampaign $campaign)
    {
        if (in_array($campaign->status, ['scheduled', 'sending', 'paused'])) {
            $campaign->status = 'canceled';
            $campaign->save();
            
            // Cancel all pending scheduled emails
            $campaign->scheduledEmails()
                ->whereIn('status', ['scheduled'])
                ->update(['status' => 'failed', 'error_message' => 'Campaign canceled']);
                
            return redirect()->route('campaigns.show', $campaign)
                ->with('success', 'Campaign canceled successfully.');
        }
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('error', 'Campaign cannot be canceled in its current state.');
    }

    /**
     * Send emails for the specified campaign.
     */
    public function send(EmailCampaign $campaign)
    {
        if ($campaign->status !== 'scheduled') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Only scheduled campaigns can be sent.');
        }

        try {
            $campaign->sendEmails();
            return redirect()->route('campaigns.show', $campaign)
                ->with('success', 'Emails sent successfully.');
        } catch (\Exception $e) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Failed to send emails: ' . $e->getMessage());
        }
    }

    /**
     * Track email open event.
     */
    public function trackOpen(EmailCampaign $campaign)
    {
        $campaign->increment('open_count');

        // Return a 1x1 transparent pixel
        $response = response()->make(base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='));
        $response->header('Content-Type', 'image/gif');

        return $response;
    }

    /**
     * Track email click event.
     */
    public function trackClick(Request $request, EmailCampaign $campaign)
    {
        $url = $request->query('url');

        if ($url) {
            $campaign->increment('click_count');
            return redirect(urldecode($url));
        }

        return redirect('/');
    }

    /**
     * Show the form for editing the schedule of a campaign.
     */
    public function editSchedule(EmailCampaign $campaign)
    {
        return view('campaigns.edit-schedule', compact('campaign'));
    }
}
