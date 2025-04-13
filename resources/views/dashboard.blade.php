@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('dashboard-content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Leads</h5>
                <p class="card-text display-4">{{ \App\Models\Lead::count() }}</p>
                <a href="{{ route('leads.index') }}" class="text-white">View all leads →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Active Campaigns</h5>
                <p class="card-text display-4">{{ \App\Models\EmailCampaign::whereIn('status', ['scheduled', 'sending'])->count() }}</p>
                <a href="{{ route('campaigns.index') }}" class="text-white">View all campaigns →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Email Templates</h5>
                <p class="card-text display-4">{{ \App\Models\EmailTemplate::count() }}</p>
                <a href="{{ route('templates.index') }}" class="text-white">View all templates →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Emails Sent</h5>
                <p class="card-text display-4">{{ \App\Models\ScheduledEmail::where('status', 'sent')->count() }}</p>
                <span class="text-white">In the last 30 days</span>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Recent Campaigns
            </div>
            <div class="card-body">
                @if($recentCampaigns->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Sent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCampaigns as $campaign)
                                    <tr>
                                        <td>{{ $campaign->name }}</td>
                                        <td>
                                            @if($campaign->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($campaign->status == 'scheduled')
                                                <span class="badge bg-primary">Scheduled</span>
                                            @elseif($campaign->status == 'sending')
                                                <span class="badge bg-info">Sending</span>
                                            @elseif($campaign->status == 'sent')
                                                <span class="badge bg-success">Sent</span>
                                            @elseif($campaign->status == 'paused')
                                                <span class="badge bg-warning">Paused</span>
                                            @else
                                                <span class="badge bg-danger">Canceled</span>
                                            @endif
                                        </td>
                                        <td>{{ $campaign->sent_count }}</td>
                                        <td>
                                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center my-3">No campaigns yet. <a href="{{ route('campaigns.create') }}">Create your first campaign</a></p>
                @endif
            </div>
            <div class="card-footer text-muted">
                <a href="{{ route('campaigns.index') }}">View all campaigns →</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Recent Leads
            </div>
            <div class="card-body">
                @if($recentLeads->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLeads as $lead)
                                    <tr>
                                        <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
                                        <td>{{ $lead->email }}</td>
                                        <td>
                                            @if($lead->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($lead->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center my-3">No leads yet. <a href="{{ route('leads.create') }}">Add your first lead</a> or <a href="{{ route('leads.import.form') }}">Import leads</a></p>
                @endif
            </div>
            <div class="card-footer text-muted">
                <a href="{{ route('leads.index') }}">View all leads →</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Quick Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">Create Campaign</a>
                    <a href="{{ route('templates.builder') }}" class="btn btn-secondary">Design Email Template</a>
                    <a href="{{ route('leads.import.form') }}" class="btn btn-success">Import Leads</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Email Performance
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h3>{{ number_format(\App\Models\EmailCampaign::sum('open_count') / max(1, \App\Models\EmailCampaign::sum('sent_count')) * 100, 1) }}%</h3>
                        <p class="text-muted">Open Rate</p>
                    </div>
                    <div class="col">
                        <h3>{{ number_format(\App\Models\EmailCampaign::sum('click_count') / max(1, \App\Models\EmailCampaign::sum('open_count')) * 100, 1) }}%</h3>
                        <p class="text-muted">Click Rate</p>
                    </div>
                    <div class="col">
                        <h3>{{ number_format(\App\Models\EmailCampaign::sum('bounce_count') / max(1, \App\Models\EmailCampaign::sum('sent_count')) * 100, 1) }}%</h3>
                        <p class="text-muted">Bounce Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection