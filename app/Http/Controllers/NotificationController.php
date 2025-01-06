<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
                                      ->with('post:id,content')
                                      ->orderBy('created_at', 'desc')
                                      ->take(10)
                                      ->get();
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function delete($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $notification = $user->notifications()->create($request->all());

        Broadcast::channel('notifications.{userId}', function ($user, $userId) use ($notification) {
            if ((int) $user->id === (int) $userId) {
                return $notification;
            }
        })->send(new \App\Events\NewNotification($notification));

        return response()->json($notification, 201);
    }
}

// namespace App\Http\Controllers;

// use App\Models\Notification;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Broadcast;

// class NotificationController extends Controller
// {
//     public function index(Request $request)
//     {
//         $user = $request->user();
//         $notifications = $user->notifications;

//         return response()->json($notifications);
//     }

//     public function markAsRead(Request $request, $notificationId)
//     {
//         $user = $request->user();
//         $notification = $user->notifications()->find($notificationId);

//         if ($notification) {
//             $notification->update(['is_read' => true]);
//         }

//         return response()->json(['message' => 'Notification marked as read']);
//     }

//     public function delete(Request $request, $notificationId)
//     {
//         $user = $request->user();
//         $notification = $user->notifications()->find($notificationId);

//         if ($notification) {
//             $notification->delete();
//         }

//         return response()->json(['message' => 'Notification deleted']);
//     }

//     public function store(Request $request)
//     {
//         $user = $request->user();
//         $notification = $user->notifications()->create($request->all());

//         Broadcast::channel('notifications.{userId}', function ($user, $userId) use ($notification) {
//             if ((int) $user->id === (int) $userId) {
//                 return $notification;
//             }
//         })->send(new \App\Events\NewNotification($notification));

//         return response()->json($notification, 201);
//     }
// }