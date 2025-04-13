@extends('layouts.dashboard')

@section('title', 'Email Campaigns')

@section('dashboard-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Email Campaigns</h1>
    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Campaign
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('campaigns.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter_status" class="form-label">Status</label>
                <select class="form-select" id="filter_status" name="status">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_date" class="form-label">Date Range</label>
                <select class="form-select" id="filter_date" name="date_range">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last7days" {{ request('date_range') == 'last7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last30days" {{ request('date_range') == 'last30days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="thismonth" {{ request('date_range') == 'thismonth' ? 'selected' : '' }}>This Month</option>
                    <option value="lastmonth" {{ request('date_range') == 'lastmonth' ? 'selected' : '' }}>Last Month</option>
                    <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-md-4" id="customDateRange" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                <div class="row g-2">
                    <div class="col-6">
                        <label for="date_from" class="form-label">From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6">
                        <label for="date_to" class="form-label">To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Campaign Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <h5 class="card-title">Total Campaigns</h5>
                <p class="display-4 fw-bold mb-0">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <h5 class="card-title">Total Sent</h5>
                <p class="display-4 fw-bold mb-0">{{ $stats['sent'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <h5 class="card-title">Avg. Open Rate</h5>
                <p class="display-4 fw-bold mb-0">{{ $stats['open_rate'] }}%</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <h5 class="card-title">Avg. Click Rate</h5>
                <p class="display-4 fw-bold mb-0">{{ $stats['click_rate'] }}%</p>
            </div>
        </div>
    </div>
</div>

<!-- Campaigns Table -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Recipients</th>
                        <th>Open Rate</th>
                        <th>Click Rate</th>
                        <th>Scheduled/Sent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="fw-bold text-decoration-none">
                                {{ $campaign->name }}
                            </a>
                        </td>
                        <td>{{ Str::limit($campaign->subject, 40) }}</td>
                        <td>
                            @if($campaign->status == 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @elseif($campaign->status == 'scheduled')
                                <span class="badge bg-info">Scheduled</span>
                            @elseif($campaign->status == 'sending')
                                <span class="badge bg-warning">Sending</span>
                            @elseif($campaign->status == 'sent')
                                <span class="badge bg-success">Sent</span>
                            @elseif($campaign->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $campaign->recipient_count }}</td>
                        <td>
                            @if($campaign->status == 'sent' || $campaign->status == 'sending')
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                            style="width: {{ $campaign->open_rate }}%;" 
                                            aria-valuenow="{{ $campaign->open_rate }}" 
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="small">
                                        {{ $campaign->open_rate }}%
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($campaign->status == 'sent' || $campaign->status == 'sending')
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                            style="width: {{ $campaign->click_rate }}%;" 
                                            aria-valuenow="{{ $campaign->click_rate }}" 
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="small">
                                        {{ $campaign->click_rate }}%
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($campaign->status == 'scheduled')
                                {{ $campaign->scheduled_at->format('M d, Y g:i A') }}
                            @elseif($campaign->status == 'sending' || $campaign->status == 'sent')
                                {{ $campaign->sent_at->format('M d, Y g:i A') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="campaign{{ $campaign->id }}Actions" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="campaign{{ $campaign->id }}Actions">
                                    <li><a class="dropdown-item" href="{{ route('campaigns.show', $campaign) }}">View Report</a></li>
                                    
                                    @if($campaign->status == 'draft')
                                        <li><a class="dropdown-item" href="{{ route('campaigns.edit', $campaign) }}">Edit</a></li>
                                        <li><a class="dropdown-item" href="{{ route('campaigns.audience', $campaign) }}">Select Audience</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this campaign?')">Delete</button>
                                            </form>
                                        </li>
                                    @endif
                                    
                                    @if($campaign->status == 'scheduled')
                                        <li><a class="dropdown-item" href="{{ route('campaigns.edit', $campaign) }}">Edit</a></li>
                                        <li><a class="dropdown-item" href="{{ route('campaigns.audience', $campaign) }}">Edit Audience</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('campaigns.cancel', $campaign) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to cancel this scheduled campaign?')">Cancel Schedule</button>
                                            </form>
                                        </li>
                                    @endif
                                    
                                    @if($campaign->status == 'sent')
                                        <li><a class="dropdown-item" href="{{ route('campaigns.duplicate', $campaign) }}">Duplicate</a></li>
                                    @endif
                                    
                                    @if($campaign->status == 'sending')
                                        <li>
                                            <form action="{{ route('campaigns.stop', $campaign) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to stop sending this campaign?')">Stop Sending</button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-envelope-paper text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">No campaigns found</h5>
                                <p class="text-muted">Get started by creating your first email campaign</p>
                                <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create Campaign
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateRangeSelect = document.getElementById('filter_date');
        const customDateRange = document.getElementById('customDateRange');
        
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = '';
            } else {
                customDateRange.style.display = 'none';
            }
        });
    });
</script>
@endsection