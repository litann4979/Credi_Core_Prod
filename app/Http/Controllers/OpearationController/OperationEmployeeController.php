<?php

namespace App\Http\Controllers\OpearationController;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Str;

class OperationEmployeeController extends Controller
{

public function indexTeams()
{
    $operationsId = auth()->id();

    // Get team leads created by this operations user (full user objects)
    $teamleads = User::where('designation', 'team_lead')
        ->where('created_by', $operationsId)
        ->get();

    // Get team lead IDs from above
    $teamLeadIds = $teamleads->pluck('id');

    // Get employees under those team leads (even soft-deleted)
    $employees = User::withTrashed()
        ->whereIn('team_lead_id', $teamLeadIds)
        ->get();

    // Count totals
    $totalEmployees = User::withTrashed()
        ->whereIn('team_lead_id', $teamLeadIds)
        ->count();

    $activeEmployees = User::whereIn('team_lead_id', $teamLeadIds)
        ->whereNull('deleted_at')
        ->count();

    return view('Opearation.employees.index', compact(
        'employees',
        'totalEmployees',
        'activeEmployees',
        'teamleads' // 👈 send to view for dropdown
    ));
}



public function store(Request $request)
{
    $request->validate([
        'name'           => 'required|string|max:255',
        'email'          => 'required|email|unique:users',
        'designation'    => 'required|string',
        'phone'          => 'required|string',
        'employee_role'  => 'nullable|string',
        'address'        => 'nullable|string',
        'profile_photo'  => 'nullable|image|max:2048',
        'team_lead_id'   => 'required|exists:users,id',
    ]);

    $plainPassword = Str::random(10);

    $user = new User();
    $user->name           = $request->name;
    $user->email          = $request->email;
    $user->designation    = $request->designation;
    $user->phone          = $request->phone;
    $user->employee_role  = $request->employee_role;
    $user->address        = $request->address ?? '';
    $user->password       = Hash::make($plainPassword);
    $user->created_by     = auth()->id();
    $user->team_lead_id   = $request->team_lead_id;

    if ($request->hasFile('profile_photo')) {
        $file = $request->file('profile_photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('Uploads/profile_photos'), $filename);
        $user->profile_photo = 'Uploads/profile_photos/' . $filename;
    }

    $user->save();

    Log::info('New employee created:', $user->toArray());
    Mail::to($user->email)->send(new UserCredentials($user, $plainPassword));

    return response()->json([
        'success' => true,
        'message' => 'Employee added and credentials sent.',
        'employee' => $user // Return the created employee
    ], 201);
}



    /**
     * Update the specified employee.
     */
public function update(Request $request, $id)
{
    try {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'designation' => 'required|string',
            'phone' => 'required|string',
            'employee_role' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:2048',
            'team_lead_id' => 'required|exists:users,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->designation = $request->designation;
        $user->phone = $request->phone;
        $user->employee_role = $request->employee_role;
        $user->address = $request->address ?? '';
        $user->team_lead_id = $request->team_lead_id;

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        Log::info('Employee updated:', $user->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
            'employee' => $user->toArray()
        ], 200);
    } catch (ValidationException $e) {
        Log::warning('Validation failed for employee update:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Failed to update employee: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the employee: ' . $e->getMessage()
        ], 500);
    }
}



public function activate($id)
{
    $user = User::withTrashed()->findOrFail($id);

    if ($user->trashed()) {
        $user->restore(); // Bring back soft-deleted user
    }

    return redirect()->back()->with('success', 'Team Lead activated successfully.');
}

public function deactivate($id)
{
    $user = User::findOrFail($id);

    $user->delete(); // Soft delete (make inactive)

    return redirect()->back()->with('success', 'Team Lead deactivated successfully.');
}
}
