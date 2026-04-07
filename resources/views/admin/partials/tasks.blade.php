<div class="dashboard-card p-8 mb-8">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">
            <i class="fas fa-tasks mr-2 text-orange-600"></i>
            Tasks Assigned by Admin
        </h2>
    </div>
    <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6" id="taskFilterForm">
        <div>
            <label for="task_date_filter" class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
            <select name="task_date_filter" id="task_date_filter" class="form-input w-full" onchange="toggleTaskDateRange()">
                <option value="30_days" {{ $taskDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="15_days" {{ $taskDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                <option value="7_days" {{ $taskDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="custom" {{ $taskDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div id="task_custom_date_range" class="{{ $taskDateFilter === 'custom' ? '' : 'hidden' }}">
            <label for="task_start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
            <input type="text" name="task_start_date" id="task_start_date" value="{{ $taskStartDate }}" class="form-input w-full flatpickr">
        </div>
        <div id="task_custom_date_range_end" class="{{ $taskDateFilter === 'custom' ? '' : 'hidden' }}">
            <label for="task_end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
            <input type="text" name="task_end_date" id="task_end_date" value="{{ $taskEndDate }}" class="form-input w-full flatpickr">
        </div>
        <div>
            <label for="task_target_type" class="block text-sm font-semibold text-gray-700 mb-2">Target Type</label>
            <select name="task_target_type" id="task_target_type" class="form-input w-full">
                <option value="">All Target Types</option>
                @foreach ($taskTargetTypes as $targetType)
                    <option value="{{ $targetType }}" {{ $taskTargetType === $targetType ? 'selected' : '' }}>{{ $targetType === 'individual' ? 'Individual Employee' : ucfirst(str_replace('_', ' ', $targetType)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4 flex items-end space-x-4">
            <button type="submit" class="btn-primary flex-1">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
            <button type="button" onclick="resetTaskFilters()" class="btn-secondary flex-1">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
        </div>
    </form>
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Target Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Assigned Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody id="tasks-table-body">
                @foreach ($tasks as $task)
                    <tr class="cursor-pointer" onclick="showTaskUsers({{ $task->id }})">
                        <td class="font-semibold">{{ $task->title }}</td>
                        <td>
                            <span class="status-badge bg-purple-100 text-purple-800">
                                {{ ucfirst(str_replace('_', ' ', $task->target_type)) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge
                                @if($task->priority === 'high') bg-red-100 text-red-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge bg-blue-100 text-blue-800">
                                {{ ucfirst($task->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                                </div>
                                <span class="text-sm font-semibold">{{ $task->progress }}%</span>
                            </div>
                        </td>
                        <td>{{ $task->assigned_date ? $task->assigned_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="tasks-pagination-controls" class="pagination-controls"></div>
</div>
