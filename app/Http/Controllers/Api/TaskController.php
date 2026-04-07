<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * List tasks for employee or team lead.
     * - Employee sees their own tasks.
     * - Team lead sees all tasks assigned by them, or their team.
     */
public function index(Request $request): JsonResponse
{
    $userId = auth()->id();

    $tasks = Task::whereHas('assignedUsers', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->with(['assignedUsers' => function ($query) use ($userId) {
            $query->select('users.id')
                  ->where('users.id', $userId)
                  ->withPivot('status', 'message', 'progress', 'completed_at', 'created_at', 'updated_at', 'task_id', 'user_id');
        }])
         ->orderBy('created_at', 'desc')
        ->get(['id', 'title', 'description', 'assigned_date', 'due_date', 'priority', 'attachments'])
        ->map(function ($task) {
            $user = $task->assignedUsers->first(); // only the logged-in user

            return [
                'title'        => $task->title,
                'description'  => $task->description,
                'assigned_date'=> $task->assigned_date,
                'due_date'     => $task->due_date,
                'priority'     => $task->priority,
                'attachments'  => $task->attachments,
                'task_id'      => $task->id,
                'user_id'      => $user->id,
                'status'       => $user->pivot->status,
                'progress'     => $user->pivot->progress,
                'completed_at' => $user->pivot->completed_at,
                'created_at'   => $user->pivot->created_at,
                'updated_at'   => $user->pivot->updated_at,
                'message'      => $user->pivot->message,
            ];
        });

    return response()->json($tasks);
}



    /**
     * Store a new task.
     * Team lead can assign to individual (employee_id) or to whole team (no employee_id, will assign to all team members).
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($user->designation !== 'team_lead') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only team leads can create tasks.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'progress' => 'nullable|integer|min:0|max:100',
            'priority' => 'nullable|string|in:low,medium,high',
            'assigned_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:assigned_date',
            'attachments' => 'nullable|string|max:1024',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Assign to all team members if employee_id not provided
        if (!$request->filled('employee_id')) {
            $teamMembers = User::where('team_lead_id', $user->id)->get();
            $tasks = [];
            foreach ($teamMembers as $member) {
                $task = Task::create([
                    'team_lead_id' => $user->id,
                    'employee_id' => $member->id,
                    'title' => $request->title,
                    'progress' => $request->progress ?? 0,
                    'priority' => $request->priority,
                    'assigned_date' => $request->assigned_date ?? now(),
                    'due_date' => $request->due_date,
                    'attachments' => $request->attachments,
                    'description' => $request->description,
                    'status' => $request->status,
                    'activity_timeline' => json_encode([
                        [
                            'timestamp' => now()->toDateTimeString(),
                            'action' => 'created',
                            'by' => $user->id,
                            'note' => 'Task assigned to employee'
                        ]
                    ]),
                ]);
                $tasks[] = $task;
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Task assigned to all team members.',
                'data' => $tasks,
            ], 201);
        }

        // Assign to one employee
        $task = Task::create([
            'team_lead_id' => $user->id,
            'employee_id' => $request->employee_id,
            'title' => $request->title,
            'progress' => $request->progress ?? 0,
            'priority' => $request->priority,
            'assigned_date' => $request->assigned_date ?? now(),
            'due_date' => $request->due_date,
            'attachments' => $request->attachments,
            'description' => $request->description,
            'status' => $request->status,
            'activity_timeline' => json_encode([
                [
                    'timestamp' => now()->toDateTimeString(),
                    'action' => 'created',
                    'by' => $user->id,
                    'note' => 'Task assigned'
                ]
            ]),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    /**
     * Show a single task.
     * Employee can see only their task. Team lead can see tasks they assigned.
     */
  public function show($id)
{
    try {
        $task = Task::with(['assignedUsers' => function ($query) {
            $query->select('users.id')->withPivot('status', 'message', 'progress');
        }])->find($id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }

        // Decode attachments safely
        $attachments = is_string($task->attachments)
            ? (json_decode($task->attachments, true) ?? [])
            : ($task->attachments ?? []);

        return response()->json([
            'success' => true,
            'title' => $task->title,
            'description' => $task->description,
            'assigned_date' => $task->assigned_date,
            'due_date' => $task->due_date,
            'priority' => $task->priority,
            'attachments' => $attachments,
            'assigned_users' => $task->assignedUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'pivot' => [
                        'status' => $user->pivot->status,
                        'message' => $user->pivot->message,
                        'progress' => $user->pivot->progress
                    ]
                ];
            })
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch task details',
            'error' => $e->getMessage() // 👈 temporary debug
        ], 500);
    }
}


    /**
     * Update a task.
     * Employee: can change progress, status, description.
     * Team lead: can edit all fields except employee_id if bulk.
     */
    public function update(Request $request, $taskId){
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'message' => 'nullable|string|max:500',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        try {
            $task = Task::findOrFail($taskId);
            $userId = Auth::id();

            DB::beginTransaction();

            $pivot = $task->assignedUsers()->wherePivot('user_id', $userId)->first();
            if (!$pivot) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'User not assigned to this task'], 400);
            }

            $task->assignedUsers()->updateExistingPivot($userId, [
                'status' => $validated['status'],
                'message' => $validated['message'],
                'progress' => $validated['progress'],
                'updated_at' => now(),
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

            // 👉 Call both update methods here
        $this->recalculateTaskProgressWithWeightedUserShare($taskId);
        $this->updateOverallTaskStatus($taskId);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Task status updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update task status: ' . $e->getMessage()], 500);
        }
    }


       protected function updateOverallTaskStatus($taskId)
{
    $task = Task::with('assignedUsers')->findOrFail($taskId);

    $assignedUsers = $task->assignedUsers;

    if ($assignedUsers->isEmpty()) {
        return;
    }

    $total = $assignedUsers->count();
    $completed = $assignedUsers->where('pivot.status', 'completed')->count();
    $inProgress = $assignedUsers->where('pivot.status', 'in_progress')->count();

    // Set overall task status
    if ($completed === $total) {
        $task->status = 'completed';
    } elseif ($inProgress > 0 || $completed > 0) {
        $task->status = 'in_progress';
    } else {
        $task->status = 'pending';
    }


    $task->save();
}


protected function recalculateTaskProgressWithWeightedUserShare($taskId)
{
    $task = Task::with('assignedUsers')->findOrFail($taskId);

    $baseProgress = $task->progress ?? 0; // Team lead's initial progress
    $assignedUsers = $task->assignedUsers;
    if ($assignedUsers->isEmpty()) {
        return;
    }

    $userCount = $assignedUsers->count();
    $remainingProgress = 100 - $baseProgress;
    $perUserShare = $remainingProgress / $userCount;
    $totalUserContribution = 0;

    foreach ($assignedUsers as $user) {
        $individualProgress = $user->pivot->progress ?? 0; // User's submitted progress
        $userContribution = ($individualProgress / 100) * $perUserShare;
        $totalUserContribution += $userContribution;
    }

    $finalProgress = round($baseProgress + $totalUserContribution);
    $finalProgress = min($finalProgress, 100); // Ensure it doesn't exceed 100%

    $task->progress = $finalProgress;
    $task->status = $finalProgress === 100 ? 'completed' : 'in_progress';
    $task->save();
}

    /**
     * Delete a task (team lead only).
     */
    public function destroy(Task $task): JsonResponse
    {
        $user = Auth::user();
        if ($user->designation !== 'team_lead' || $user->id !== $task->team_lead_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only the team lead can delete this task.',
            ], 403);
        }
        $task->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully.',
        ], 200);
    }
}
