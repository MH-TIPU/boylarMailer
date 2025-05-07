<?php

namespace App\Http\Controllers;

use App\Models\SubscriberList;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Csv\Reader;
use League\Csv\Writer;

class SubscriberListController extends Controller
{
    public function index()
    {
        $lists = SubscriberList::where('user_id', Auth::id())
            ->withCount('subscribers')
            ->latest()
            ->get();

        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list = SubscriberList::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return response()->json($list, 201);
    }

    public function show(SubscriberList $list)
    {
        $this->authorize('view', $list);
        return response()->json($list->load('subscribers'));
    }

    public function update(Request $request, SubscriberList $list)
    {
        $this->authorize('update', $list);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list->update($validated);
        return response()->json($list);
    }

    public function destroy(SubscriberList $list)
    {
        $this->authorize('delete', $list);
        $list->delete();
        return response()->json(['message' => 'List deleted successfully']);
    }

    public function addSubscribers(Request $request, SubscriberList $list)
    {
        $this->authorize('update', $list);

        $validated = $request->validate([
            'subscribers' => 'required|array',
            'subscribers.*.email' => 'required|email',
            'subscribers.*.first_name' => 'nullable|string',
            'subscribers.*.last_name' => 'nullable|string',
            'subscribers.*.metadata' => 'nullable|array',
        ]);

        $subscribers = collect($validated['subscribers'])->map(function ($subscriber) {
            return new Subscriber([
                'email' => $subscriber['email'],
                'first_name' => $subscriber['first_name'] ?? null,
                'last_name' => $subscriber['last_name'] ?? null,
                'metadata' => $subscriber['metadata'] ?? null,
            ]);
        });

        $list->subscribers()->saveMany($subscribers);

        return response()->json($list->load('subscribers'));
    }

    public function removeSubscriber(Request $request, SubscriberList $list)
    {
        $this->authorize('update', $list);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $list->subscribers()->where('email', $validated['email'])->delete();

        return response()->json(['message' => 'Subscriber removed successfully']);
    }

    public function updateSubscriberStatus(Request $request, SubscriberList $list)
    {
        $this->authorize('update', $list);

        $validated = $request->validate([
            'email' => 'required|email',
            'status' => 'required|in:active,unsubscribed,bounced',
        ]);

        $subscriber = $list->subscribers()->where('email', $validated['email'])->firstOrFail();
        
        $subscriber->status = $validated['status'];
        if ($validated['status'] === 'unsubscribed') {
            $subscriber->unsubscribed_at = now();
        }
        $subscriber->save();

        return response()->json($subscriber);
    }

    public function import(Request $request, SubscriberList $list)
    {
        $this->authorize('update', $list);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $csv = Reader::createFromPath($request->file('file')->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords(['email', 'first_name', 'last_name']);
        $subscribers = [];

        foreach ($records as $record) {
            if (empty($record['email'])) continue;

            $subscribers[] = new Subscriber([
                'email' => $record['email'],
                'first_name' => $record['first_name'] ?? null,
                'last_name' => $record['last_name'] ?? null,
            ]);
        }

        $list->subscribers()->saveMany($subscribers);

        return response()->json([
            'message' => count($subscribers) . ' subscribers imported successfully',
            'imported_count' => count($subscribers),
        ]);
    }

    public function export(SubscriberList $list)
    {
        $this->authorize('view', $list);

        $csv = Writer::createFromString('');
        $csv->insertOne(['email', 'first_name', 'last_name', 'status', 'subscribed_at']);

        $subscribers = $list->subscribers()->get();
        foreach ($subscribers as $subscriber) {
            $csv->insertOne([
                $subscriber->email,
                $subscriber->first_name,
                $subscriber->last_name,
                $subscriber->status,
                $subscriber->subscribed_at,
            ]);
        }

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $list->name . '-subscribers.csv"',
        ]);
    }
} 