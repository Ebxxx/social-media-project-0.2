<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
    
        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        // Create notification for the post owner
        try {
            if ($comment->user_id !== $post->user_id) {
                $notification = Notification::create([
                    'user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'type' => 'comment'
                ]);

                Log::info('Notification created:', $notification->toArray());

                broadcast(new NewNotification($notification))->toOthers();
                Log::info('NewNotification event broadcasted');
            }
        } catch (\Exception $e) {
            Log::error('Error creating or broadcasting notification: ' . $e->getMessage());
        }

        return $comment->load('user');
    }

    public function destroy(Comment $comment)
    {
        Log::info('Destroy method called for comment: ' . $comment->id);
        
        try {
            $this->authorize('delete', $comment);
            Log::info('Authorization passed for deleting comment: ' . $comment->id);
            
            $comment->delete();
            Log::info('Comment deleted successfully: ' . $comment->id);

            // Delete associated notification if exists
            Notification::where([
                'post_id' => $comment->post_id,
                'type' => 'comment'
            ])->delete();
            
            return response()->json(['message' => 'Comment deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete comment'], 500);
        }
    }
}
