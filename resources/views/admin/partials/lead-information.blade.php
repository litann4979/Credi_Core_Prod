<div class="dashboard-card p-8 mb-8" id="lead-info-section">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">
            <i class="fas fa-users mr-2 text-purple-600"></i>
            Lead Information
        </h2>
    </div>
    <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-6" id="leadFilterForm" onsubmit="applyLeadInfoFilter(event)">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" name="lead_info_search" value="{{ $search }}" placeholder="Search by name or email..." class="form-input w-full">
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            <button type="button" onclick="resetLeadFilters()" class="btn-secondary">
                <i class="fas fa-times mr-2"></i>Reset
            </button>
        </div>
    </form>
    <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6" id="leadFilterForm" onsubmit="applyLeadInfoFilter(event)">
        <!-- Existing filter fields with updated names -->
        <div>
            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <select name="lead_info_status" id="status" class="form-input w-full">
                <option value="" {{ $status === '' ? 'selected' : '' }}>All Statuses</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption }}" {{ $status === $statusOption ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statusOption)) }}</option>
                @endforeach
            </select>
        </div>
        <!-- Other fields updated similarly (e.g., lead_info_state, lead_info_district, etc.) -->
        <div>
            <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State</label>
            <select name="lead_info_state" id="state" class="form-input w-full" onchange="updateDistricts()">
                <option value="">All States</option>
                @foreach ($states as $stateOption)
                    <option value="{{ $stateOption->state_title }}" {{ $state === $stateOption->state_title ? 'selected' : '' }}>{{ $stateOption->state_title }}</option>
                @endforeach
            </select>
        </div>
        <!-- Rest of the fields... -->
        <div class="md:col-span-3 lg:col-span-5 flex items-end space-x-4">
            <button type="submit" class="btn-primary flex-1">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
            <button type="button" onclick="resetLeadFilters()" class="btn-secondary flex-1">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
        </div>
    </form>
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <!-- Existing table structure -->
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>City</th>
                    <th>District</th>
                    <th>State</th>
                    <th>Company</th>
                    <th>Lead Amount</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Lead Type</th>
                    <th>Turnover</th>
                    <th>Bank</th>
                </tr>
            </thead>
            <tbody id="leads-table-body">
                @foreach ($leads as $lead)
                    <tr class="cursor-pointer" onclick="showLeadHistory({{ $lead->id }})">
                        <td class="font-semibold">{{ $lead->name }}</td>
                        <td>{{ $lead->email ?? 'N/A' }}</td>
                        <td>{{ $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $lead->city ?? 'N/A' }}</td>
                        <td>{{ $lead->district ?? 'N/A' }}</td>
                        <td>{{ $lead->state ?? 'N/A' }}</td>
                        <td>{{ $lead->company_name ?? 'N/A' }}</td>
                        <td class="font-semibold text-green-600">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0) }}</td>
                        <td class="font-semibold text-blue-600">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0) }}</td>
                        <td>
                            @php $statusColorClass = match($lead->status) { 'personal_lead' => 'bg-blue-100', 'authorized' => 'bg-emerald-100', 'login' => 'bg-amber-100', 'approved' => 'bg-violet-100', 'rejected' => 'bg-red-100', 'disbursed' => 'bg-cyan-100', 'future_lead' => 'bg-lime-100', default => 'bg-gray-100', }; @endphp
                            <span class="status-badge {{ $statusColorClass }} text-{{ str_replace('bg-', '', $statusColorClass) }}-800">
                                {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                            </span>
                        </td>
                        <td>{{ $lead->lead_type ?? 'N/A' }}</td>
                        <td class="font-semibold text-purple-600">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->turnover_amount ?? 0) }}</td>
                        <td>{{ $lead->bank_name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="leads-pagination-controls" class="pagination-controls"></div>
</div>
