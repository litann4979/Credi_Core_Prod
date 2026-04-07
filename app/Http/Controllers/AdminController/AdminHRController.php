<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Str;

class AdminHRController extends Controller
{
    public function indexHR()
    {
        $teamleads = User::where('designation', 'hr')
            ->withTrashed()
            ->orderBy('created_at', 'desc') 
            ->get();
        $totalTeamleads = $teamleads->count();
        $activeTeamleads = $teamleads->whereNull('deleted_at')->count();

        return view('admin.hr.index', compact('teamleads', 'totalTeamleads', 'activeTeamleads'));
    }



public function storeHR(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'designation' => 'required|in:hr',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (Auth::user()->designation !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized: Only admins can add Hr.');
        }

        $plainPassword = Str::random(10);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->designation = 'hr';
        $user->address = $validated['address'] ?? null;
        $user->password = Hash::make($plainPassword);
        $user->created_by = auth()->id();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('profile_photos', 'public');
            $user->profile_photo = $photo;
        }

        $user->save();

        // ✅ Add default Leave Balance record
        LeaveBalance::create([
            'user_id'    => $user->id,
            'leave_type' => 'casual',
            'total'      => 12,
            'used'       => 0,
            'balance'    => 12,
        ]);

        Mail::to($user->email)->send(new UserCredentials($user, $plainPassword));

        return redirect()->route('admin.hr.index')->with('success', 'HR added successfully!');
    } catch (ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput()
            ->with('error', 'Validation failed');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to add HR: ' . $e->getMessage());
    }
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

        return redirect()->back()->with('success', 'HR activated successfully.');
    }

    public function deactivate($id)
    {
        if (Auth::user()->designation !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'HR deactivated successfully.');
    }

    public function edit($id)
    {
        $teamlead = User::where('designation', 'hr')->findOrFail($id);
        $teamleads = User::where('designation', 'hr')->withTrashed()->get();
        $totalTeamleads = $teamleads->count();
        $activeTeamleads = $teamleads->whereNull('deleted_at')->count();

        return view('admin.hr.index', compact('teamlead', 'teamleads', 'totalTeamleads', 'activeTeamleads'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'photo' => 'nullable|image|max:2048',
                 'password' => 'nullable|string|min:8',
            ]);

            if (Auth::user()->designation !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized: Only admins can update HR.');
            }

            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->address = $validated['address'] ?? null;

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('profile_photos', 'public');
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

            return redirect()->route('admin.hr.index')->with('success', 'HR updated successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update HR: ' . $e->getMessage());
        }
    }
}
