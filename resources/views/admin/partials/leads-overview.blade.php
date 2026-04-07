<div class="mb-8">
    <div class="card-header p-6 -mb-6 rounded-b-none">
        <h2 class="section-title mb-0">{{ $tableDateFilter === '30_days' || $tableDateFilter === '15_days' || $tableDateFilter === '7_days' ? 'This Month Leads Overview' : 'Leads Overview' }}</h2>
    </div>
    <div class="dashboard-card p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Leads Card -->
        <div class="bg-blue-50 p-6 rounded-xl shadow-sm flex flex-col items-center justify-center text-center">
            <i class="fas fa-users text-blue-600 text-3xl mb-3"></i>
            <h3 class="text-xl font-bold text-blue-800">Total Leads</h3>
            <p class="text-3xl font-extrabold text-blue-900 mt-2">{{ array_sum(array_column($leadsByStatus, 'count')) }}</p>
            <p class="text-xl font-bold text-blue-800 mt-1">{{ \App\Helpers\FormatHelper::formatToIndianCurrency(array_sum(array_column($leadsByStatus, 'total_valuation'))) }}</p>
        </div>
        <!-- Leads by Status Cards -->
        @foreach (['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed', 'future_lead'] as $status)
            @php
                $count = $leadsByStatus[$status]['count'] ?? 0;
                $valuation = $leadsByStatus[$status]['total_valuation'] ?? 0;
                $statusName = ucfirst(str_replace('_', ' ', $status));
                $colorClass = match($status) {
                    'personal_lead' => 'bg-blue-50 text-blue-800',
                    'authorized' => 'bg-emerald-50 text-emerald-800',
                    'login' => 'bg-amber-50 text-amber-800',
                    'approved' => 'bg-violet-50 text-violet-800',
                    'rejected' => 'bg-red-50 text-red-800',
                    'disbursed' => 'bg-cyan-50 text-cyan-800',
                    'future_lead' => 'bg-lime-50 text-lime-800',
                    default => 'bg-gray-50 text-gray-800',
                };
                $iconClass = match($status) {
                    'personal_lead' => 'fas fa-user-plus',
                    'authorized' => 'fas fa-check-circle',
                    'login' => 'fas fa-sign-in-alt',
                    'approved' => 'fas fa-thumbs-up',
                    'rejected' => 'fas fa-times-circle',
                    'disbursed' => 'fas fa-money-bill-wave',
                    'future_lead' => 'fas fa-clock',
                    default => 'fas fa-info-circle',
                };
            @endphp
            @if($count > 0 || $valuation > 0)
                <div class="{{ $colorClass }} p-6 rounded-xl shadow-sm flex flex-col items-center justify-center text-center">
                    <i class="{{ $iconClass }} text-3xl mb-3"></i>
                    <h3 class="text-xl font-bold">{{ $statusName }}</h3>
                    <p class="text-2xl font-extrabold mt-2">{{ $count }}</p>
                    <p class="text-lg font-semibold mt-1">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($valuation) }}</p>
                </div>
            @endif
        @endforeach
    </div>
</div>
