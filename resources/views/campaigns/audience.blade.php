@extends('layouts.dashboard')

@section('title', 'Select Campaign Audience')

@section('dashboard-content')
<form action="{{ route('campaigns.audience.save', $campaign) }}" method="POST" id="audienceForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Audience Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Select Campaign Audience</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Campaign: {{ $campaign->name }}</h6>
                        <p class="text-muted">Choose which leads will receive this email campaign</p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="audience_type" id="allLeads" value="all" 
                                   {{ old('audience_type', $audienceType ?? 'all') == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label" for="allLeads">
                                All Active Leads
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="audience_type" id="segmentLeads" value="segment" 
                                   {{ old('audience_type', $audienceType ?? '') == 'segment' ? 'checked' : '' }}>
                            <label class="form-check-label" for="segmentLeads">
                                Segment
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="audience_type" id="specificLeads" value="specific" 
                                   {{ old('audience_type', $audienceType ?? '') == 'specific' ? 'checked' : '' }}>
                            <label class="form-check-label" for="specificLeads">
                                Select Specific Leads
                            </label>
                        </div>
                    </div>
                    
                    <!-- All Active Leads -->
                    <div id="allLeadsSection" class="audience-section mb-4 {{ old('audience_type', $audienceType ?? 'all') == 'all' ? '' : 'd-none' }}">
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-info-circle-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="alert-heading">All Active Leads</h6>
                                    <p class="mb-0">Campaign will be sent to all leads with an "active" status ({{ $activeLeadCount }} leads).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segment Selection -->
                    <div id="segmentSection" class="audience-section mb-4 {{ old('audience_type', $audienceType ?? '') == 'segment' ? '' : 'd-none' }}">
                        <div class="card border bg-light">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Define Segment Criteria</h6>
                            </div>
                            <div class="card-body">
                                <div class="segment-builder">
                                    <div class="segment-rules">
                                        <!-- Status -->
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="ms-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filter_status[]" value="active" id="statusActive" 
                                                           {{ in_array('active', old('filter_status', $filterStatus ?? ['active'])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="statusActive">Active</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filter_status[]" value="inactive" id="statusInactive" 
                                                           {{ in_array('inactive', old('filter_status', $filterStatus ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="statusInactive">Inactive</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Source -->
                                        <div class="mb-3">
                                            <label class="form-label">Source</label>
                                            <div class="ms-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filter_source[]" value="website" id="sourceWebsite" 
                                                           {{ in_array('website', old('filter_source', $filterSource ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sourceWebsite">Website</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filter_source[]" value="import" id="sourceImport" 
                                                           {{ in_array('import', old('filter_source', $filterSource ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sourceImport">Import</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filter_source[]" value="manual" id="sourceManual" 
                                                           {{ in_array('manual', old('filter_source', $filterSource ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sourceManual">Manual</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="filter_source[]" value="other" id="sourceOther" 
                                                           {{ in_array('other', old('filter_source', $filterSource ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sourceOther">Other</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Tags -->
                                        <div class="mb-3">
                                            <label for="filterTags" class="form-label">Tags</label>
                                            <input type="text" class="form-control" id="filterTags" name="filter_tags" 
                                                   value="{{ old('filter_tags', $filterTags ?? '') }}" placeholder="e.g., customer, vip, newsletter">
                                            <div class="form-text">Enter tags separated by commas. Leads with ANY of these tags will be included.</div>
                                        </div>
                                        
                                        <!-- Recent Campaigns -->
                                        <div class="mb-3">
                                            <label class="form-label">Campaign Activity</label>
                                            <div class="ms-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="campaign_filter[]" value="not_sent_any" id="notSentAny" 
                                                           {{ in_array('not_sent_any', old('campaign_filter', $campaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="notSentAny">Has not received any campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="campaign_filter[]" value="not_opened_any" id="notOpenedAny" 
                                                           {{ in_array('not_opened_any', old('campaign_filter', $campaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="notOpenedAny">Has not opened any campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="campaign_filter[]" value="opened_any" id="openedAny" 
                                                           {{ in_array('opened_any', old('campaign_filter', $campaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="openedAny">Has opened any campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="campaign_filter[]" value="clicked_any" id="clickedAny" 
                                                           {{ in_array('clicked_any', old('campaign_filter', $campaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="clickedAny">Has clicked on any campaign</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Specific Campaign -->
                                        <div class="mb-3">
                                            <label for="specificCampaign" class="form-label">Specific Campaign Interaction</label>
                                            <select class="form-select" id="specificCampaign" name="specific_campaign">
                                                <option value="">Select campaign...</option>
                                                @foreach($campaigns as $c)
                                                    <option value="{{ $c->id }}" {{ old('specific_campaign', $specificCampaign ?? '') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            <div class="mt-2 ms-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="specific_campaign_filter[]" value="received" id="receivedSpecific" 
                                                           {{ in_array('received', old('specific_campaign_filter', $specificCampaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="receivedSpecific">Received this campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="specific_campaign_filter[]" value="not_received" id="notReceivedSpecific" 
                                                           {{ in_array('not_received', old('specific_campaign_filter', $specificCampaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="notReceivedSpecific">Did not receive this campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="specific_campaign_filter[]" value="opened" id="openedSpecific" 
                                                           {{ in_array('opened', old('specific_campaign_filter', $specificCampaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="openedSpecific">Opened this campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="specific_campaign_filter[]" value="not_opened" id="notOpenedSpecific" 
                                                           {{ in_array('not_opened', old('specific_campaign_filter', $specificCampaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="notOpenedSpecific">Did not open this campaign</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="specific_campaign_filter[]" value="clicked" id="clickedSpecific" 
                                                           {{ in_array('clicked', old('specific_campaign_filter', $specificCampaignFilter ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="clickedSpecific">Clicked on this campaign</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary" id="previewSegment">
                                        <i class="bi bi-eye"></i> Preview Results
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="segmentPreviewContainer" class="mt-3 d-none">
                            <div class="card border">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Segment Preview</h6>
                                    <span class="badge bg-primary" id="segmentCount">0 leads</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                    <th>Source</th>
                                                </tr>
                                            </thead>
                                            <tbody id="segmentPreviewTable">
                                                <!-- Preview results will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Specific Leads Selection -->
                    <div id="specificLeadsSection" class="audience-section mb-4 {{ old('audience_type', $audienceType ?? '') == 'specific' ? '' : 'd-none' }}">
                        <div class="card border bg-light">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Select Specific Leads</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="leadSearch" class="form-label">Search Leads</label>
                                    <input type="text" class="form-control" id="leadSearch" placeholder="Search by name, email or company">
                                </div>
                                
                                <div class="lead-selection-area">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="selectAllLeads">
                                                            <label class="form-check-label" for="selectAllLeads"></label>
                                                        </div>
                                                    </th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Company</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="leadsTable">
                                                @foreach($leads as $lead)
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input lead-checkbox" type="checkbox" 
                                                                       name="selected_leads[]" value="{{ $lead->id }}" id="lead{{ $lead->id }}"
                                                                       {{ in_array($lead->id, old('selected_leads', $selectedLeads ?? [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lead{{ $lead->id }}"></label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
                                                        <td>{{ $lead->email }}</td>
                                                        <td>{{ $lead->company ?? '-' }}</td>
                                                        <td>
                                                            <span class="badge {{ $lead->status == 'active' ? 'bg-success' : ($lead->status == 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                                                {{ ucfirst($lead->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <span id="selectedLeadsCount">0</span> leads selected
                                        </div>
                                        <div class="pagination-container">
                                            {{ $leads->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Summary & Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Campaign Summary</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $campaign->name }}</h6>
                    <p class="text-muted">{{ $campaign->subject }}</p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="estimated_recipients" class="form-label">Estimated Recipients</label>
                        <input type="text" class="form-control-plaintext" id="estimated_recipients" value="{{ $activeLeadCount }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exclusions" class="form-label">Exclude Previous Recipients</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="excludeAll" name="exclude_previous" value="1"
                                   {{ old('exclude_previous', $excludePrevious ?? '') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="excludeAll">
                                Exclude leads who have received any campaign in the last 7 days
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Schedule -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Campaign Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="schedule_type" id="sendNow" value="now" 
                                   {{ old('schedule_type', $scheduleType ?? 'now') == 'now' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sendNow">
                                Send Immediately
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="schedule_type" id="scheduleFor" value="scheduled" 
                                   {{ old('schedule_type', $scheduleType ?? '') == 'scheduled' ? 'checked' : '' }}>
                            <label class="form-check-label" for="scheduleFor">
                                Schedule for Later
                            </label>
                        </div>
                    </div>
                    
                    <div id="scheduleSection" class="{{ old('schedule_type', $scheduleType ?? 'now') == 'scheduled' ? '' : 'd-none' }}">
                        <div class="mb-3">
                            <label for="scheduled_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" 
                                   value="{{ old('scheduled_date', $scheduledDate ?? date('Y-m-d', strtotime('+1 day'))) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="scheduled_time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" 
                                   value="{{ old('scheduled_time', $scheduledTime ?? '09:00') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="UTC" {{ old('timezone', $timezone ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ old('timezone', $timezone ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                                <option value="America/Chicago" {{ old('timezone', $timezone ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                                <option value="America/Denver" {{ old('timezone', $timezone ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $timezone ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                                <option value="Europe/London" {{ old('timezone', $timezone ?? '') == 'Europe/London' ? 'selected' : '' }}>London (GMT)</option>
                                <option value="Europe/Paris" {{ old('timezone', $timezone ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Central European Time (CET)</option>
                                <option value="Asia/Tokyo" {{ old('timezone', $timezone ?? '') == 'Asia/Tokyo' ? 'selected' : '' }}>Japan (JST)</option>
                                <option value="Australia/Sydney" {{ old('timezone', $timezone ?? '') == 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEST)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Campaign Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" name="save" value="1" class="btn btn-primary">
                            <i class="bi bi-check2-circle"></i> Save Audience & Schedule
                        </button>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Audience type selection
        const audienceTypeRadios = document.querySelectorAll('input[name="audience_type"]');
        const audienceSections = document.querySelectorAll('.audience-section');
        const estimatedRecipientsInput = document.getElementById('estimated_recipients');
        
        // Update visible section based on audience type
        function updateAudienceSection() {
            const selectedType = document.querySelector('input[name="audience_type"]:checked').value;
            
            audienceSections.forEach(section => {
                section.classList.add('d-none');
            });
            
            document.getElementById(selectedType + 'LeadsSection')?.classList.remove('d-none');
            document.getElementById(selectedType + 'Section')?.classList.remove('d-none');
            
            // Update recipient count estimate
            if (selectedType === 'all') {
                estimatedRecipientsInput.value = '{{ $activeLeadCount }}';
            } else if (selectedType === 'specific') {
                updateSelectedLeadsCount();
            }
        }
        
        audienceTypeRadios.forEach(radio => {
            radio.addEventListener('change', updateAudienceSection);
        });
        
        // Schedule section toggle
        const scheduleTypeRadios = document.querySelectorAll('input[name="schedule_type"]');
        const scheduleSection = document.getElementById('scheduleSection');
        
        scheduleTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'scheduled') {
                    scheduleSection.classList.remove('d-none');
                } else {
                    scheduleSection.classList.add('d-none');
                }
            });
        });
        
        // Lead search functionality
        const leadSearch = document.getElementById('leadSearch');
        const leadsRows = document.querySelectorAll('#leadsTable tr');
        
        leadSearch?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            leadsRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Select all leads checkbox
        const selectAllLeads = document.getElementById('selectAllLeads');
        const leadCheckboxes = document.querySelectorAll('.lead-checkbox');
        
        selectAllLeads?.addEventListener('change', function() {
            const isChecked = this.checked;
            
            leadCheckboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = isChecked;
                }
            });
            
            updateSelectedLeadsCount();
        });
        
        // Update selected leads count
        function updateSelectedLeadsCount() {
            const selectedLeadsCount = document.getElementById('selectedLeadsCount');
            const checkedLeads = document.querySelectorAll('.lead-checkbox:checked').length;
            
            if (selectedLeadsCount) {
                selectedLeadsCount.textContent = checkedLeads;
                estimatedRecipientsInput.value = checkedLeads;
            }
        }
        
        leadCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedLeadsCount);
        });
        
        // Preview segment
        const previewSegmentBtn = document.getElementById('previewSegment');
        const segmentPreviewContainer = document.getElementById('segmentPreviewContainer');
        const segmentPreviewTable = document.getElementById('segmentPreviewTable');
        const segmentCount = document.getElementById('segmentCount');
        
        previewSegmentBtn?.addEventListener('click', function() {
            // Get all segment form data
            const formData = new FormData(document.getElementById('audienceForm'));
            formData.append('preview', '1');
            
            // Send AJAX request to preview segment
            fetch('{{ route("campaigns.audience.preview", $campaign) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show preview container
                    segmentPreviewContainer.classList.remove('d-none');
                    
                    // Update count
                    segmentCount.textContent = data.count + ' leads';
                    estimatedRecipientsInput.value = data.count;
                    
                    // Clear table
                    segmentPreviewTable.innerHTML = '';
                    
                    // Add preview rows (limit to first 10)
                    const previewLeads = data.leads.slice(0, 10);
                    
                    if (previewLeads.length > 0) {
                        previewLeads.forEach(lead => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${lead.first_name} ${lead.last_name}</td>
                                <td>${lead.email}</td>
                                <td><span class="badge ${lead.status === 'active' ? 'bg-success' : (lead.status === 'inactive' ? 'bg-secondary' : 'bg-danger')}">${lead.status}</span></td>
                                <td>${lead.source || 'manual'}</td>
                            `;
                            segmentPreviewTable.appendChild(row);
                        });
                        
                        if (data.count > 10) {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td colspan="4" class="text-center">
                                    <em>Showing 10 of ${data.count} leads</em>
                                </td>
                            `;
                            segmentPreviewTable.appendChild(row);
                        }
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td colspan="4" class="text-center">
                                <em>No leads match the selected criteria</em>
                            </td>
                        `;
                        segmentPreviewTable.appendChild(row);
                    }
                } else {
                    alert('Error previewing segment: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while previewing the segment.');
            });
        });
        
        // Initialize selections
        updateAudienceSection();
        updateSelectedLeadsCount();
    });
</script>
@endsection