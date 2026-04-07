<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TargetController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');

        $targets = Target::query()
            ->with('user')
            ->when($type && in_array($type, ['lead', 'attendance', 'leave'], true), function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.target.index', compact('targets', 'type'));
    }

    public function create()
    {
        return view('admin.target.create', $this->usersForForm());
    }

    public function store(Request $request)
    {
        $data = $this->validatedPayload($request);

        Target::create($data);

        return redirect()
            ->route('admin.targets.index')
            ->with('success', 'Target created successfully.');
    }

    public function edit(Target $target)
    {
        $target->load('user');

        return view('admin.target.edit', array_merge(
            $this->usersForForm(),
            ['target' => $target]
        ));
    }

    public function update(Request $request, Target $target)
    {
        $data = $this->validatedPayload($request);

        $target->update($data);

        return redirect()
            ->route('admin.targets.index')
            ->with('success', 'Target updated successfully.');
    }

    public function destroy(Target $target)
    {
        $target->delete();

        return redirect()
            ->route('admin.targets.index')
            ->with('success', 'Target deleted successfully.');
    }

    /**
     * @return array{employees: \Illuminate\Database\Eloquent\Collection, team_leads: \Illuminate\Database\Eloquent\Collection}
     */
    private function usersForForm(): array
    {
        return [
            'employees' => User::query()
                ->where('designation', 'employee')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'team_leads' => User::query()
                ->where('designation', 'team_lead')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        $request->validate([
            'user_id_employee' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('designation', 'employee')->whereNull('deleted_at');
                }),
            ],
            'user_id_team_lead' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('designation', 'team_lead')->whereNull('deleted_at');
                }),
            ],
            'type' => ['required', Rule::in(['lead', 'attendance', 'leave'])],
            'target_value' => ['required', 'integer', 'min:0'],
            'achieved_value' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $hasEmployee = filled($request->input('user_id_employee'));
        $hasTeamLead = filled($request->input('user_id_team_lead'));

        if (! $hasEmployee && ! $hasTeamLead) {
            throw ValidationException::withMessages([
                'user_id_employee' => 'Please select either an employee or a team lead.',
            ]);
        }

        if ($hasEmployee && $hasTeamLead) {
            throw ValidationException::withMessages([
                'user_id_employee' => 'Select only one assignee: either an employee or a team lead.',
            ]);
        }

        $userId = $hasEmployee
            ? (int) $request->input('user_id_employee')
            : (int) $request->input('user_id_team_lead');

        return [
            'user_id' => $userId,
            'type' => $request->input('type'),
            'target_value' => (int) $request->input('target_value'),
            'achieved_value' => (int) ($request->input('achieved_value') ?? 0),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'is_completed' => $request->boolean('is_completed'),
        ];
    }
}
