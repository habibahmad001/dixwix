<?php
namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $data = [
            'title'    => 'All Blog Posts',
            'template' => 'admin.blog.list',
        ];

        $posts = BlogPost::get();

        return view('with_login_common', compact('data', 'posts'));
    }

    public function create()
    {
        $retdata          = [];
        $tags             = Tag::get();
        $data['title']    = 'Add Blog Post';
        $data['template'] = 'admin.blog.add';
        return view('with_login_common', compact('data', 'retdata', 'tags'));
    }

    public function store(Request $request)
    {
        $formData = $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'author'   => 'nullable|string|max:255',
            'content'  => 'nullable',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'   => 'required',
        ]);

        $formData['slug'] = Str::slug($request->title);
        if (BlogPost::where('slug', $formData['slug'])->exists()) {
            return back()->with('error', 'Post already exists with the same title');
        }
        $formData['author'] = auth()->id();

        if ($request->hasFile('image')) {
            $formData['image'] = $request->file('image')->store('blog_images', 'public');
        }

        BlogPost::create($formData);

        return redirect()->back()->with('success', 'Blog post added successfully!');
    }

    public function edit($id)
    {
        $post             = BlogPost::findOrFail($id);
        $data['title']    = 'Edit Blog Post';
        $data['template'] = 'admin.blog.edit';
        return view('with_login_common', compact('data', 'post'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $formData = $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'content'  => 'nullable',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'   => 'required',
        ]);

        if ($blogPost->title !== $request->title) {
            $formData['slug'] = Str::slug($request->title);
            if (BlogPost::where('slug', $formData['slug'])->where('id', '!=', $blogPost->id)->exists()) {
                return back()->with('error', 'Post already exists with the same title');
            }
        }

        if ($request->hasFile('image')) {
            if ($blogPost->image) {
                $parts = explode('/storage/', $blogPost->image);
                if (isset($parts[1])) {
                    Storage::disk('public')->delete($parts[1]);
                }
            }

            $formData['image'] = $request->file('image')->store('blog_images', 'public');
        }

        $blogPost->update($formData);

        return redirect()->back()->with('success', 'Blog post updated successfully!');
    }

    public function destroy($id)
    {
        $blogPost = BlogPost::find($id);

        if (! $blogPost) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found.',
            ], 404);
        }

        if ($blogPost->image) {
            $parts = explode('/storage/', $blogPost->image);
            if (isset($parts[1])) {
                Storage::disk('public')->delete($parts[1]);
            }
        }

        $blogPost->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully.',
        ]);
    }

    public function postBySlug($slug)
    {

        $data['title']            = 'Blog';
        $data['template']         = 'single-blog';
        $data['is_banner']        = true;
        $data['is_banner_link']   = true;
        $data['banner_link']      = route('signup');
        $data['banner_heading']   = 'Blog';
        $data['banner_text']      = "The Heart of Knowledge and Imagination";
        $data['background-class'] = "header";

        $post         = BlogPost::with('user')->where("status", "published")->where("slug", "like", "%$slug%")->firstOrFail();
        $relatedPosts = BlogPost::where('category', "like", "%$post->category%")
            ->where('id', '!=', $post->id)->limit(3)->get();

        return view('without_login_common', compact('data', 'post', 'relatedPosts'));
    }

}
