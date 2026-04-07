<?php

namespace App\Http\Controllers\OpearationController;

use App\Http\Controllers\Controller;
use App\Models\CompOff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationCompOffController extends Controller
{
     /**
     * Show the comp-off application form and recent comp-off histories.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch the authenticated user's name
        $userName = Auth::user()->name;

        // Fetch recent comp-off histories for the authenticated user
        $compOffHistories = CompOff::where('user_id', Auth::id())
            ->with(['approver' => fn($query) => $query->select('id', 'name')])
            ->select('id', 'worked_on', 'requested_for', 'status', 'approved_by', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Opearation.comp-off.index', compact('userName', 'compOffHistories'));
    }

    /**
     * Store the comp-off application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
   
       public function store(Request $request)
{
    $validated = $request->validate([
        'worked_on' => 'required|date|before_or_equal:today',
        'requested_for' => 'required|date|after:today',
    ]);

    // Check for duplicate
    $exists = CompOff::where('user_id', Auth::id())
        ->where('worked_on', $validated['worked_on'])
        ->exists();

    if ($exists) {
        return redirect()->back()->withErrors([
            'worked_on' => 'You have already applied for a comp-off on this worked date.'
        ]);
    }

    // Pick HR
    $hrUser = User::where('designation', 'hr')
        ->withCount(['compOffsApproved'])
        ->orderBy('comp_offs_approved_count', 'asc')
        ->first();

    if (!$hrUser) {
        return redirect()->back()->withErrors([
            'approved_by' => 'No HR user available to approve the comp-off.'
        ]);
    }

    $expiresOn = Carbon::parse($validated['worked_on'])->addDays(30);

    CompOff::create([
        'user_id' => Auth::id(),
        'worked_on' => $validated['worked_on'],
        'requested_for' => $validated['requested_for'],
        'status' => 'Pending',
        'approved_by' => $hrUser->id,
        'expires_on' => $expiresOn,
    ]);

    return redirect()->route('operations.comp-off.index')
        ->with('success', 'Comp-off application submitted successfully.');
}
}
