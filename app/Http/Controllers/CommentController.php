<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Groupmember;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function addComment(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:book,id',
            'comment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'item_id' => $request->item_id,
            'user_id' => auth()->user()->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment added successfully');
    }

    public function getComments($itemId)
    {
        $book = Book::with([
            'group',
            'comments' => function ($query) {
                $query->whereHas('user')->with('user:id,name')->latest('id');
            },
        ])->findOrFail($itemId);

        $status = user_in_group($book->group);

        if (!auth()->user()->hasRole('admin') && (empty($status) || !$status->activated)) {
            if (auth()->id() != $book->created_by && auth()->id() != $book['group']->created_by) {
                abort(403, 'Unauthorized action.');
            }
        }

        $comments = $book->comments;

        $data = [
            'title' => "{$book->name}'s comments",
            'template' => 'comment.list',
        ];

        return view('with_login_common', compact('data', 'comments', 'book', 'status'));
    }

    public function deleteAll(Request $request)
    {
        $book = Book::with('group')->findOrFail($request->item_id);
        $status = user_in_group($book->group);

        if (auth()->user()->hasRole('admin') || $book->created_by == auth()->id() || (!empty($status) && $status->activated && $status->member_role == 'admin')) {
            if ($book->comments()->count() > 0) {
                $book->comments()->delete();
                return back()->with('success', 'All comments deleted successfully.');
            }
            return back()->with('error', 'No comments to delete.');
        } else {
            abort(403);
        }
    }

    public function delete($id)
    {
        $comment = Comment::with(['book','book.group'])->findOrFail($id);
        $status = user_in_group($comment->book->group);

        if (auth()->user()->hasRole('admin') || auth()->id() == $comment->user_id || $comment->book->created_by == auth()->id() || (!empty($status) && $status->activated && $status->member_role == 'admin')) {
            if ($comment) {
                $comment->delete();
                return back()->with('success', 'Comment deleted successfully.');
            }
            return back()->with('error', 'Notification not found.');
        } else {
            abort(403);
        }
    }
}
