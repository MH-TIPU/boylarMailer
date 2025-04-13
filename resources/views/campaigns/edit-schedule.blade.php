@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Schedule for Campaign: {{ $campaign->name }}</h1>

    <form action="{{ route('campaigns.schedule', $campaign) }}" method="POST">
        @csrf
        @method('POST')

        <div class="form-group">
            <label for="scheduled_at">Scheduled At</label>
            <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $campaign->scheduled_at) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Schedule</button>
    </form>
</div>
@endsection