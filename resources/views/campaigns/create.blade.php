@extends('layouts.dashboard')

@section('title', 'Create Campaign')

@section('dashboard-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Create New Campaign</h1>
    <div>
        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Campaigns
        </a>
    </div>
</div>

<form action="{{ route('campaigns.store') }}" method="POST" id="campaign-form">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <!-- Main Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Campaign Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="e.g. April Newsletter, Product Launch, etc.">
                        <div class="form-text">Internal name to identify this campaign (not visible to recipients)</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject Line <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required
                               placeholder="Enter the subject line of your email">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="preview_text" class="form-label">Preview Text</label>
                        <input type="text" class="form-control @error('preview_text') is-invalid @enderror" 
                               id="preview_text" name="preview_text" value="{{ old('preview_text') }}"
                               placeholder="Brief summary shown in recipient's inbox after the subject line">
                        <div class="form-text">This text appears in most email clients after the subject line (max 150 characters)</div>
                        @error('preview_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Email Content -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Email Content</h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="visual-editor-btn">Visual Editor</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="html-editor-btn">HTML Editor</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Email Content <span class="text-danger">*</span></span>
                            <a href="{{ route('templates.index') }}" class="text-decoration-none small">
                                <i class="bi bi-grid-3x3-gap"></i> Use Template
                            </a>
                        </label>
                        
                        <div id="visual-editor" class="border rounded p-3 mb-3">
                            <div id="editor-toolbar" class="btn-toolbar mb-2" role="toolbar">
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline"><i class="bi bi-type-underline"></i></button>
                                </div>
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyLeft"><i class="bi bi-text-left"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyCenter"><i class="bi bi-text-center"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyRight"><i class="bi bi-text-right"></i></button>
                                </div>
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                                </div>
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="createLink"><i class="bi bi-link-45deg"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertImage"><i class="bi bi-image"></i></button>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-fill"></i> Personalize
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-merge-tag="{{ '{' . '{first_name}' . '}' }}">First Name</a></li>
                                        <li><a class="dropdown-item" href="#" data-merge-tag="{{ '{' . '{last_name}' . '}' }}">Last Name</a></li>
                                        <li><a class="dropdown-item" href="#" data-merge-tag="{{ '{' . '{email}' . '}' }}">Email</a></li>
                                        <li><a class="dropdown-item" href="#" data-merge-tag="{{ '{' . '{company}' . '}' }}">Company</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" data-merge-tag="{{ '{' . '{unsubscribe_link}' . '}' }}">Unsubscribe Link</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div id="editor-content" class="form-control @error('content') is-invalid @enderror" contenteditable="true" style="min-height: 400px;">
                                {!! old('content') !!}
                            </div>
                        </div>
                        
                        <div id="html-editor" class="d-none">
                            <textarea id="html-content" class="form-control @error('content') is-invalid @enderror" 
                                      name="content" rows="15" style="font-family: monospace;">{{ old('content') }}</textarea>
                        </div>
                        
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Campaign Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sender Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                               id="from_name" name="from_name" 
                               value="{{ old('from_name', auth()->user()->name) }}" required>
                        @error('from_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="from_email" class="form-label">From Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('from_email') is-invalid @enderror" 
                               id="from_email" name="from_email" 
                               value="{{ old('from_email', auth()->user()->email) }}" required>
                        @error('from_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="reply_to" class="form-label">Reply-To Email</label>
                        <input type="email" class="form-control @error('reply_to') is-invalid @enderror" 
                               id="reply_to" name="reply_to" 
                               value="{{ old('reply_to', auth()->user()->email) }}">
                        @error('reply_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Tracking Options -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tracking Options</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" 
                               id="track_opens" name="track_opens" value="1" 
                               {{ old('track_opens', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="track_opens">
                            Track Email Opens
                        </label>
                        <div class="form-text">Monitor when recipients open your email</div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" 
                               id="track_clicks" name="track_clicks" value="1" 
                               {{ old('track_clicks', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="track_clicks">
                            Track Link Clicks
                        </label>
                        <div class="form-text">Monitor when recipients click links in your email</div>
                    </div>
                </div>
            </div>
            
            <!-- Save and Continue -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" name="save_type" value="draft" class="btn btn-primary">
                            Save Draft
                        </button>
                        <button type="submit" name="save_type" value="next" class="btn btn-success">
                            Save & Continue to Audience Selection
                        </button>
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
        const visualEditorBtn = document.getElementById('visual-editor-btn');
        const htmlEditorBtn = document.getElementById('html-editor-btn');
        const visualEditor = document.getElementById('visual-editor');
        const htmlEditor = document.getElementById('html-editor');
        const htmlContent = document.getElementById('html-content');
        const editorContent = document.getElementById('editor-content');

        // Switch to Visual Editor
        visualEditorBtn.addEventListener('click', function() {
            visualEditorBtn.classList.add('btn-primary');
            visualEditorBtn.classList.remove('btn-outline-primary');
            htmlEditorBtn.classList.add('btn-outline-secondary');
            htmlEditorBtn.classList.remove('btn-primary');

            // Transfer content from HTML to Visual Editor
            editorContent.innerHTML = htmlContent.value;

            visualEditor.classList.remove('d-none');
            htmlEditor.classList.add('d-none');
        });

        // Switch to HTML Editor
        htmlEditorBtn.addEventListener('click', function() {
            htmlEditorBtn.classList.add('btn-primary');
            htmlEditorBtn.classList.remove('btn-outline-secondary');
            visualEditorBtn.classList.add('btn-outline-primary');
            visualEditorBtn.classList.remove('btn-primary');

            // Transfer content from Visual to HTML Editor
            htmlContent.value = editorContent.innerHTML;

            htmlEditor.classList.remove('d-none');
            visualEditor.classList.add('d-none');
        });

        // Update HTML content before form submission
        document.getElementById('campaign-form').addEventListener('submit', function() {
            if (!visualEditor.classList.contains('d-none')) {
                htmlContent.value = editorContent.innerHTML;
            }
        });

        // Text editor toolbar functionality
        document.querySelectorAll('#editor-toolbar [data-command]').forEach(button => {
            button.addEventListener('click', function() {
                const command = this.getAttribute('data-command');
                
                if (command === 'createLink') {
                    const url = prompt('Enter the link URL:');
                    if (url) document.execCommand(command, false, url);
                } else if (command === 'insertImage') {
                    const url = prompt('Enter the image URL:');
                    if (url) document.execCommand(command, false, url);
                } else {
                    document.execCommand(command, false, null);
                }
                
                editorContent.focus();
            });
        });
        
        // Personalization merge tags
        document.querySelectorAll('[data-merge-tag]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const tag = this.getAttribute('data-merge-tag');
                
                // Insert at cursor position in the editor
                const selection = window.getSelection();
                const range = selection.getRangeAt(0);
                const node = document.createTextNode(tag);
                range.insertNode(node);
                
                // Move cursor after inserted text
                range.setStartAfter(node);
                range.setEndAfter(node);
                selection.removeAllRanges();
                selection.addRange(range);
                
                editorContent.focus();
            });
        });
    });
</script>
@endsection