<?php

namespace App\Http\Controllers\TLController;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentials;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TLEmployeeController extends Controller
{
    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'designation' => 'required|string',
            'phone' => [
    'required',
    'regex:/^[0-9]{10,15}$/',
],

            'employee_role' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:2048',
        ], [
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.regex' => 'The phone number must be 10-15 digits, optionally starting with a country code (e.g., +91).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $plainPassword = Str::random(10);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->designation = $request->designation;
        $user->phone = preg_replace('/\D/', '', $request->phone); // Removes all non-digit characters
        $user->employee_role = $request->employee_role;
        $user->address = $request->address ?? '';
        $user->password = Hash::make($plainPassword);
        $user->created_by = auth()->id();
        $user->team_lead_id = auth()->id();

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $user->profile_photo = $path;
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

        // Send welcome email
        Mail::to($user->email)->send(new UserCredentials($user, $plainPassword));

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully and credentials sent to email.',
            'employee' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'designation' => $user->designation,
                'phone' => $user->phone,
                'employee_role' => $user->employee_role,
                'address' => $user->address ?? 'Not provided',
                'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                'deleted_at' => $user->deleted_at ? $user->deleted_at->toISOString() : null,
            ]
        ], 201);
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::where('team_lead_id', auth()->id())->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'designation' => 'required|string',
                'phone' => [
    'required',
    'regex:/^[0-9]{10,15}$/',
],

                'employee_role' => 'nullable|string',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
            ], [
                'email.required' => 'The email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already in use.',
                'phone.regex' => 'The phone number must be 10-15 digits, optionally starting with a country code (e.g., +91).',
                 'team_lead_id' => 'required|exists:users,id,designation,team_lead',
                 'password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->phone = preg_replace('/\D/', '', $request->phone); // Removes all non-digit characters
            $user->employee_role = $request->employee_role;
            $user->address = $request->address ?? '';

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                    unlink(storage_path('app/public/' . $user->profile_photo));
                }
                $file = $request->file('profile_photo');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $path = $file->storeAs('profile_photos', $filename, 'public');
                $user->profile_photo = $path;
            }
                 if (!empty($request->password)) {
    $user->password = Hash::make($request->password);
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

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully.',
                'employee' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'designation' => $user->designation,
                    'phone' => $user->phone,
                    'employee_role' => $user->employee_role,
                    'address' => $user->address ?? 'Not provided',
                    'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                    'deleted_at' => $user->deleted_at ? $user->deleted_at->toISOString() : null,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Employee not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or you are not authorized to update this employee.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: Unable to update employee.',
            ], 500);
        }
    }

    /**
     * Deactivate (Soft delete)
     */
public function deactivate($id)
    {
        try {
            $user = User::where('team_lead_id', auth()->id())->findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deactivated successfully.',
                'employee' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'designation' => $user->designation,
                    'phone' => $user->phone,
                    'employee_role' => $user->employee_role,
                    'address' => $user->address ?? 'Not provided',
                    'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                    'deleted_at' => $user->deleted_at ? $user->deleted_at->toISOString() : null,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Employee not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or you are not authorized to deactivate this employee.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deactivating employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: Unable to deactivate employee.',
            ], 500);
        }
    }

    /**
     * Activate (Restore)
     */
    public function activate($id)
    {
        try {
            $user = User::withTrashed()
                ->where('team_lead_id', auth()->id())
                ->findOrFail($id);

            $user->restore();

            return response()->json([
                'success' => true,
                'message' => 'Employee restored successfully.',
                'employee' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'designation' => $user->designation,
                    'phone' => $user->phone,
                    'employee_role' => $user->employee_role,
                    'address' => $user->address ?? 'Not provided',
                    'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                    'deleted_at' => $user->deleted_at ? $user->deleted_at->toISOString() : null,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Employee not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or you are not authorized to restore this employee.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error restoring employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: Unable to restore employee.',
            ], 500);
        }
    }

}
