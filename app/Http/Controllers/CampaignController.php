<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\ScheduledEmail;
use App\Models\SubscriberList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns.
     */
    public function index()
    {
        $campaigns = EmailCampaign::where('user_id', Auth::id())
            ->with(['template', 'subscriberList'])
            ->latest()
            ->get();

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create()
    {
        $templates = EmailTemplate::where('user_id', Auth::id())->get();
        $lists = SubscriberList::where('user_id', Auth::id())->get();

        return Inertia::render('Campaigns/Create', [
            'templates' => $templates,
            'lists' => $lists,
        ]);
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:email_templates,id',
            'subscriber_list_id' => 'required|exists:subscriber_lists,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = EmailCampaign::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'template_id' => $validated['template_id'],
            'subscriber_list_id' => $validated['subscriber_list_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
        ]);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified campaign.
     */
    public function show(EmailCampaign $campaign)
    {
        $this->authorize('view', $campaign);

        $campaign->load(['template', 'subscriberList']);

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        if ($campaign->status !== 'draft') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Only draft campaigns can be edited.');
        }

        $templates = EmailTemplate::where('user_id', Auth::id())->get();
        $lists = SubscriberList::where('user_id', Auth::id())->get();

        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'templates' => $templates,
            'lists' => $lists,
        ]);
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(Request $request, EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        if ($campaign->status !== 'draft') {
            return response()->json(['message' => 'Only draft campaigns can be updated.'], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:email_templates,id',
            'subscriber_list_id' => 'required|exists:subscriber_lists,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'template_id' => $validated['template_id'],
            'subscriber_list_id' => $validated['subscriber_list_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
        ]);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(EmailCampaign $campaign)
    {
        $this->authorize('delete', $campaign);

        if ($campaign->status !== 'draft') {
            return response()->json(['message' => 'Only draft campaigns can be deleted.'], 422);
        }

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
        $this->authorize('update', $campaign);

        if (!in_array($campaign->status, ['scheduled', 'sending'])) {
            return response()->json(['message' => 'Only scheduled or sending campaigns can be cancelled.'], 422);
        }

        $campaign->update([
            'status' => 'draft',
            'scheduled_at' => null,
        ]);

        return response()->json(['message' => 'Campaign cancelled successfully.']);
    }

    /**
     * Send emails for the specified campaign.
     */
    public function send(EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        if ($campaign->status !== 'draft') {
            return response()->json(['message' => 'Only draft campaigns can be sent.'], 422);
        }

        // TODO: Implement email sending logic
        $campaign->update([
            'status' => 'sending',
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Campaign is being sent.']);
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

    public function schedule(Request $request, EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        if ($campaign->status !== 'draft') {
            return response()->json(['message' => 'Only draft campaigns can be scheduled.'], 422);
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $campaign->update([
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'scheduled',
        ]);

        return response()->json(['message' => 'Campaign scheduled successfully.']);
    }
}
