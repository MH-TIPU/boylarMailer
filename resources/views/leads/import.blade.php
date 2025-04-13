@extends('layouts.dashboard')

@section('title', 'Import Leads')

@section('dashboard-content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Import Leads from CSV</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">CSV File Requirements</h6>
                            <p class="mb-0">Your CSV file should include the following columns:</p>
                            <ul class="mb-0">
                                <li><strong>first_name</strong> - Lead's first name</li>
                                <li><strong>last_name</strong> - Lead's last name</li>
                                <li><strong>email</strong> - Lead's email address (required)</li>
                                <li><strong>phone</strong> - Lead's phone number (optional)</li>
                                <li><strong>company</strong> - Lead's company (optional)</li>
                                <li><strong>status</strong> - Lead's status (optional: active, inactive, unsubscribed)</li>
                                <li><strong>tags</strong> - Comma-separated tags (optional)</li>
                            </ul>
                            <p class="mt-2 mb-0">Any additional columns will be imported as custom fields.</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="csv_file" class="form-label">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file" name="csv_file" accept=".csv" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">File must be in CSV format and under 10MB.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="source" class="form-label">Lead Source</label>
                        <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                            <option value="import">Import</option>
                            <option value="website">Website</option>
                            <option value="referral">Referral</option>
                            <option value="social">Social Media</option>
                            <option value="other">Other</option>
                        </select>
                        @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">This source will be assigned to all imported leads.</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="has_headers" name="has_headers" checked>
                            <label class="form-check-label" for="has_headers">
                                CSV file has headers
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Import Options</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="duplicate_handling" id="skip_duplicates" value="skip" checked>
                            <label class="form-check-label" for="skip_duplicates">
                                Skip duplicates (recommended)
                            </label>
                            <div class="form-text">If a lead with the same email already exists, skip the import for that lead.</div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="duplicate_handling" id="update_duplicates" value="update">
                            <label class="form-check-label" for="update_duplicates">
                                Update duplicates
                            </label>
                            <div class="form-text">If a lead with the same email already exists, update their information.</div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="confirm_import" name="confirm_import" required>
                            <label class="form-check-label" for="confirm_import">
                                I confirm that I have permission to import these contacts and that they have consented to receive email communications.
                            </label>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Import Leads</button>
                        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Download Sample CSV</h5>
            </div>
            <div class="card-body">
                <p>Download a sample CSV file with the correct formatting to use as a template for your import.</p>
                <a href="{{ route('leads.download-sample') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download"></i> Download Sample CSV
                </a>
            </div>
        </div>
    </div>
</div>
@endsection