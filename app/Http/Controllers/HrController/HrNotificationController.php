<?php

namespace App\Http\Controllers\HrController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Notification;
use App\Models\SalarySlip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HrNotificationController extends Controller
{
     public function index()
    {
       $users = User::whereIn('designation', ['team_lead', 'operations', 'employee'])
                     ->orderBy('name')
                     ->get();

        return view('hr.Notification.notification', compact('users'));
    }

    public function fetchHoliday(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $holiday = Holiday::whereDate('date', $today)->first();

        if ($holiday) {
             $notificationExists = Notification::where('holiday_id', $holiday->id)->exists();

            return response()->json([
                'status' => true,
                'holiday' => $holiday->name,
                'already_sent' => $notificationExists,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No holiday present today.',
            ]);
        }
    }

public function sendNotification(Request $request)
{
    $request->validate([
        'type' => 'required|in:holiday,birthday,others',
        'target' => 'nullable|required_if:type,others|in:all,individual',
        'user_ids' => 'nullable|required_if:target,individual|array',
         'message' => 'nullable|required_if:type,others|string|max:255',
    ]);

    $recipients = collect();
    $title = '';
    $message = '';

    if ($request->type === 'holiday') {
        $holiday = Holiday::whereDate('date', today())->first();

        if (!$holiday) {
            return response()->json([
                'status' => false,
                'message' => 'No holiday today'
            ]);
        }

        $recipients = User::whereIn('designation', [
            'employee', 'team_lead', 'operations'
        ])->get();

        $title = "Holiday Alert";
        $message = "Today is {$holiday->name}. Enjoy your holiday!";
    }

    elseif ($request->type === 'others') {
        $title = "New Message";
        $message = $request->message;

        $recipients = $request->target === 'all'
            ? User::whereIn('designation', ['employee', 'team_lead', 'operations'])->get()
            : User::whereIn('id', $request->user_ids)->get();
    }

    if ($recipients->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No recipients found'
        ]);
    }

    foreach ($recipients as $user) {
        NotificationHelper::sendGeneralNotification(
            $user->id,
            $title,
            $message,
            null
        );
    }

    return response()->json([
        'status' => true,
        'message' => 'Notifications sent successfully!'
    ]);
}



public function sendBirthdayNotification(Request $request)
{
    $user = User::findOrFail($request->input('user_id'));

    $already = Notification::where('user_id', $user->id)
        ->whereNull('holiday_id')
        ->whereNotNull('message')
        ->whereDate('created_at', now())
        ->exists();

    if ($already) {
        return response()->json(['status' => false, 'message' => "Birthday notification already sent to {$user->name}."]);
    }

    Notification::create([
        'user_id' => $user->id,
        'holiday_id' => null,
        'message' => "🎂 Happy Birthday, {$user->name}! 🎉",
        'is_read' => false,
    ]);

       // 🔔 Push notification
    NotificationHelper::sendGeneralNotification(
        $user->id,
        "Happy Birthday!",
        "🎂 Happy Birthday, {$user->name}! 🎉",
        null
    );

    return response()->json(['status' => true, 'message' => "Birthday notification sent to {$user->name}."]);
}

public function indexNotifications()
    {
        return view('hr.Notification.notification');
    }

    public function fetch()
{
    $notifications = Notification::where('user_id', auth()->id())
        ->where('created_at', '>=', Carbon::now()->subDays(7)) // last 7 days
        ->orderBy('created_at', 'desc')
        ->get(['id', 'user_id', 'task_id', 'salary_slip_id', 'message', 'is_read', 'created_at']);

    return response()->json($notifications);
}


     public function markRead(Request $request, $id)
{
    Log::info('markRead called', [
        'id' => $id,
        'user_id' => auth()->id(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    try {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
            Log::info('Notification marked as read', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);
        }

        return response()->json(['success' => true]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::warning('Notification not found or unauthorized', [
            'id' => $id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Notification not found or you are not authorized',
        ], 404);
    } catch (\Exception $e) {
        Log::error('Error marking notification as read', [
            'id' => $id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while marking the notification as read',
        ], 500);
    }
}

    // Optional: Uncomment if notification badge is needed

    public function countUnread()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        return response()->json(['count' => $count]);
    }

public function download($id)
    {
        $salarySlip = SalarySlip::findOrFail($id);
        $filePath = storage_path('app/public/' . $salarySlip->pdf_path);
        return response()->download($filePath);
    }



}
