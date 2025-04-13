<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(10);
        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template = EmailTemplate::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Template saved successfully!', 'id' => $template->id], 200);
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified template.
     */
    public function show(EmailTemplate $template)
    {
        return view('templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(EmailTemplate $template)
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Template updated successfully!'], 200);
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(EmailTemplate $template)
    {
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Show the drag-drop template builder.
     */
    public function builder()
    {
        return view('template_builder');
    }

    /**
     * Save template from the drag-drop builder via AJAX.
     */
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'name' => 'nullable|string|max:255',
        ]);

        $template = new EmailTemplate();
        $template->name = $request->input('name', 'Untitled Template ' . now()->format('Y-m-d H:i'));
        $template->content = $request->input('content');
        $template->save();

        return response()->json(['message' => 'Template saved successfully!', 'id' => $template->id], 200);
    }

    /**
     * Load a template via AJAX.
     */
    public function loadTemplate(Request $request)
    {
        $templateId = $request->input('id');
        
        if ($templateId) {
            $template = EmailTemplate::findOrFail($templateId);
        } else {
            $template = EmailTemplate::latest()->first();
        }

        if (!$template) {
            return response()->json(['message' => 'No template found.'], 404);
        }

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'content' => $template->content
        ], 200);
    }

    /**
     * Duplicate an existing template.
     */
    public function duplicate(EmailTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = 'Copy of ' . $template->name;
        $newTemplate->save();

        return redirect()->route('templates.index')
            ->with('success', 'Template duplicated successfully.');
    }
}