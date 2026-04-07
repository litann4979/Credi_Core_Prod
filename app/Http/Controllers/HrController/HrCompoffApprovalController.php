<?php

namespace App\Http\Controllers\HrController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\CompOff;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrCompoffApprovalController extends Controller
{
     public function index()
    {
        // Fetch CompOff requests where the HR user is assigned (approved_by)
        $compOffRequests = CompOff::where('approved_by', Auth::id())
            ->with([
                'user' => fn($query) => $query->select('id', 'name'),
                'approver' => fn($query) => $query->select('id', 'name')
            ])
            ->select('id', 'user_id', 'worked_on', 'requested_for', 'status', 'approved_by', 'expires_on')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.comp_off_approval.index', compact('compOffRequests'));
    }

    public function update(Request $request, CompOff $compOff)
    {
        // Ensure the CompOff is assigned to the authenticated HR user
        if ($compOff->approved_by !== Auth::id()) {
            return redirect()->route('hr.compoff.approvals')->with('error', 'Unauthorized action.');
        }

        // Ensure the CompOff is still pending
        if ($compOff->status !== 'Pending') {
            return redirect()->route('hr.compoff.approvals')->with('error', 'This CompOff request has already been processed.');
        }

        // Validate the request
        $validated = $request->validate([
            'action' => 'required|in:Approved,Rejected',
        ]);

        // Update CompOff status
        $compOff->update([
            'status' => $validated['action'] === 'Approved' ? 'Approved' : 'Rejected',
        ]);

        // Create notification for the user who submitted the CompOff
        Notification::create([
            'user_id' => $compOff->user_id,
            'message' => 'Your CompOff request for ' . $compOff->requested_for->format('Y-m-d') .
                         ' (worked on ' . $compOff->worked_on->format('Y-m-d') . ') has been ' . $validated['action'] . '.',
            'is_read' => false,
        ]);

            // 🔔 Push Notification
    NotificationHelper::sendGeneralNotification(
        $compOff->user_id,
        'CompOff Update',
        "Your CompOff request has been {$validated['action']}.",
        null
    );

        $message = $validated['action'] === 'Approved' ? 'CompOff approved successfully.' : 'CompOff rejected successfully.';
        return redirect()->route('hr.compoff.approvals')->with('success', $message);
    }
}
