<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    /**
     * Store FCM token for authenticated user
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

     try {
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized user',
        ], 401);
    }

    $user->fcm_token = $request->fcm_token;
    $user->save();
    return response()->json([
        'success' => true,
        'message' => 'FCM token saved successfully',
    ]);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'Failed to save FCM token',
    ], 500);
}

    }
}
