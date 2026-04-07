<?php

namespace App\Http\Controllers\HrController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrLeaveApprovalController extends Controller
{
    /**
     * Display leave requests submitted to the authenticated HR user.
     *
     * @return \Illuminate\View\View
     */
   public function create()
{
    // Fetch leave requests where the HR user is the applied_to
    $leaveRequests = Leave::where('applied_to', Auth::id())
        ->with([
            'user' => fn($query) => $query->select('id', 'name'),
            'appliedTo' => fn($query) => $query->select('id', 'name')
        ])
        ->select('id', 'user_id', 'leave_type', 'start_date', 'end_date', 'total_days', 'reason', 'status', 'applied_to', 'decision_date')
        ->orderByRaw("CASE
                        WHEN status = 'pending' THEN 1
                        WHEN status = 'approved' THEN 2
                        WHEN status = 'rejected' THEN 3
                        ELSE 4 END")
        ->orderBy('created_at', 'desc') // within each status, latest first
        ->get();

    // Fetch leave balances for each leave's user and leave_type
    $leaveRequests->transform(function ($leave) {
        $leave->balance_info = LeaveBalance::where('user_id', $leave->user_id)
            ->select('total', 'used', 'balance')
            ->first();
        return $leave;
    });

    return view('hr.leave_approval.index', compact('leaveRequests'));
}


    /**
     * Handle approve or reject action for a leave request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Leave $leave
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Leave $leave)
{
    // Ensure the leave is assigned to the authenticated HR user
    if ($leave->applied_to !== Auth::id()) {
        return redirect()->route('hr.leave.approvals')->with('error', 'Unauthorized action.');
    }

    // Validate the request
    $validated = $request->validate([
        'action' => 'required|in:approved,rejected',
    ]);

    // Check leave balance for approval (only if NOT paid_leave)
    if ($validated['action'] === 'approved' && $leave->leave_type !== 'paid_leave') {
        $balance = LeaveBalance::where('user_id', $leave->user_id)->first();

        if (!$balance || $balance->balance < $leave->total_days) {
            return redirect()->route('hr.leave.approvals')->with('error', 'Insufficient leave balance.');
        }
    }

    // Update leave status
    $leave->update([
        'status' => $validated['action'] === 'approved' ? 'approved' : 'rejected',
        'approved_by' => Auth::id(),
        'decision_date' => Carbon::now(),
    ]);

    // Update leave balance only if NOT paid_leave
    if ($validated['action'] === 'approved' && $leave->leave_type !== 'paid_leave') {
        $balance->update([
            'used' => $balance->used + $leave->total_days,
            'balance' => $balance->balance -  $leave->total_days,
        ]);
    }

    // Create notification for the user who submitted the leave
    Notification::create([
        'user_id' => $leave->user_id,
        'message' => 'Your ' . $leave->leave_type . ' request from ' .
                     $leave->start_date->format('Y-m-d') . ' to ' .
                     $leave->end_date->format('Y-m-d') . ' has been ' .
                     $validated['action'] . '.',
        'is_read' => false,
    ]);

       // 🔔 Push Notification
    NotificationHelper::sendGeneralNotification(
        $leave->user_id,
        'Leave Request Update',
        "Your {$leave->leave_type} leave has been {$validated['action']}.",
        null
    );

    $message = $validated['action'] === 'approved' ? 'Leave approved successfully.' : 'Leave rejected successfully.';
    return redirect()->route('hr.leave.approvals')->with('success', $message);
}

}
