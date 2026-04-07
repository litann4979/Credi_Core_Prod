<?php

namespace App\Http\Controllers\AdminController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTaskController extends Controller
{
    public function index()
    {
        $operations = User::where('designation', 'operations')->get(); // Assuming 'operation' is the designation for operations
        $teamleads = User::where('designation', 'team_lead')->get();
        $employees = User::where('designation', 'employee')->get();

        return view('admin.task.index', compact('employees', 'teamleads', 'operations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_date' => 'required|date',
            'due_date' => 'required|date|after:assigned_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'progress' => 'required|integer|min:0|max:100',
            'target_type' => 'required|in:individual,individual_teamlead,individual_operation,all',
            'employees' => 'required_if:target_type,individual|array',
            'employees.*' => 'exists:users,id',
            'teamleads' => 'required_if:target_type,individual_teamlead|array',
            'teamleads.*' => 'exists:users,id',
            'operations' => 'required_if:target_type,individual_operation|array',
            'operations.*' => 'exists:users,id', // Assuming operations are users with a specific designation
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('task_attachments', 'public');
                    $attachmentPaths[] = $path;
                }
            }

            $task = $request->id ? Task::findOrFail($request->id) : new Task();
            $task->admin_id = auth()->id(); // Assuming admin is logged in
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
            Notification::where('task_id', $task->id)->delete();
            $task->assignees()->detach();
            $task->assignedUsers()->detach();
            $recipientIds = [];
            switch ($validated['target_type']) {
                case 'individual':
                    $recipientIds = array_unique($validated['employees'] ?? []);
                    break;
                case 'individual_teamlead':
                    $recipientIds = array_unique($validated['teamleads'] ?? []);
                    break;
                case 'individual_operation':
                    $recipientIds = array_unique($validated['operations'] ?? []);
                    break;
                case 'all':
                    $employeeIds = User::where('designation', 'employee')->pluck('id')->toArray();
                    $teamLeadIds = User::where('designation', 'team_lead')->pluck('id')->toArray();
                    $operationIds = User::where('designation', 'operations')->pluck('id')->toArray();
                    $recipientIds = array_unique(array_merge($employeeIds, $teamLeadIds, $operationIds));
                    break;
            }
            if (empty($recipientIds)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No recipients found for the task.'], 422);
            }

            $pivotData = [];
            foreach ($recipientIds as $userId) {
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
        $attachmentPaths[0] ?? null
    );
                }

                $pivotData[$userId] = [
                    'status' => 'pending',
                    'progress' => 0,
                    'completed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $task->assignees()->sync($recipientIds);
            $task->assignedUsers()->sync($pivotData);
            DB::commit();
            $action = $request->id ? 'updated' : 'created';
            return response()->json(['success' => true, 'message' => "Task {$action} successfully"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Task save failed: ' . $e->getMessage()], 500);
        }
    }

    public function getAllTasksForAdmin()
    {
        $adminId = auth()->id();
        try {
            $tasks = Task::with(['assignees:id,name,profile_photo'])
                ->where('admin_id', $adminId)
                ->get();

            $tasks->transform(function ($task) {
                $task->attachments = is_string($task->attachments)
                    ? json_decode($task->attachments, true) ?? []
                    : ($task->attachments ?? []);
                return $task;
            });

            return response()->json($tasks);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
