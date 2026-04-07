<div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-history text-white text-xl"></i>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-white">Lead History</h4>
                <p class="text-blue-100 text-sm font-medium">{{ $lead->name }}</p>
            </div>
        </div>
    </div>

    <div class="p-8">
        @if ($histories->isEmpty())
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No History Available</h3>
                <p class="text-gray-500">This lead doesn't have any recorded history yet.</p>
            </div>
        @else
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 via-indigo-500 to-purple-500"></div>

                <div class="space-y-8">
                    @foreach ($histories as $index => $history)
                        <div class="relative flex items-start space-x-6">
                            <!-- Timeline Dot -->
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg ring-4 ring-white">
                                    @if($history->action === 'created')
                                        <i class="fas fa-plus text-white text-sm"></i>
                                    @elseif($history->action === 'updated')
                                        <i class="fas fa-edit text-white text-sm"></i>
                                    @elseif($history->action === 'forwarded')
                                        <i class="fas fa-share text-white text-sm"></i>
                                    @elseif($history->action === 'approved')
                                        <i class="fas fa-check text-white text-sm"></i>
                                    @elseif($history->action === 'rejected')
                                        <i class="fas fa-times text-white text-sm"></i>
                                    @else
                                        <i class="fas fa-circle text-white text-xs"></i>
                                    @endif
                                </div>
                                @if($index === 0)
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white animate-pulse"></div>
                                @endif
                            </div>

                            <!-- Content Card -->
                            <div class="flex-1 min-w-0">
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                                @if($history->action === 'created') bg-green-100 text-green-800
                                                @elseif($history->action === 'updated') bg-blue-100 text-blue-800
                                                @elseif($history->action === 'forwarded') bg-purple-100 text-purple-800
                                                @elseif($history->action === 'approved') bg-emerald-100 text-emerald-800
                                                @elseif($history->action === 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($history->action) }}
                                            </span>
                                            @if($history->status)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                                    {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $history->created_at->format('M d, Y') }}
                                        </div>
                                    </div>

                                    <!-- Details Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 uppercase tracking-wide">Performed By</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $history->user->name ?? 'System' }}</p>
                                            </div>
                                        </div>

                                        @if($history->forwardedTo)
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-arrow-right text-purple-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Forwarded To</p>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $history->forwardedTo->name }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Comments -->
                                    @if($history->comments)
                                        <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-blue-500">
                                            <div class="flex items-start space-x-3">
                                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                                                    <i class="fas fa-comment text-blue-600 text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Comment</p>
                                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $history->comments }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Timestamp -->
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ $history->created_at->format('l, F j, Y') }}</span>
                                            <span>{{ $history->created_at->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.history-item {
    animation: fadeInUp 0.5s ease-out forwards;
}

.history-item:nth-child(1) { animation-delay: 0.1s; }
.history-item:nth-child(2) { animation-delay: 0.2s; }
.history-item:nth-child(3) { animation-delay: 0.3s; }
.history-item:nth-child(4) { animation-delay: 0.4s; }
.history-item:nth-child(5) { animation-delay: 0.5s; }
</style>
