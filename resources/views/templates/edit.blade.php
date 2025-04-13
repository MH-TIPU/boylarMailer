@extends('layouts.dashboard')

@section('title', 'Edit Template')

@section('dashboard-content')
<div class="container">
    <h1>Edit Template</h1>
    <form action="{{ route('templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Template Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $template->name }}" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Template Content</label>
            <textarea class="form-control" id="content" name="content" rows="10" required>{{ $template->content }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Template</button>
    </form>
</div>
@endsection