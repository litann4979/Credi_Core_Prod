<?php

namespace App\Http\Controllers\AdminController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        return view('admin.Target.index', compact('targets', 'type'));
    }

    public function create()
    {
        return view('admin.Target.create', $this->usersForForm());
    }

    public function store(Request $request)
    {
        $data = $this->validatedPayload($request);

        $target = Target::create($data);
        $this->notifyTargetAssigned($target, false);

        return redirect()
            ->route('admin.targets.index')
            ->with('success', 'Target created successfully.');
    }

    public function edit(Target $target)
    {
        $target->load('user');

        return view('admin.Target.edit', array_merge(
            $this->usersForForm(),
            ['target' => $target]
        ));
    }

    public function update(Request $request, Target $target)
    {
        $data = $this->validatedPayload($request);

        $target->update($data);
        $target->refresh();
        $this->notifyTargetAssigned($target, true);

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
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        $request->validate([
            'user_id_employee' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('designation', 'employee')->whereNull('deleted_at');
                }),
            ],
            'type' => ['required', Rule::in(['lead', 'attendance', 'leave'])],
            'target_value' => ['required', 'integer', 'min:0'],
            'achieved_value' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $userId = (int) $request->input('user_id_employee');

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

    private function notifyTargetAssigned(Target $target, bool $isUpdate): void
    {
        $title = $isUpdate ? 'Target updated' : 'New target assigned';
        $message = sprintf(
            '%s target: %d | From %s to %s',
            ucfirst($target->type),
            (int) $target->target_value,
            $target->start_date?->format('d M Y') ?? '-',
            $target->end_date?->format('d M Y') ?? '-'
        );

        NotificationHelper::sendTargetNotification(
            (int) $target->user_id,
            (int) $target->id,
            $title,
            $message
        );
    }
}
