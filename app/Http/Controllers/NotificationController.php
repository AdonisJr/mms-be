<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    // Get notifications for the authenticated user
    public function index()
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        $notifications = Notification::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return response()->json($notifications);
    }
    // Store a new notification
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        $notification = Notification::create($request->all());

        return response()->json([
            'message' => 'Notification created successfully.',
            'notification' => $notification,
        ], 201);
    }

    // Mark a notification as read for the authenticated user
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notification->isRead = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
