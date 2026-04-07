<div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-8 py-6">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-tasks text-white text-xl"></i>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-white">Task Assignments</h4>
                {{-- <p class="text-orange-100 text-sm font-medium">{{ $task->title }}</p> --}}
            </div>
        </div>
    </div>

    <div class="p-8">
        @if ($taskUsers->isEmpty())
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-slash text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Users Assigned</h3>
                <p class="text-gray-500">This task hasn't been assigned to any users yet.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-gray-200 shadow-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-900 to-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-user"></i>
                                        <span>Assigned User</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-flag"></i>
                                        <span>Status</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Progress</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-comment"></i>
                                        <span>Message</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Completed At</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($taskUsers as $index => $taskUser)
                                <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 transition-all duration-300 {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                    <!-- User Name -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-sm">{{ substr($taskUser->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $taskUser->name }}</p>
                                                <p class="text-xs text-gray-500">Team Member</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5">
                                        @php
                                            $status = $taskUser->pivot->status ?? 'pending';
                                            $statusConfig = [
                                                'completed' => ['bg-green-100', 'text-green-800', 'fas fa-check-circle'],
                                                'in_progress' => ['bg-blue-100', 'text-blue-800', 'fas fa-clock'],
                                                'pending' => ['bg-yellow-100', 'text-yellow-800', 'fas fa-hourglass-half'],
                                                'overdue' => ['bg-red-100', 'text-red-800', 'fas fa-exclamation-triangle'],
                                            ];
                                            $config = $statusConfig[$status] ?? ['bg-gray-100', 'text-gray-800', 'fas fa-question-circle'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $config[0] }} {{ $config[1] }}">
                                            <i class="{{ $config[2] }} mr-1.5 text-xs"></i>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>

                                    <!-- Progress -->
                                    <td class="px-6 py-5">
                                        @php
                                            $progress = $taskUser->pivot->progress ?? 0;
                                            $progressColor = $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : ($progress >= 25 ? 'bg-yellow-500' : 'bg-red-500'));
                                        @endphp
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-1">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner">
                                                    <div class="{{ $progressColor }} h-2.5 rounded-full transition-all duration-500 ease-out shadow-sm" style="width: {{ $progress }}%"></div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="text-sm font-bold text-gray-900">{{ $progress }}%</span>
                                                @if($progress >= 100)
                                                    <i class="fas fa-trophy text-yellow-500 text-sm"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Message -->
                                    <td class="px-6 py-5 max-w-xs">
                                        @if($taskUser->pivot->message)
                                            <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-500">
                                                <p class="text-sm text-gray-700 leading-relaxed">{{ Str::limit($taskUser->pivot->message, 100) }}</p>
                                                @if(strlen($taskUser->pivot->message) > 100)
                                                    <button class="text-xs text-blue-600 hover:text-blue-800 mt-1 font-medium">Read more...</button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex items-center space-x-2 text-gray-400">
                                                <i class="fas fa-minus-circle text-xs"></i>
                                                <span class="text-sm italic">No message</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Completed At -->
                                    <td class="px-6 py-5">
                                        @if($taskUser->pivot->completed_at)
                                            <div class="flex items-center space-x-2">
                                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($taskUser->pivot->completed_at)->format('M d, Y') }}</p>
                                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($taskUser->pivot->completed_at)->format('g:i A') }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center space-x-2 text-gray-400">
                                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                                                </div>
                                                <span class="text-sm italic">Pending</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $totalUsers = $taskUsers->count();
                    $completedUsers = $taskUsers->where('pivot.status', 'completed')->count();
                    $inProgressUsers = $taskUsers->where('pivot.status', 'in_progress')->count();
                    $avgProgress = $taskUsers->avg('pivot.progress') ?? 0;
                @endphp

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-blue-900">{{ $totalUsers }}</p>
                            <p class="text-sm text-blue-700">Total Assigned</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-900">{{ $completedUsers }}</p>
                            <p class="text-sm text-green-700">Completed</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-yellow-900">{{ $inProgressUsers }}</p>
                            <p class="text-sm text-yellow-700">In Progress</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-purple-900">{{ number_format($avgProgress, 1) }}%</p>
                            <p class="text-sm text-purple-700">Avg Progress</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.progress-bar {
    transition: width 0.5s ease-in-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

tbody tr {
    animation: slideInRight 0.3s ease-out forwards;
}

tbody tr:nth-child(1) { animation-delay: 0.1s; }
tbody tr:nth-child(2) { animation-delay: 0.2s; }
tbody tr:nth-child(3) { animation-delay: 0.3s; }
tbody tr:nth-child(4) { animation-delay: 0.4s; }
tbody tr:nth-child(5) { animation-delay: 0.5s; }
</style>
