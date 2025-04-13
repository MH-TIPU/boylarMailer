@extends('layouts.dashboard')

@section('title', 'Leads')

@section('actions')
    <a href="{{ route('leads.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus"></i> Add Lead
    </a>
    <a href="{{ route('leads.import.form') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-upload"></i> Import Leads
    </a>
    <a href="{{ route('leads.export') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-download"></i> Export
    </a>
</section>

@section('dashboard-content')
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('leads.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search name or email" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="source" class="form-select">
                        <option value="">All Sources</option>
                        <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                        <option value="import" {{ request('source') == 'import' ? 'selected' : '' }}>Imported</option>
                        <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if($leads->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input select-all" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input lead-select" type="checkbox" name="leads[]" value="{{ $lead->id }}" id="lead{{ $lead->id }}">
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
                                    <td>{{ ucfirst($lead->source ?? 'manual') }}</td>
                                    <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $lead->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $lead->id }}">
                                                <li><a class="dropdown-item" href="{{ route('leads.show', $lead) }}">View Details</a></li>
                                                <li><a class="dropdown-item" href="{{ route('leads.edit', $lead) }}">Edit</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} leads</span>
                    </div>
                    <div>
                        {{ $leads->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <h3>No Leads Found</h3>
            <p class="text-muted">Start by adding leads to your account or importing them from a CSV file.</p>
            <div class="mt-4">
                <a href="{{ route('leads.create') }}" class="btn btn-primary">Add Lead</a>
                <a href="{{ route('leads.import.form') }}" class="btn btn-secondary">Import Leads</a>
            </div>
        </div>
    @endif

    <form id="bulkActionsForm" action="{{ route('leads.index') }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="selected_leads" id="selectedLeads" value="">
        <div class="card">
            <div class="card-header">
                Bulk Actions
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-auto">
                        <select name="bulk_action" class="form-select" id="bulkAction">
                            <option value="">Select Action</option>
                            <option value="active">Mark as Active</option>
                            <option value="inactive">Mark as Inactive</option>
                            <option value="unsubscribed">Mark as Unsubscribed</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" id="applyBulkAction" disabled>Apply</button>
                    </div>
                    <div class="col-auto">
                        <span class="text-muted" id="selectedCount">0 leads selected</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.querySelector('.select-all');
        const leadSelects = document.querySelectorAll('.lead-select');
        const selectedCountElem = document.getElementById('selectedCount');
        const selectedLeadsInput = document.getElementById('selectedLeads');
        const applyBtn = document.getElementById('applyBulkAction');
        const bulkActionSelect = document.getElementById('bulkAction');
        const bulkForm = document.getElementById('bulkActionsForm');
        
        // Select all checkbox
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            
            leadSelects.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateSelectedCount();
        });
        
        // Individual checkboxes
        leadSelects.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                
                // Check if all checkboxes are selected
                const allChecked = Array.from(leadSelects).every(cb => cb.checked);
                selectAll.checked = allChecked;
            });
        });
        
        // Update selected count and button state
        function updateSelectedCount() {
            const selected = Array.from(leadSelects).filter(cb => cb.checked);
            const count = selected.length;
            const ids = selected.map(cb => cb.value);
            
            selectedCountElem.textContent = count + ' leads selected';
            selectedLeadsInput.value = ids.join(',');
            applyBtn.disabled = count === 0 || bulkActionSelect.value === '';
        }
        
        // Bulk action select change
        bulkActionSelect.addEventListener('change', function() {
            const selected = Array.from(leadSelects).filter(cb => cb.checked);
            applyBtn.disabled = selected.length === 0 || this.value === '';
        });
        
        // Apply bulk action
        applyBtn.addEventListener('click', function() {
            const action = bulkActionSelect.value;
            const selected = Array.from(leadSelects).filter(cb => cb.checked);
            
            if (selected.length === 0) return;
            
            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete these ' + selected.length + ' leads? This action cannot be undone.')) {
                    return;
                }
            }
            
            bulkForm.action = "{{ route('leads.bulk-action') }}";
            bulkForm.submit();
        });
    });
</script>
@endsection