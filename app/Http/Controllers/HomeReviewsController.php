<?php

namespace App\Http\Controllers;

use App\Models\HomeReviews;
use Illuminate\Http\Request;

class HomeReviewsController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index()
    {
        try {

            $data['title'] = 'Reviews';
            $data['template'] = 'admin.reviews.home.list';

            $reviews = HomeReviews::latest()->get();

            return view('with_login_common', compact('data', 'reviews'));
        } catch (\Exception $e) {
            Log::error('Error fetching Reviews: ' . $e->getMessage());
            return response()->json(['error' => 'Reviews not found.'], 404);
        }
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:256',
                'role' => 'nullable|string|max:256',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'textDescription' => 'nullable|string',
            ]);

            // Handle avatar upload if present
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('homeavatar');
                $validated['avatar'] = $avatarPath;
            }

            // Store review in DB
            $review = HomeReviews::create($validated);

            return redirect()->back()->with('success', 'Review submitted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error storing Review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit review.');
        }
    }

    /**
     * Display the specified review.
     */
    public function show(HomeReviews $homeReview)
    {
        try {
            $data['title'] = 'Reviews';
            $data['template'] = 'admin.reviews.home.show';

            return view('with_login_common', compact('data', 'homeReview'));
        } catch (\Exception $e) {
            \Log::error('Error showing review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Review not found.');
        }
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, HomeReviews $homeReview)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:256',
                'role' => 'nullable|string|max:256',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'textDescription' => 'nullable|string',
            ]);

            // Handle avatar upload if a new file is provided
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('homeavatar');
                $validated['avatar'] = $avatarPath;
            }

            $homeReview->update($validated);

            return redirect()->back()->with('success', 'Review updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update review.');
        }
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(HomeReviews $homeReview)
    {
        $homeReview->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }
}

