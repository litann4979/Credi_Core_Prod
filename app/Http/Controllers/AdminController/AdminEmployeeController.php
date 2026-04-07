<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Str;

class AdminEmployeeController extends Controller
{
    public function indexTeams()
    {
        $teamleads = User::where('designation', 'team_lead')->get();
        $employees = User::withTrashed()
            ->where('designation', 'employee')
            ->with('teamLead')
            ->orderBy('created_at', 'desc')
            ->get();
        $totalEmployees = User::withTrashed()
            ->where('designation', 'employee')
            ->count();
        $activeEmployees = User::where('designation', 'employee')
            ->whereNull('deleted_at')
            ->count();
        return view('admin.employee.index', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'teamleads'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'designation' => 'required|in:employee',
                'phone' => 'required|string|max:20',
                'employee_role' => 'nullable|string',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
                'team_lead_id' => 'required|exists:users,id,designation,team_lead',
            ]);

            if (Auth::user()->designation !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized: Only admins can add employees.');
            }

            $plainPassword = Str::random(10);
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->designation = 'employee';
            $user->phone = $validated['phone'];
            $user->employee_role = $validated['employee_role'] ?? null;
            $user->address = $validated['address'] ?? null;
            $user->password = Hash::make($plainPassword);
            $user->created_by = $validated['team_lead_id'];
            $user->team_lead_id = $validated['team_lead_id'];

            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

            $user->save();

               // ✅ Create LeaveBalance for the new employee
        LeaveBalance::create([
            'user_id'   => $user->id,
            'leave_type'=> 'casual',
            'total'     => 12,
            'used'      => 0,
            'balance'   => 12,
        ]);
            Mail::to($user->email)->send(new UserCredentials($user, $plainPassword));

            return redirect()->route('admin.employees.index')->with('success', 'Employee added successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Employee: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'designation' => 'required|in:employee',
                'phone' => 'required|string|max:20',
                'employee_role' => 'nullable|string',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
                'team_lead_id' => 'required|exists:users,id,designation,team_lead',
                 'password' => 'nullable|string|min:8',
            ]);

            if (Auth::user()->designation !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized: Only admins can update employees.');
            }

            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->designation = 'employee';
            $user->phone = $validated['phone'];
            $user->employee_role = $validated['employee_role'] ?? null;
            $user->address = $validated['address'] ?? null;
            $user->team_lead_id = $validated['team_lead_id'];

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

             if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

            $user->save();

               // ✅ Ensure LeaveBalance exists for the user
        LeaveBalance::firstOrCreate(
            [
                'user_id'    => $user->id,
                'leave_type' => 'casual',
            ],
            [
                'total'   => 12,
                'used'    => 0,
                'balance' => 12,
            ]
        );
            return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Employee: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $employee = User::where('designation', 'employee')->findOrFail($id);
        $teamleads = User::where('designation', 'team_lead')->get();
        $employees = User::withTrashed()
            ->where('designation', 'employee')
            ->with('teamLead')
            ->get();
        $totalEmployees = User::withTrashed()
            ->where('designation', 'employee')
            ->count();
        $activeEmployees = User::where('designation', 'employee')
            ->whereNull('deleted_at')
            ->count();

        return view('admin.employee.index', compact(
            'employee',
            'employees',
            'totalEmployees',
            'activeEmployees',
            'teamleads'
        ));
    }

    public function activate($id)
    {
        if (Auth::user()->designation !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->restore();
        }

        return redirect()->back()->with('success', 'Employee activated successfully.');
    }

    public function deactivate($id)
    {
        if (Auth::user()->designation !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Employee deactivated successfully.');
    }
}
