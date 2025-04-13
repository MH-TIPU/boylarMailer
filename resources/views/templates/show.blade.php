@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">{{ $template->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('templates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Templates
                </a>
                <a href="{{ route('templates.edit', $template) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Template
                </a>
                <a href="{{ route('campaigns.create') }}?template_id={{ $template->id }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Create Campaign
                </a>
                <button id="test-email-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Send Test Email
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Template Preview -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-medium mb-4">Template Preview</h2>
                    
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex space-x-2">
                                <button id="desktop-view-btn" class="text-indigo-600 py-1 px-2 rounded hover:bg-indigo-50 active">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button id="mobile-view-btn" class="text-gray-400 py-1 px-2 rounded hover:bg-indigo-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('templates.preview', $template) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Open in New Tab
                                </a>
                            </div>
                        </div>
                        
                        <div id="preview-container" class="preview-desktop">
                            <iframe id="template-preview" srcdoc="{{ $emailHtml }}" class="w-full border-0" style="height: 600px;" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Info -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-medium mb-4">Template Information</h2>
                    
                    <div class="grid gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Created</p>
                            <p class="font-medium">{{ $template->created_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Last Updated</p>
                            <p class="font-medium">{{ $template->updated_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Category</p>
                            <p class="font-medium">{{ $template->category ?: 'Uncategorized' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Tags</p>
                            @if(count($template->tags ?? []))
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($template->tags as $tag)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $tag }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500">No tags</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h2 class="text-lg font-medium mb-4">Personalization Variables</h2>
                    
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{first_name}' }}</div>
                                <div class="text-xs text-gray-500">Lead's first name</div>
                            </li>
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{last_name}' }}</div>
                                <div class="text-xs text-gray-500">Lead's last name</div>
                            </li>
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{email}' }}</div>
                                <div class="text-xs text-gray-500">Lead's email address</div>
                            </li>
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{company}' }}</div>
                                <div class="text-xs text-gray-500">Lead's company name</div>
                            </li>
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{title}' }}</div>
                                <div class="text-xs text-gray-500">Lead's job title</div>
                            </li>
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{date}' }}</div>
                                <div class="text-xs text-gray-500">Current date</div>
                            </li>
                            <li class="px-4 py-3">
                                <div class="text-sm font-medium">{{ '{unsubscribe_link}' }}</div>
                                <div class="text-xs text-gray-500">Unsubscribe link (required)</div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-medium mb-4">Usage</h2>
                    
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium">Used in Campaigns</span>
                                <span class="text-indigo-600 font-medium">{{ count($campaigns) }}</span>
                            </div>
                            
                            @if(count($campaigns))
                            <ul class="divide-y divide-gray-200 mt-3">
                                @foreach($campaigns as $campaign)
                                <li class="py-2">
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        {{ $campaign->name }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        Status: 
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
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
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-gray-500 text-sm">This template hasn't been used in any campaigns yet.</p>
                            @endif
                        </div>
                        
                        @if(count($campaigns) > 3)
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-right">
                            <a href="{{ route('campaigns.index') }}?template_id={{ $template->id }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                View All Campaigns
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div id="test-email-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Send Test Email</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Send this template to yourself or a test email address to see how it looks in an actual email client.
                </p>
                <form id="test-email-form" action="{{ route('templates.send-test', $template) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="test_email" class="block text-sm font-medium text-gray-700 text-left mb-1">Email Address</label>
                        <input type="email" name="test_email" id="test_email" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="email@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="block text-sm font-medium text-gray-700 text-left mb-1">Subject Line</label>
                        <input type="text" name="subject" id="subject" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Test Email: {{ $template->name }}" value="Test Email: {{ $template->name }}">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" id="cancel-test-email" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Send Test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview device switching
        const desktopBtn = document.getElementById('desktop-view-btn');
        const mobileBtn = document.getElementById('mobile-view-btn');
        const previewContainer = document.getElementById('preview-container');
        
        desktopBtn.addEventListener('click', function() {
            previewContainer.className = 'preview-desktop';
            desktopBtn.classList.add('text-indigo-600');
            desktopBtn.classList.remove('text-gray-400');
            mobileBtn.classList.add('text-gray-400');
            mobileBtn.classList.remove('text-indigo-600');
        });
        
        mobileBtn.addEventListener('click', function() {
            previewContainer.className = 'preview-mobile';
            mobileBtn.classList.add('text-indigo-600');
            mobileBtn.classList.remove('text-gray-400');
            desktopBtn.classList.add('text-gray-400');
            desktopBtn.classList.remove('text-indigo-600');
        });
        
        // Test email modal
        const testEmailBtn = document.getElementById('test-email-btn');
        const testEmailModal = document.getElementById('test-email-modal');
        const cancelTestEmail = document.getElementById('cancel-test-email');
        
        testEmailBtn.addEventListener('click', function() {
            testEmailModal.classList.remove('hidden');
        });
        
        cancelTestEmail.addEventListener('click', function() {
            testEmailModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === testEmailModal) {
                testEmailModal.classList.add('hidden');
            }
        });
    });
</script>

<style>
    .preview-desktop {
        width: 100%;
    }
    
    .preview-mobile {
        width: 375px;
        margin: 0 auto;
        border-left: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
    }
</style>
@endpush
@endsection