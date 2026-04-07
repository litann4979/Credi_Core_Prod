<?php

namespace App\Http\Controllers\HrController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class HrPasswordController extends Controller
{
     public function edit()
    {
        return view('hr.password.edit', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Update the admin's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // Log the request data for debugging
        Log::debug('Password update request received', [
            'user_id' => $request->user()->id,
            'input' => $request->except(['password', 'password_confirmation', 'current_password'])
        ]);

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            // Custom error messages
            'current_password.required' => 'Please enter your current password.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least :min characters.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must include at least one number.',
            'password.symbols' => 'Password must include at least one special character (@, #, $, etc).',
        ]);

        /** @var User $user */
        $user = $request->user();

        Log::info('Password update attempt for user ID: ' . $user->id);

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        Log::info('Password updated successfully for user ID: ' . $user->id);

        return back()->with('status', 'Password updated successfully!');
    }
}
