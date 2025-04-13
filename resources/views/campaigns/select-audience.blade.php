@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Select Audience for Campaign: {{ $campaign->name }}</h1>

    <h2>Leads by Status</h2>
    <ul>
        @foreach ($leadsByStatus as $status)
            <li>{{ $status->status }}: {{ $status->count }}</li>
        @endforeach
    </ul>

    <h2>Leads by Source</h2>
    <ul>
        @foreach ($leadsBySource as $source)
            <li>{{ $source->source }}: {{ $source->count }}</li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('campaigns.schedule', $campaign) }}">
        @csrf
        <button type="submit" class="btn btn-primary">Schedule Campaign</button>
    </form>
</div>
@endsection