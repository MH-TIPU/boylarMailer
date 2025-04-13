@extends('layouts.dashboard')

@section('title', 'Lead Details')

@section('actions')
    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil"></i> Edit Lead
    </a>
    <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this lead?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Lead Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">First Name</p>
                        <p class="mb-0 fw-bold">{{ $lead->first_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Last Name</p>
                        <p class="mb-0 fw-bold">{{ $lead->last_name }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Email</p>
                        <p class="mb-0 fw-bold">{{ $lead->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Phone</p>
                        <p class="mb-0 fw-bold">{{ $lead->phone ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Company</p>
                        <p class="mb-0 fw-bold">{{ $lead->company ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Status</p>
                        <p class="mb-0">
                            <span class="badge {{ $lead->status == 'active' ? 'bg-success' : ($lead->status == 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Source</p>
                        <p class="mb-0 fw-bold">{{ ucfirst($lead->source ?? 'Manual') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Created</p>
                        <p class="mb-0 fw-bold">{{ $lead->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1 text-muted">Tags</p>
                        <div>
                            @if(!empty($lead->tags) && is_array($lead->tags))
                                @foreach($lead->tags as $tag)
                                    <span class="badge bg-primary me-1">{{ $tag }}</span>
                                @endforeach
                            @else
                                <p class="mb-0">No tags</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if(!empty($lead->custom_fields) && is_array($lead->custom_fields))
                    <hr>
                    <h6>Custom Fields</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Field</th>
                                    <th scope="col">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lead->custom_fields as $key => $value)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Email Activity</h5>
            </div>
            <div class="card-body">
                @php
                    $scheduledEmails = \App\Models\ScheduledEmail::where('lead_id', $lead->id)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($scheduledEmails->count() > 0)
                    <div class="list-group">
                        @foreach($scheduledEmails as $email)
                            <a href="{{ route('campaigns.show', $email->email_campaign_id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $email->campaign->name ?? 'Unknown Campaign' }}</h6>
                                    <small>{{ $email->created_at->format('M d') }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge {{ $email->status == 'sent' ? 'bg-success' : ($email->status == 'scheduled' ? 'bg-primary' : ($email->status == 'opened' ? 'bg-info' : ($email->status == 'clicked' ? 'bg-warning' : 'bg-secondary'))) }}">
                                        {{ ucfirst($email->status) }}
                                    </span>
                                </p>
                                <small>
                                    @if($email->status == 'scheduled')
                                        Scheduled for {{ $email->scheduled_at->format('M d, Y \a\t h:i A') }}
                                    @elseif($email->status == 'sent')
                                        Sent on {{ $email->sent_at->format('M d, Y \a\t h:i A') }}
                                    @elseif($email->status == 'opened')
                                        Opened on {{ $email->opened_at->format('M d, Y \a\t h:i A') }}
                                    @elseif($email->status == 'clicked')
                                        Clicked on {{ $email->clicked_at->format('M d, Y \a\t h:i A') }}
                                    @else
                                        {{ ucfirst($email->status) }}
                                    @endif
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-3">No email activity found</p>
                @endif
            </div>
            
            @if($scheduledEmails->count() > 0)
                <div class="card-footer">
                    <a href="#" class="link-primary">View All Activity</a>
                </div>
            @endif
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('campaigns.create') }}?lead_id={{ $lead->id }}" class="btn btn-primary">
                        <i class="bi bi-envelope"></i> Send Email
                    </a>
                    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit Lead
                    </a>
                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> Delete Lead
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection