<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        return 'NewNotification';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'post_id' => $this->notification->post_id,
            'created_at' => $this->notification->created_at,
            'post' => [
                'id' => $this->notification->post->id,
                'content' => substr($this->notification->post->content, 0, 30) . '...'
            ]
        ];
    }
}

//OLD CODE

// namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class NewNotification implements ShouldBroadcast
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $notification;

//     public function __construct($notification)
//     {
//         $this->notification = $notification;
//     }

//     // public function broadcastOn()
//     // {
//     //     return new Channel('user.' . $this->notification->user_id);
//     // }

//     public function broadcastOn()
// {
//     \Log::info('Broadcasting on channel: post.' . $this->post->id);
//     return new Channel('post.' . $this->post->id);
// }

//     public function broadcastAs()
//     {
//         return 'NewNotification';
//     }

//     public function broadcastWith()
//     {
//         return [
//             'id' => $this->notification->id,
//             'type' => $this->notification->type,
//             'post_id' => $this->notification->post_id,
//             'created_at' => $this->notification->created_at,
//         ];
//     }
// }