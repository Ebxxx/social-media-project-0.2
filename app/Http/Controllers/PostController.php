<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use App\Events\NewPostCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        return Post::with(['user', 'comments.user', 'likes'])
        ->withCount('likes')
        ->latest()
        ->get()
        ->map(function ($post) {
            $post->is_liked = $post->likes->contains('user_id', auth()->id());
            $post->can_edit = $post->user_id === auth()->id();
            return $post;
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = $request->user()->posts()->create($validated);
        
        // Get all users except the post creator
        $otherUsers = \App\Models\User::where('id', '!=', $request->user()->id)->get();
        
        // Create notifications for other users
        foreach ($otherUsers as $user) {
            Notification::create([
                'user_id' => $user->id,  // Send to other users
                'post_id' => $post->id,
                'type' => 'new_post',
                'is_read' => false
            ]);
        }

        // Load the post with its relations and add necessary flags
        $post = Post::with('user')
            ->withCount('likes')
            ->findOrFail($post->id);

        $post->can_edit = true;
        $post->is_liked = false;

        // Broadcast the NewPostCreated event with the creator's ID to exclude
        event(new NewPostCreated($post, $request->user()->id));

        return $post;
    }

    public function show(Post $post)
    {
        return $post->load('user');
    }

    public function update(Request $request, Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->update($validated);

        return $post;
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->noContent();
    }
}



//OLD CODE
// namespace App\Http\Controllers;

// use App\Models\Post;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class PostController extends Controller
// {
//     public function index()
//     {
//         return Post::with(['user', 'comments.user', 'likes'])
//         ->withCount('likes')
//         ->latest()
//         ->get()
//         ->map(function ($post) {
//             $post->is_liked = $post->likes->contains('user_id', auth()->id());
//             $post->can_edit = $post->user_id === auth()->id();
//             return $post;
//         });
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'content' => 'required|string|max:1000',
//         ]);

//         $post = $request->user()->posts()->create($validated);

//         // Load the post with its relations and add necessary flags
//         $post = Post::with('user')
//         ->withCount('likes')
//         ->findOrFail($post->id);

//         $post->can_edit = true; // The creator can always edit their new post
//         $post->is_liked = false; // A new post is not liked by default

//         return $post;
//     }

//     public function show(Post $post)
//     {
//         return $post->load('user');
//     }

//     public function update(Request $request, Post $post)
//     {
//         if (Auth::id() !== $post->user_id) {
//             return response()->json(['message' => 'Unauthorized'], 403);
//         }

//         $validated = $request->validate([
//             'content' => 'required|string|max:1000',
//         ]);

//         $post->update($validated);

//         return $post;
//     }

//     public function destroy(Post $post)
//     {
//         $this->authorize('delete', $post);
//         $post->delete();
//         return response()->noContent();
//     }
// }