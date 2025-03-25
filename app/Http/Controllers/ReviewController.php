<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Entries;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function addReview(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:book,id',
            'review'  => 'nullable|string|max:255',
            'rating'  => 'required|integer|min:1|max:5',
        ]);

        Review::updateOrCreate(['item_id' => $request->item_id, 'user_id' => auth()->id()],
            [
                'review' => $request->review,
                'rating' => $request->rating,
            ]);

        return back()->with('success', 'Your review has been saved successfully!');
    }

    public function getReviews($itemId)
    {
        $book = Book::with([
            'group',
            'reviews' => function ($query) {
                $query->whereHas('user')->with('user:id,name')->latest('id');
            },
        ])->findOrFail($itemId);

        $status = user_in_group($book->group);

        if (! auth()->user()->hasRole('admin') && (empty($status) || ! $status->activated)) {
            if (auth()->id() != $book->created_by && auth()->id() != $book['group']->created_by) {
                abort(403, 'Unauthorized action.');
            }
        }

        $reviews = $book->reviews;

        $averageRating = Review::where('item_id', $itemId)->avg('rating');

        $canAddReview = false;

        if (Entries::where("book_id", $book->id)->where("reserved_by", auth()->id())->where("is_reserved", 1)->exists()) {
            $canAddReview = true;
        } elseif ((auth()->user()->hasRole('admin') || auth()->id() == $book->created_by) || auth()->id() == $book->group->created_by || (! empty($status) && $status->activated && $status->member_role == 'admin')) {
            $canAddReview = true;
        }

        $data = [
            'title'    => "{$book->name}'s reviews",
            'template' => 'review.list',
        ];

        return view('with_login_common', compact('data', 'reviews', 'book', 'averageRating', 'status', 'canAddReview'));
    }

    public function deleteAll(Request $request)
    {
        $book = Book::with('group')->findOrFail($request->item_id);

        $status = user_in_group($book->group);

        if (auth()->user()->hasRole('admin') || $book->created_by == auth()->id() || (! empty($status) && $status->activated && $status->member_role == 'admin')) {
            if ($book->reviews()->count() > 0) {
                $book->reviews()->delete();
                return back()->with('success', 'All reviews deleted successfully.');
            }
            return back()->with('error', 'No reviews to delete.');
        } else {
            abort(403);
        }

    }

    public function delete($id)
    {
        $review = Review::with(['book', 'book.group'])->findOrFail($id);

        $status = user_in_group($review->book->group);

        if (auth()->user()->hasRole('admin') || auth()->id() == $review->user_id || $review->book->created_by == auth()->id() || (! empty($status) && $status->activated && $status->member_role == 'admin')) {
            if ($review) {
                $review->delete();
                return redirect()->back()->with('success', 'Review deleted successfully.');
            }

            return redirect()->back()->with('error', 'Review not found.');
        } else {
            abort(403);
        }

    }
}
