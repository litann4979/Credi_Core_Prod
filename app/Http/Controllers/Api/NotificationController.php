<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
   public function getEmployeeNotifications(Request $request)
{
    $userId = $request->user()->id; // logged in employee id

    $notifications = Notification::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get(['id', 'message', 'is_read', 'created_at'])
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'message' => $item->message,
                'is_read' => $item->is_read,
                'created_at' => $item->created_at->format('d-m-Y h:i A'), // 👈 formatted
            ];
        });

    return response()->json([
        'status' => true,
        'notifications' => $notifications
    ]);
}

     public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id()) // Ensure employee owns it
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read'
        ]);
    }
}
