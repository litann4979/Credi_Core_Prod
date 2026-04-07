<?php

namespace App\Http\Controllers\TLController;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TLLeaveController extends Controller
{
  public function create()
    {
        // Fetch users with 'hr' designation for the applied_to dropdown
        $hrUsers = User::where('designation', 'hr')->select('id', 'name')->get();

        // Fetch recent leave histories for the authenticated user
        $leaveHistories = Leave::where('user_id', Auth::id())
            ->with(['appliedTo' => fn($query) => $query->select('id', 'name')])
            ->select('id', 'leave_type', 'start_date', 'end_date', 'total_days', 'reason', 'status', 'applied_to', 'decision_date', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('TeamLead.leave.index', compact('hrUsers', 'leaveHistories'));
    }

    /**
     * Store the leave application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'leave_type' => 'required|in:casual_leave,sick_leave,earned_leave,maternity_leave,paid_leave',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string|max:1000',
        'applied_to' => 'required|exists:users,id',
    ]);

    $startDate = Carbon::parse($validated['start_date']);
    $endDate   = Carbon::parse($validated['end_date']);

    // 🔴 Check if applying for only one day and it is a holiday
    if ($startDate->eq($endDate)) {
        $holiday = Holiday::whereDate('date', $startDate)->first();
        if ($holiday) {
            return redirect()->back()
                ->with('error', 'Cannot apply leave on holiday: '
                    . $holiday->name . ' (' . $holiday->date->format('Y-m-d') . ')')
                ->withInput();
        }
    }

    $totalDays = 0;
    $period = CarbonPeriod::create($startDate, $endDate);

    foreach ($period as $date) {
        // Skip holidays
        if (Holiday::whereDate('date', $date)->exists()) {
            continue;
        }

        // Skip weekends
        if ($date->isWeekend()) {
            continue;
        }

        $totalDays++;
    }

    if ($totalDays == 0) {
        return redirect()->back()
            ->with('error', 'Leave cannot be applied as all selected days are holidays/weekends.')
            ->withInput();
    }

    Leave::create([
        'user_id'     => Auth::id(),
        'leave_type'  => $validated['leave_type'],
        'start_date'  => $validated['start_date'],
        'end_date'    => $validated['end_date'],
        'total_days'  => $totalDays,
        'reason'      => $validated['reason'],
        'applied_to'  => $validated['applied_to'],
        'status'      => 'pending',
    ]);

    return redirect()->route('team_lead.leave.index')
        ->with('success', "Leave application submitted successfully for {$totalDays} day(s).");
}
}
