<?php

namespace App\Http\Controllers;

use App\Models\UserEntry;
use Illuminate\Http\Request;

class UserEntryController extends Controller
{
    /**
     * Display a listing of the user entries.
     */
    public function index()
    {
        $entries = UserEntry::with(['user', 'bookEntry'])->get();
        return response()->json($entries);
    }

    /**
     * Store a newly created user entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'entry_id' => 'required|exists:book_entries,id',
            'started_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:started_date',
        ]);

        $entry = UserEntry::create($validated);
        return response()->json($entry, 201);
    }

    /**
     * Display the specified user entry.
     */
    public function show(UserEntry $userEntry)
    {
        $userEntry->load(['user', 'bookEntry']);
        return response()->json($userEntry);
    }

    /**
     * Update the specified user entry in storage.
     */
    public function update(Request $request, UserEntry $userEntry)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'entry_id' => 'sometimes|exists:book_entries,id',
            'started_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:started_date',
        ]);

        $userEntry->update($validated);
        return response()->json($userEntry);
    }

    /**
     * Remove the specified user entry from storage.
     */
    public function destroy(UserEntry $userEntry)
    {
        $userEntry->delete();
        return response()->json(null, 204);
    }
}
