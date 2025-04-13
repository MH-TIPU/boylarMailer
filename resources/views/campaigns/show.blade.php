@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">{{ $campaign->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('campaigns.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Campaigns
                </a>
                
                @if($campaign->status == 'draft')
                <a href="{{ route('campaigns.edit', $campaign) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Campaign
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Campaign Info and Status -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <div class="flex justify-between mb-2">
                        <h2 class="text-lg font-medium">Campaign Details</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($campaign->status == 'sent') bg-sent text-sent
                            @elseif($campaign->status == 'scheduled') bg-scheduled text-scheduled
                            @elseif($campaign->status == 'sending') bg-sending text-sending
                            @elseif($campaign->status == 'draft') bg-draft text-draft
                            @elseif($campaign->status == 'paused') bg-paused text-paused
                            @elseif($campaign->status == 'canceled') bg-canceled text-canceled
                            @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-sm text-gray-500">Subject</p>
                            <p class="font-medium">{{ $campaign->subject }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">From</p>
                            <p class="font-medium">{{ $campaign->sender_name }} &lt;{{ $campaign->sender_email }}&gt;</p>
                        </div>
                        
                        @if($campaign->reply_to)
                        <div>
                            <p class="text-sm text-gray-500">Reply-To</p>
                            <p class="font-medium">{{ $campaign->reply_to }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-sm text-gray-500">Created</p>
                            <p class="font-medium">{{ $campaign->created_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                        
                        @if($campaign->scheduled_at)
                        <div>
                            <p class="text-sm text-gray-500">Scheduled For</p>
                            <p class="font-medium">{{ $campaign->scheduled_at->format('M d, Y \a\t H:i') }} ({{ $campaign->scheduled_at->diffForHumans() }})</p>
                        </div>
                        @endif
                        
                        @if($campaign->sent_at)
                        <div>
                            <p class="text-sm text-gray-500">Sent On</p>
                            <p class="font-medium">{{ $campaign->sent_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-sm text-gray-500">Tracking</p>
                            <p class="font-medium">
                                @if($campaign->track_opens) <span class="text-green-600">Opens: Enabled</span> @else <span class="text-gray-500">Opens: Disabled</span> @endif
                                |
                                @if($campaign->track_clicks) <span class="text-green-600">Clicks: Enabled</span> @else <span class="text-gray-500">Clicks: Disabled</span> @endif
                            </p>
                        </div>
                    </div>
                    
                    @if(in_array($campaign->status, ['scheduled', 'sending', 'paused']))
                    <div class="mt-4 pt-4 border-t border-gray-200 flex space-x-2">
                        @if($campaign->status == 'scheduled' || $campaign->status == 'paused')
                        <a href="{{ route('campaigns.edit.schedule', $campaign) }}" class="text-indigo-600 hover:text-indigo-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Change Schedule
                        </a>
                        @endif
                        
                        @if($campaign->status == 'scheduled')
                        <form action="{{ route('campaigns.send', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to send this campaign immediately?');">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                Send Now
                            </button>
                        </form>
                        @endif
                        
                        @if($campaign->status == 'scheduled' || $campaign->status == 'sending')
                        <form action="{{ route('campaigns.pause', $campaign) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pause
                            </button>
                        </form>
                        @endif
                        
                        @if($campaign->status == 'paused')
                        <form action="{{ route('campaigns.resume', $campaign) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Resume
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('campaigns.cancel', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this campaign? This action cannot be undone.');">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel Campaign
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                
                @if(in_array($campaign->status, ['sending', 'sent']))
                <!-- Campaign Performance Metrics -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-medium mb-4">Performance</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $campaign->sent_count }}</div>
                            <div class="text-sm text-gray-600">Sent</div>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $campaign->open_count }}</div>
                            <div class="text-sm text-gray-600">Opens</div>
                            <div class="text-xs text-gray-500">{{ $campaign->sent_count ? round(($campaign->open_count / $campaign->sent_count) * 100, 1) : 0 }}%</div>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $campaign->click_count }}</div>
                            <div class="text-sm text-gray-600">Clicks</div>
                            <div class="text-xs text-gray-500">{{ $campaign->sent_count ? round(($campaign->click_count / $campaign->sent_count) * 100, 1) : 0 }}%</div>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $campaign->bounce_count }}</div>
                            <div class="text-sm text-gray-600">Bounces</div>
                            <div class="text-xs text-gray-500">{{ $campaign->sent_count ? round(($campaign->bounce_count / $campaign->sent_count) * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-md font-medium mb-2">Activity Timeline</h3>
                        <div id="timeline-chart" class="h-64">
                            <!-- Timeline chart will be rendered here -->
                        </div>
                    </div>
                </div>
                
                <!-- Top Link Clicks -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-medium mb-4">Top Clicked Links</h2>
                    
                    @if(count($clickedLinks) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-100 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                                    <th class="px-6 py-3 bg-gray-100 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                                    <th class="px-6 py-3 bg-gray-100 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Clicks</th>
                                    <th class="px-6 py-3 bg-gray-100 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">CTR</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($clickedLinks as $link)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate">
                                            <a href="{{ $link->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $link->url }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $link->clicks }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $link->unique_clicks }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ round(($link->unique_clicks / $campaign->open_count) * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500">
                        No link clicks recorded yet.
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Template Info -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-medium mb-2">Email Template</h2>
                    
                    @if($campaign->template)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="p-4">
                            <p class="font-medium">{{ $campaign->template->name }}</p>
                            <p class="text-sm text-gray-500">Last updated: {{ $campaign->template->updated_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div class="h-48 border-t border-gray-200 overflow-hidden">
                            <iframe srcdoc="{{ $emailPreview }}" class="w-full h-full border-0" frameborder="0"></iframe>
                        </div>
                        
                        <div class="p-3 bg-gray-50 border-t border-gray-200">
                            <a href="{{ route('templates.show', $campaign->template) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                View Template
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500">
                        Template not found or deleted.
                    </div>
                    @endif
                </div>
                
                <!-- Audience Info -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-medium mb-2">Audience</h2>
                    
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium">Recipients</span>
                            <span class="text-indigo-600 font-medium">{{ $totalRecipients }}</span>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-2">
                            @if($campaign->audience_type == 'all')
                                <p>All active leads</p>
                            @elseif($campaign->audience_type == 'filtered')
                                <p>Filtered leads based on:</p>
                                <ul class="list-disc list-inside ml-2 mt-1 text-gray-500">
                                    @if($campaign->audience_filter['source'] ?? null)
                                        <li>Source: {{ $campaign->audience_filter['source'] }}</li>
                                    @endif
                                    @if($campaign->audience_filter['company'] ?? null)
                                        <li>Company: {{ $campaign->audience_filter['company'] }}</li>
                                    @endif
                                    @if(($campaign->audience_filter['date_from'] ?? null) || ($campaign->audience_filter['date_to'] ?? null))
                                        <li>
                                            Added: 
                                            @if($campaign->audience_filter['date_from'] ?? null)
                                                from {{ $campaign->audience_filter['date_from'] }}
                                            @endif
                                            @if($campaign->audience_filter['date_to'] ?? null)
                                                to {{ $campaign->audience_filter['date_to'] }}
                                            @endif
                                        </li>
                                    @endif
                                </ul>
                            @elseif($campaign->audience_type == 'tagged')
                                <p>Leads with tags:</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($campaign->audience_tags as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Condition: {{ $campaign->tag_condition == 'any' ? 'Match ANY tag' : 'Match ALL tags' }}
                                </p>
                            @elseif($campaign->audience_type == 'specific')
                                <p>Selected specific leads</p>
                            @endif
                        </div>
                        
                        @if(in_array($campaign->status, ['scheduled', 'sending', 'paused', 'sent']))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <a href="{{ route('campaigns.recipients', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                View All Recipients
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if(in_array($campaign->status, ['sending', 'sent']))
                <!-- Recent Activity -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-medium mb-2">Recent Activity</h2>
                    
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            @forelse($recentActivities as $activity)
                            <li class="px-4 py-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($activity->type == 'sent')
                                            <span class="inline-block w-2 h-2 bg-indigo-500 rounded-full"></span>
                                        @elseif($activity->type == 'open')
                                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                                        @elseif($activity->type == 'click')
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @elseif($activity->type == 'bounce')
                                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">
                                            {{ $activity->lead->first_name }} {{ $activity->lead->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if($activity->type == 'sent')
                                                Email sent
                                            @elseif($activity->type == 'open')
                                                Opened email
                                            @elseif($activity->type == 'click')
                                                Clicked link
                                            @elseif($activity->type == 'bounce')
                                                Email bounced
                                            @endif
                                            <span class="ml-1">{{ $activity->created_at->diffForHumans() }}</span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="px-4 py-3 text-center text-gray-500 text-sm">
                                No activity recorded yet.
                            </li>
                            @endforelse
                        </ul>
                        
                        @if(count($recentActivities) > 0)
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-right">
                            <a href="{{ route('campaigns.activities', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                View All Activity
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(in_array($campaign->status, ['sending', 'sent']))
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample data - in a real application, this would come from the server
        const timelineData = {
            labels: ['1 Apr', '2 Apr', '3 Apr', '4 Apr', '5 Apr', '6 Apr', '7 Apr'],
            datasets: [
                {
                    label: 'Opens',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Clicks',
                    data: [28, 48, 40, 19, 86, 27, 90],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        };
        
        // Render timeline chart
        const ctx = document.getElementById('timeline-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: timelineData,
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                maintainAspectRatio: false
            }
        });
    });
</script>
@endif
@endpush
@endsection