<?php
namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostInteraction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $post;
    public $userId;

    public function __construct($type, Post $post, $userId)
    {
        $this->type = $type;
        $this->post = $post;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->post->user_id);
    }

    public function broadcastAs()
    {
        return 'post.interaction';
    }

    public function broadcastWith()
    {
        return [
            'type' => $this->type,
            'post_id' => $this->post->id,
            'user_id' => $this->userId,
            'created_at' => now(),
        ];
    }
}