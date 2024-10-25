<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggleLike(Request $request, Post $post)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $like = $post->likes()->where('user_id', $request->user()->id)->first();
    
        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            $like = $post->likes()->create(['user_id' => $request->user()->id]);
            $isLiked = true;

            // Create notification for post owner
            if ($post->user_id !== $request->user()->id) {
                $notification = Notification::create([
                    'user_id' => $post->user_id,
                    'type' => 'like',
                    'post_id' => $post->id
                ]);

                // Broadcast the notification
                event(new NewNotification($notification, [
                    'user' => $request->user(),
                    'post' => $post
                ]));
            }
        }
    
        return response()->json([
            'likes_count' => $post->likes()->count(),
            'is_liked' => $isLiked,
        ]);
    }
}


// OLD CODE

// namespace App\Http\Controllers;

// use App\Models\Post;
// use Illuminate\Http\Request;


// class LikeController extends Controller
// {
//     public function toggleLike(Request $request, Post $post)
//     {
//         if (!$request->user()) {
//             return response()->json(['message' => 'Unauthorized'], 401);
//         }
    
//         $like = $post->likes()->where('user_id', $request->user()->id)->first();
    
//         if ($like) {
//             $like->delete();
//             $isLiked = false;
//         } else {
//             $like = $post->likes()->create(['user_id' => $request->user()->id]);
//             $isLiked = true;

            
//         }
    
//         return response()->json([
//             'likes_count' => $post->likes()->count(),
//             'is_liked' => $isLiked,
//         ]);
//     }
    
// }