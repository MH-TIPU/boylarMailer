@extends('layouts.dashboard')

@section('title', 'Email Templates')

@section('actions')
    <a href="{{ route('templates.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus"></i> New Template
    </a>
    <a href="{{ route('templates.builder') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-brush"></i> Template Builder
    </a>
@endsection

@section('dashboard-content')
    @if($templates->count() > 0)
        <div class="row">
            @foreach($templates as $template)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            {{ $template->name }}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $template->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $template->id }}">
                                    <li><a class="dropdown-item" href="{{ route('templates.edit', $template) }}">Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ route('templates.preview', $template) }}" target="_blank">Preview</a></li>
                                    <li>
                                        <form action="{{ route('templates.duplicate', $template) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Duplicate</button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="template-preview mb-3" style="height: 150px; overflow: hidden; background-color: #f8f9fa; border-radius: 5px;">
                                <iframe srcdoc="{{ $template->content }}" style="width: 100%; height: 300px; transform: scale(0.5); transform-origin: 0 0; pointer-events: none;" title="{{ $template->name }}"></iframe>
                            </div>
                            <p class="card-text">
                                <small class="text-muted">Created: {{ $template->created_at->format('M d, Y') }}</small>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-primary">Edit</a>
                            <a href="{{ route('campaigns.create') }}?template_id={{ $template->id }}" class="btn btn-sm btn-success">Use in Campaign</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $templates->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <h3>No Email Templates Yet</h3>
            <p class="text-muted">Create beautiful email templates to use in your campaigns</p>
            <div class="mt-4">
                <a href="{{ route('templates.create') }}" class="btn btn-primary">Create Template</a>
                <a href="{{ route('templates.builder') }}" class="btn btn-secondary">Use Template Builder</a>
            </div>
        </div>
    @endif
@endsection