@extends('layouts.dashboard')

@section('title', 'Create Template')

@section('dashboard-content')
<div class="container">
    <h1>Create a New Template</h1>
    <form action="{{ route('templates.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Template Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Template Content</label>
            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Template</button>
    </form>
</div>
@endsection