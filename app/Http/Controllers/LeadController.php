<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    public function index()
    {
        $leads = Lead::latest()->paginate(10);
        return view('leads.index', compact('leads'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        return view('leads.create');
    }

    /**
     * Store a newly created lead in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:50',
            'tags' => 'nullable|array',
            'custom_fields' => 'nullable|array',
        ]);

        $lead = Lead::create($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead)
    {
        return view('leads.edit', compact('lead'));
    }

    /**
     * Update the specified lead in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:50',
            'tags' => 'nullable|array',
            'custom_fields' => 'nullable|array',
        ]);

        $lead->update($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified lead from storage.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Import leads from CSV file.
     */
    public function importForm()
    {
        return view('leads.import');
    }

    /**
     * Process the import of leads from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        // Get headers
        $headers = array_shift($data);
        
        // Map CSV columns to database fields
        $mapping = [
            'first_name' => array_search('first_name', array_map('strtolower', $headers)),
            'last_name' => array_search('last_name', array_map('strtolower', $headers)),
            'email' => array_search('email', array_map('strtolower', $headers)),
            'phone' => array_search('phone', array_map('strtolower', $headers)),
            'company' => array_search('company', array_map('strtolower', $headers)),
            'status' => array_search('status', array_map('strtolower', $headers)),
            'source' => array_search('source', array_map('strtolower', $headers)),
        ];

        $importCount = 0;
        $errorCount = 0;
        $skippedEntries = [];
        
        foreach ($data as $row) {
            $leadData = [];
            
            foreach ($mapping as $field => $index) {
                if ($index !== false && isset($row[$index])) {
                    $leadData[$field] = $row[$index];
                }
            }
            
            // Email is required, skip if missing
            if (empty($leadData['email'])) {
                $skippedEntries[] = ['row' => $row, 'reason' => 'Missing email'];
                $errorCount++;
                continue;
            }
            
            // Check if lead already exists
            $existingLead = Lead::where('email', $leadData['email'])->first();
            
            if ($existingLead) {
                $existingLead->update($leadData);
            } else {
                // Validate minimum required data
                $validator = Validator::make($leadData, [
                    'email' => 'required|email',
                    'first_name' => 'required',
                    'last_name' => 'required',
                ]);
                
                if ($validator->fails()) {
                    $skippedEntries[] = ['row' => $row, 'reason' => $validator->errors()->first()];
                    $errorCount++;
                    continue;
                }
                
                Lead::create($leadData);
            }
            
            $importCount++;
        }
        
        // Log skipped entries
        if (!empty($skippedEntries)) {
            Log::info('Skipped entries during lead import', ['skipped' => $skippedEntries]);
        }
        
        return redirect()->route('leads.index')
            ->with('success', "Imported $importCount leads successfully. Skipped $errorCount invalid entries.");
    }

    /**
     * Export leads to CSV.
     */
    public function export()
    {
        $leads = Lead::all();
        $filename = 'leads-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Status', 'Source']);
            
            // Add data rows
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->first_name,
                    $lead->last_name,
                    $lead->email,
                    $lead->phone,
                    $lead->company,
                    $lead->status,
                    $lead->source,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for uploading leads via CSV.
     */
    public function showUploadForm()
    {
        return view('leads.upload');
    }

    /**
     * Upload leads from a CSV file.
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $headers = array_shift($data);
        $importCount = 0;

        foreach ($data as $row) {
            $leadData = array_combine($headers, $row);

            if (!empty($leadData['email'])) {
                Lead::updateOrCreate(['email' => $leadData['email']], $leadData);
                $importCount++;
            }
        }

        return redirect()->route('leads.index')
            ->with('success', "$importCount leads imported successfully.");
    }

    /**
     * Download all leads as a CSV file.
     */
    public function downloadCsv()
    {
        $leads = Lead::all();
        $filename = 'leads-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Status', 'Source']);

            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->first_name,
                    $lead->last_name,
                    $lead->email,
                    $lead->phone,
                    $lead->company,
                    $lead->status,
                    $lead->source,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadSample(): StreamedResponse
    {
        $headers = ['First Name', 'Last Name', 'Email', 'Company', 'Phone', 'Status'];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Example row
            fputcsv($file, ['John', 'Doe', 'john.doe@example.com', 'Example Inc.', '123-456-7890', 'Active']);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_leads.csv"',
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
        ]);

        $action = $request->input('action');
        $leadIds = $request->input('lead_ids');

        switch ($action) {
            case 'delete':
                Lead::whereIn('id', $leadIds)->delete();
                return redirect()->route('leads.index')->with('success', 'Selected leads have been deleted.');

            case 'activate':
                Lead::whereIn('id', $leadIds)->update(['status' => 'Active']);
                return redirect()->route('leads.index')->with('success', 'Selected leads have been activated.');

            case 'deactivate':
                Lead::whereIn('id', $leadIds)->update(['status' => 'Inactive']);
                return redirect()->route('leads.index')->with('success', 'Selected leads have been deactivated.');

            default:
                return redirect()->route('leads.index')->with('error', 'Invalid action.');
        }
    }
}
