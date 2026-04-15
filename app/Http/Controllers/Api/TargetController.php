<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->query('type');
        $activeOnly = $request->boolean('active_only', false);
        $month = $request->query('month'); // Expected format: YYYY-MM

        $targetsQuery = Target::query()
            ->where('user_id', $user->id)
            ->when($type && in_array($type, ['lead', 'attendance', 'leave'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($activeOnly, function ($query) {
                $today = now()->toDateString();
                $query->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })
            ->orderByDesc('start_date');

        if (filled($month)) {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', (string) $month);
            } catch (\Throwable $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid month format. Use YYYY-MM.',
                ], 422);
            }

            $targetsQuery->whereYear('start_date', $monthDate->year)
                ->whereMonth('start_date', $monthDate->month);
        }

        $targets = $targetsQuery
            ->get()
            ->map(function (Target $target) {
                $progress = (int) $target->target_value > 0
                    ? round(((int) $target->achieved_value / (int) $target->target_value) * 100, 2)
                    : 0.0;

                return [
                    'id' => $target->id,
                    'type' => $target->type,
                    'target_value' => (int) $target->target_value,
                    'achieved_value' => (int) $target->achieved_value,
                    'progress_percent' => min(100, max(0, $progress)),
                    'start_date' => optional($target->start_date)->toDateString(),
                    'end_date' => optional($target->end_date)->toDateString(),
                    'is_completed' => (bool) $target->is_completed,
                    'created_at' => optional($target->created_at)->toISOString(),
                ];
            });

        return response()->json([
            'status' => true,
            'targets' => $targets,
        ]);
    }

    public function show(Request $request, Target $target): JsonResponse
    {
        if ((int) $target->user_id !== (int) $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Target not found.',
            ], 404);
        }

        $progress = (int) $target->target_value > 0
            ? round(((int) $target->achieved_value / (int) $target->target_value) * 100, 2)
            : 0.0;

        return response()->json([
            'status' => true,
            'target' => [
                'id' => $target->id,
                'type' => $target->type,
                'target_value' => (int) $target->target_value,
                'achieved_value' => (int) $target->achieved_value,
                'progress_percent' => min(100, max(0, $progress)),
                'start_date' => optional($target->start_date)->toDateString(),
                'end_date' => optional($target->end_date)->toDateString(),
                'is_completed' => (bool) $target->is_completed,
                'created_at' => optional($target->created_at)->toISOString(),
                'updated_at' => optional($target->updated_at)->toISOString(),
            ],
        ]);
    }
}
