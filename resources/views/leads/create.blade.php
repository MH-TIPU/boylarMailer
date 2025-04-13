@extends('layouts.dashboard')

@section('title', 'Add Lead')

@section('dashboard-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lead Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('leads.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company') }}">
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="unsubscribed" {{ old('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="source" class="form-label">Source</label>
                            <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                                <option value="manual" {{ old('source') == 'manual' ? 'selected' : '' }}>Manual Entry</option>
                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="social" {{ old('source') == 'social' ? 'selected' : '' }}>Social Media</option>
                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags" value="{{ old('tags') }}" placeholder="Enter tags separated by commas">
                            <div class="form-text">Enter tags separated by commas (e.g., customer,vip,prospect)</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Custom Fields</label>
                        <div id="custom-fields-container">
                            <!-- Custom fields will be added here -->
                            @if(old('custom_fields_keys') && old('custom_fields_values'))
                                @foreach(old('custom_fields_keys') as $index => $key)
                                    @if(!empty($key) && isset(old('custom_fields_values')[$index]))
                                        <div class="row mb-2 custom-field-row">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="custom_fields_keys[]" placeholder="Field name" value="{{ $key }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="custom_fields_values[]" placeholder="Field value" value="{{ old('custom_fields_values')[$index] }}">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger remove-field" title="Remove field"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-custom-field">
                            <i class="bi bi-plus"></i> Add Custom Field
                        </button>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Save Lead</button>
                            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('custom-fields-container');
        const addBtn = document.getElementById('add-custom-field');
        const tagsInput = document.getElementById('tags');
        
        // Format tags input as a comma-separated list
        function formatTags(input) {
            // Split by comma, trim whitespace, filter empty, join back with comma+space
            const tags = input.value.split(',')
                .map(tag => tag.trim())
                .filter(tag => tag.length > 0)
                .join(', ');
                
            input.value = tags;
        }
        
        tagsInput.addEventListener('blur', function() {
            formatTags(this);
        });
        
        // Add new custom field
        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'row mb-2 custom-field-row';
            row.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="custom_fields_keys[]" placeholder="Field name">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="custom_fields_values[]" placeholder="Field value">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-field" title="Remove field"><i class="bi bi-trash"></i></button>
                </div>
            `;
            
            // Add remove event listener to the new remove button
            const removeBtn = row.querySelector('.remove-field');
            removeBtn.addEventListener('click', function() {
                row.remove();
            });
            
            container.appendChild(row);
        });
        
        // Remove custom field (for initial fields)
        document.querySelectorAll('.remove-field').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.custom-field-row').remove();
            });
        });
    });
</script>
@endsection