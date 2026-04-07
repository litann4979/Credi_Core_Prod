<?php

namespace App\Http\Controllers\OpearationController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationsTaskController extends Controller
{
    public function index()
    {
        // Get all team leads and employees created under this operation
        $teamleads = User::where('designation', 'team_lead')
                        ->where('created_by', auth()->id())
                        ->get();

        $employees = User::whereIn('team_lead_id', $teamleads->pluck('id'))->get();

        return view('Opearation.tasks.index', compact('employees', 'teamleads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
           'assigned_date' => 'required|date_format:Y-m-d',
        'due_date' => 'required|date_format:Y-m-d|after:assigned_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'progress' => 'required|integer|min:0|max:100',
            'target_type' => 'required|in:individual,individual_teamlead,all',
            'employees' => 'required_if:target_type,individual|array',
            'employees.*' => 'exists:users,id',
            'teamleads' => 'required_if:target_type,individual_teamlead|array',
            'teamleads.*' => 'exists:users,id',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:2048',
        ]);

        try {
            Log::info('Task request validated successfully.', ['validated' => $validated]);

            // Start transaction
            DB::beginTransaction();

            // Upload attachments
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('task_attachments', 'public');
                    $attachmentPaths[] = $path;
                }
            }

            // Create or update task
            $task = $request->id ? Task::findOrFail($request->id) : new Task();

            $task->operations_id = auth()->id();
            $task->title = $validated['title'];
            $task->description = $validated['description'];
            $task->assigned_date = $validated['assigned_date'];
            $task->due_date = $validated['due_date'];
            $task->priority = $validated['priority'];
            $task->progress = $validated['progress'];
            $task->status = 'pending';
            $task->target_type = $validated['target_type'];

            if (!empty($attachmentPaths)) {
                $task->attachments = json_encode($attachmentPaths);
            }

            $task->save();
            Log::info('Task saved successfully.', ['task_id' => $task->id]);

            // Clear existing notifications and pivot entries
            Notification::where('task_id', $task->id)->delete();
            $task->assignees()->detach();
            $task->assignedUsers()->detach();
            Log::info('Cleared existing notifications and pivot entries.', ['task_id' => $task->id]);

            // Determine recipients and ensure uniqueness
            $recipientIds = [];
            switch ($validated['target_type']) {
                case 'individual':
                    $recipientIds = array_unique($validated['employees'] ?? []);
                    break;
                case 'individual_teamlead':
                    $recipientIds = array_unique($validated['teamleads'] ?? []);
                    break;
                case 'all':
                    $teamLeadIds = User::where('designation', 'team_lead')
                        ->where('created_by', auth()->id())
                        ->pluck('id')
                        ->toArray();

                    $employeeIds = User::whereIn('team_lead_id', $teamLeadIds)
                        ->pluck('id')
                        ->toArray();

                    $recipientIds = array_unique(array_merge($teamLeadIds, $employeeIds));
                    break;
            }

            Log::info('Recipient IDs after deduplication:', ['recipientIds' => $recipientIds]);

            if (empty($recipientIds)) {
                DB::rollBack();
                Log::warning('No recipients found for the task.', ['task_id' => $task->id]);
                return response()->json(['success' => false, 'message' => 'No recipients found for the task.'], 422);
            }

            // Prepare pivot data and create notifications
            $pivotData = [];
             $attachmentPath = $attachmentPaths[0] ?? null;
            foreach ($recipientIds as $userId) {
                // Ensure no duplicate notifications
                if (!Notification::where('user_id', $userId)->where('task_id', $task->id)->exists()) {
                    Notification::create([
                        'user_id' => $userId,
                        'task_id' => $task->id,
                        'message' => 'You have been assigned a task: ' . $task->title,
                        'is_read' => false,
                    ]);

                           // 🔔 Push Notification
            NotificationHelper::sendTaskNotification(
                $userId,
                $task->id,
                'New Task Assigned',
                'You have been assigned a task: ' . $task->title,
                $attachmentPath
            );
                    Log::info('Notification created for user:', [
                        'user_id' => $userId,
                        'task_id' => $task->id,
                        'message' => 'You have been assigned a task: ' . $task->title
                    ]);
                }

                $pivotData[$userId] = [
                    'status' => 'pending',
                    'progress' => 0,
                    'completed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Sync relationships
            $task->assignees()->sync($recipientIds);
            $task->assignedUsers()->sync($pivotData);
            Log::info('Assignees and pivot data synced successfully.', ['task_id' => $task->id, 'assignee_ids' => $recipientIds]);

            DB::commit();
            $action = $request->id ? 'updated' : 'created';
            return response()->json(['success' => true, 'message' => "Task {$action} successfully"]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Operations Task save failed.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Task save failed: ' . $e->getMessage()], 500);
        }
    }


public function getAllTasksForOperations()
{
    $operationsId = auth()->id();
    Log::info('Fetching tasks for operations ID: ' . $operationsId);

    try {
        $tasks = Task::with(['assignees:id,name,profile_photo'])
            ->where('operations_id', $operationsId)
            ->get();

        // 🔁 Decode attachments safely
        $tasks->transform(function ($task) {
            $task->attachments = is_string($task->attachments)
                ? json_decode($task->attachments, true) ?? []
                : ($task->attachments ?? []);
            return $task;
        });

        return response()->json($tasks);
    } catch (Exception $e) {
        Log::error('getAllTasksForOperations failed: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}



// For showing task details
public function show($id)
    {
        try {
            $task = Task::with(['assignedUsers' => function ($query) {
                $query->select('users.id')->withPivot('status', 'message', 'progress');
            }])->findOrFail($id);

            // Decode attachments safely
            $attachments = is_string($task->attachments)
                ? json_decode($task->attachments, true) ?? []
                : ($task->attachments ?? []);

            Log::info('Showing task data', ['task' => $task->toArray(), 'attachments' => $attachments]);

            return response()->json([
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
            Log::error('Failed to fetch task details', [
                'task_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch task details'], 500);
        }
    }

    public function updateTaskStatus(Request $request, $taskId)
    {
        if (!Auth::check()) {
            Log::warning('User not authenticated', ['task_id' => $taskId]);
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
                Log::warning('User not assigned to task', ['task_id' => $taskId, 'user_id' => $userId]);
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

            Log::info('Task status updated in task_user', [
                'task_id' => $taskId,
                'user_id' => $userId,
                'status' => $validated['status'],
                'message' => $validated['message'],
                'progress' => $validated['progress'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Task status updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update task status', [
                'task_id' => $taskId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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

    Log::info("Starting task progress recalculation for Task ID: {$taskId}");
    Log::info("Initial base progress set by team lead: {$baseProgress}%");

    if ($assignedUsers->isEmpty()) {
        Log::info("No assigned users found for task.");
        return;
    }

    $userCount = $assignedUsers->count();
    $remainingProgress = 100 - $baseProgress;
    $perUserShare = $remainingProgress / $userCount;

    Log::info("Total assigned users: {$userCount}");
    Log::info("Remaining progress to distribute among users: {$remainingProgress}%");
    Log::info("Each user's maximum contribution: {$perUserShare}%");

    $totalUserContribution = 0;

    foreach ($assignedUsers as $user) {
        $individualProgress = $user->pivot->progress ?? 0; // User's submitted progress
        $userContribution = ($individualProgress / 100) * $perUserShare;
        $totalUserContribution += $userContribution;

        Log::info("User ID: {$user->id}, individual progress: {$individualProgress}%, calculated contribution: {$userContribution}%");
    }

    $finalProgress = round($baseProgress + $totalUserContribution);
    $finalProgress = min($finalProgress, 100); // Ensure it doesn't exceed 100%

    $task->progress = $finalProgress;
    $task->status = $finalProgress === 100 ? 'completed' : 'in_progress';
    $task->save();

    Log::info("Final calculated progress: {$finalProgress}%");
    Log::info("Task status updated to: {$task->status}");
}

}
