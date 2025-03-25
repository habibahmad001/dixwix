<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:group,id',
            'title' => 'required|string|max:1000',
            'content' => 'nullable|string|max:1000',
            'comments_enabled' => 'nullable',
        ]);

        $post = auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'group_id' => $request->group_id,
            'comments_enabled' => $request->comments_enabled == 'on' ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post added successfully!',
            'data' => $post,
        ]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['success' => true]);
    }

    public function postCommentStore(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:500',
        ]);

        $post = Post::findOrFail($request->post_id);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Responded successfully',
            'comment' => $comment,
            'user' => auth()->user(),
        ]);

    }

    public function postCommentDestroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['success' => true]);
    }

}
