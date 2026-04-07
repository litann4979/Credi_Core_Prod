<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeRule;
use Illuminate\Http\Request;

class OfficeRuleController extends Controller
{
    public function edit()
    {
        $rule = OfficeRule::first();
        if (! $rule) {
            $rule = OfficeRule::create([]);
        }

        return view('admin.office_rules.edit', compact('rule'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'office_start_time' => ['required', 'date_format:H:i'],
            'office_end_time' => ['required', 'date_format:H:i'],
            'lunch_start' => ['required', 'date_format:H:i'],
            'lunch_end' => ['required', 'date_format:H:i'],
            'lunch_allowed_minutes' => ['required', 'numeric', 'min:0'],
            'break_start' => ['required', 'date_format:H:i'],
            'break_end' => ['required', 'date_format:H:i'],
            'break_allowed_minutes' => ['required', 'numeric', 'min:0'],
            'work_allowed_minutes' => ['required', 'numeric', 'min:0'],
            'geofence_radius' => ['required', 'numeric', 'min:0'],
            'default_score' => ['required', 'numeric'],
            'target_mark' => ['required', 'numeric'],
            'lead_mark' => ['required', 'numeric'],
            'personal_lead_count' => ['required', 'numeric', 'min:0'],
            'late_penalty' => ['required', 'numeric', 'min:0'],
            'late_15min_penalty' => ['required', 'numeric', 'min:0'],
            'unauthorized_outside_penalty' => ['required', 'numeric', 'min:0'],
            'unauthorized_penalty_window_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'extra_break_penalty' => ['required', 'numeric', 'min:0'],
            'extra_lunch_penalty' => ['required', 'numeric', 'min:0'],
            'early_checkout_penalty' => ['required', 'numeric', 'min:0'],
            'work_delay_penalty' => ['required', 'numeric', 'min:0'],
            'penalty_per_minute' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['late_15min_enabled'] = $request->boolean('late_15min_enabled');
        $validated['per_minute_deduction_enabled'] = $request->boolean('per_minute_deduction_enabled');
        $validated['allow_admin_override'] = $request->boolean('allow_admin_override');

        $rule = OfficeRule::first();
        if (! $rule) {
            $rule = OfficeRule::create([]);
        }

        $rule->update($validated);

        return redirect()
            ->route('admin.office_rules.edit')
            ->with('success', 'Office rules updated successfully.');
    }
}
