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

            $data['title'] = 'Home Page reviews';
            $data['template'] = 'admin.reviews.home.list';

            $reviews = HomeReviews::latest()->get();

            return view('with_login_common', compact('data', 'reviews'));
        } catch (\Exception $e) {
            Log::error('Error fetching Reviews: ' . $e->getMessage());
            return response()->json(['error' => 'Reviews not found.'], 404);
        }
    }

    public function create()
    {
        try {
            $data['title'] = 'Create Reviews';
            $data['template'] = 'admin.reviews.home.add';

            return view('with_login_common', compact('data'));
        } catch (\Exception $e) {
            \Log::error('Error showing review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Review not found.');
        }
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $review = new HomeReviews();
            $review->name = $request->name;
            $review->role = $request->role;

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $review->avatar = $avatarPath;
            }

            $review->textDescription = $request->textDescription;

            $review->save();

            return redirect("/home-reviews")->with('success', 'Review submitted successfully.');
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
            $data['title'] = 'Edit Reviews';
            $data['template'] = 'admin.reviews.home.add';

            $reviews = HomeReviews::findOrFail($homeReview->id);

            return view('with_login_common', compact('data', "reviews"));
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
            $request->validate([
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $review = HomeReviews::findOrFail($homeReview->id);
            $review->name = $request->name;
            $review->role = $request->role;

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $review->avatar = $avatarPath;
            }

            $review->textDescription = $request->textDescription;

            $review->save();

            return redirect("/home-reviews")->with('success', 'Review updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update review.');
        }
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(string $id)
    {
        try {
            $homeReviews = HomeReviews::findOrFail($id);
            $homeReviews->delete();

            return redirect()->back()->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting Redeem request: '.$e->getMessage());
            return response()->json(['error' => 'Failed to delete Review.'], 500);
        }
    }
}

