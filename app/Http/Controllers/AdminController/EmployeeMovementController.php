<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\EmployeeMovement;
use App\Models\OfficeRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeMovementController extends Controller
{
    public function index()
    {
        $movements = EmployeeMovement::query()
            ->with(['employee:id,name,email', 'approver:id,name'])
            ->latest()
            ->paginate(15);

        return view('admin.employee_movements.index', compact('movements'));
    }

    public function create()
    {
        $employees = User::query()
            ->where('designation', 'employee')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $rule = OfficeRule::query()->first();
        $defaultAllowedMinutes = (int) ($rule?->work_allowed_minutes ?? 20);

        return view('admin.employee_movements.create', compact('employees', 'defaultAllowedMinutes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('designation', 'employee')->whereNull('deleted_at');
                }),
            ],
            'start_time' => ['required', 'date'],
            'allowed_minutes' => ['required', 'integer', 'min:1'],
        ]);

        EmployeeMovement::create([
            'employee_id' => (int) $validated['employee_id'],
            'type' => 'work',
            'start_time' => $validated['start_time'],
            'allowed_minutes' => (int) $validated['allowed_minutes'],
            'approved_by' => Auth::id(),
            'status' => 'approved',
            'penalty_applied' => false,
        ]);

        return redirect()
            ->route('admin.employee-movements.index')
            ->with('success', 'Work movement assigned successfully.');
    }

    public function show(EmployeeMovement $employeeMovement)
    {
        return redirect()->route('admin.employee-movements.index');
    }

    public function edit(EmployeeMovement $employeeMovement)
    {
        return redirect()->route('admin.employee-movements.index');
    }

    public function update(Request $request, EmployeeMovement $employeeMovement)
    {
        return redirect()->route('admin.employee-movements.index');
    }

    public function destroy(EmployeeMovement $employeeMovement)
    {
        $employeeMovement->delete();

        return redirect()
            ->route('admin.employee-movements.index')
            ->with('success', 'Movement deleted successfully.');
    }
}

