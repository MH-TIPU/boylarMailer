<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CampaignController;
use App\Models\Lead;
use App\Models\EmailCampaign;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('home');
});

// Authentication routes (provided by Laravel)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $recentCampaigns = EmailCampaign::latest()->take(5)->get();
        $recentLeads = Lead::latest()->take(5)->get();
        
        return view('dashboard', compact('recentCampaigns', 'recentLeads'));
    })->name('dashboard');
    
    // Lead Management
    Route::resource('leads', LeadController::class);
    Route::get('/leads-import', [LeadController::class, 'importForm'])->name('leads.import.form');
    Route::post('/leads-import', [LeadController::class, 'import'])->name('leads.import');
    Route::get('/leads-export', [LeadController::class, 'export'])->name('leads.export');
    Route::get('/leads/upload', [LeadController::class, 'showUploadForm'])->name('leads.upload.form');
    Route::post('/leads/upload', [LeadController::class, 'uploadCsv'])->name('leads.upload');
    Route::get('/leads/download', [LeadController::class, 'downloadCsv'])->name('leads.download');
    Route::get('/leads/download-sample', [LeadController::class, 'downloadSample'])->name('leads.download-sample');
    Route::post('/leads/bulk-action', [LeadController::class, 'bulkAction'])->name('leads.bulk-action');
    
    // Email Templates
    Route::resource('templates', TemplateController::class);
    Route::get('/template-builder', [TemplateController::class, 'builder'])->name('templates.builder');
    Route::post('/save-template', [TemplateController::class, 'saveTemplate']);
    Route::get('/load-template', [TemplateController::class, 'loadTemplate']);
    Route::post('/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');
    
    // Email Campaigns
    Route::resource('campaigns', CampaignController::class);
    Route::get('/campaigns/{campaign}/audience', [CampaignController::class, 'selectAudience'])->name('campaigns.audience');
    Route::post('/campaigns/{campaign}/schedule', [CampaignController::class, 'scheduleForAudience'])->name('campaigns.schedule');
    Route::post('/campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('/campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');
    Route::post('/campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
    Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::get('/campaigns/{campaign}/edit-schedule', [CampaignController::class, 'editSchedule'])->name('campaigns.edit.schedule');
});

// Routes accessible without authentication
Route::get('/template-preview/{template}', function (\App\Models\EmailTemplate $template) {
    return view('templates.preview', compact('template'));
})->name('templates.preview');

Route::get('/track/open/{campaign}', [CampaignController::class, 'trackOpen'])->name('track.open');
Route::get('/track/click/{campaign}', [CampaignController::class, 'trackClick'])->name('track.click');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
