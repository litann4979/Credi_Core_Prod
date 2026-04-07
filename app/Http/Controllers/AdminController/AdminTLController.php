<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Str;

class AdminTLController extends Controller
{
    public function indexTeamlead()
    {
        $teamleads = User::where('designation', 'team_lead')
            ->withTrashed()
            ->with('creator')
            ->orderBy('created_at', 'desc') 
            ->get();

        $totalTeamleads = $teamleads->count();
        $activeTeamleads = $teamleads->whereNull('deleted_at')->count();
        $operations = User::where('designation', 'operations')->get();

        return view('admin.teamlead.index', compact('teamleads', 'totalTeamleads', 'activeTeamleads', 'operations'));
    }

    public function storeTeamlead(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'designation' => 'required|in:team_lead',
                'photo' => 'nullable|image|max:2048',
                'operation_id' => 'required|exists:users,id,designation,operations',
            ]);

            if (Auth::user()->designation !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized: Only admins can add team leads.');
            }

            $plainPassword = Str::random(10);
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->designation = 'team_lead';
            $user->address = $validated['address'] ?? null;
            $user->password = Hash::make($plainPassword);
            $user->created_by = $validated['operation_id'];

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

            return redirect()->route('admin.teamlead.index')->with('success', 'Team Lead added successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Team Lead: ' . $e->getMessage());
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

        return redirect()->back()->with('success', 'Team Lead activated successfully.');
    }

    public function deactivate($id)
    {
        if (Auth::user()->designation !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Team Lead deactivated successfully.');
    }

    public function edit($id)
    {
        $teamlead = User::where('designation', 'team_lead')->findOrFail($id);
        $operations = User::where('designation', 'operations')->get();

        return view('admin.teamlead.index', compact('teamlead', 'operations'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'photo' => 'nullable|image|max:2048',
                'operation_id' => 'required|exists:users,id,designation,operations',
                'password' => 'nullable|string|min:8',
            ]);

            if (Auth::user()->designation !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized: Only admins can update team leads.');
            }

            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->address = $validated['address'] ?? null;
            $user->created_by = $validated['operation_id'];

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

            return redirect()->route('admin.teamlead.index')->with('success', 'Team Lead updated successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Team Lead: ' . $e->getMessage());
        }
    }
}
