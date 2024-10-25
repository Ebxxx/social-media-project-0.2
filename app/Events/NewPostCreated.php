<?php
// NewPostCreated.php
namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $excludeUserId;

    public function __construct(Post $post, $excludeUserId)
    {
        $this->post = $post->load('user');
        $this->excludeUserId = $excludeUserId;
    }

    public function broadcastOn()
    {
        // Broadcast to a general channel instead of user-specific
        return new Channel('posts');
    }

    public function broadcastAs()
    {
        return 'posts.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->post->id,
            'type' => 'new_post',
            'post_id' => $this->post->id,
            'created_at' => $this->post->created_at,
            'exclude_user_id' => $this->excludeUserId
        ];
    }
}