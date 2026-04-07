<div class="dashboard-card form-card p-8 mb-8" id="leads-filter-section">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">Leads Filter</h2>
    </div>
    <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6" id="tableFilterForm" onsubmit="applyLeadsFilter(event)">
        <!-- Existing form fields remain unchanged -->
        <div>
            <label for="table_date_filter" class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
            <select name="table_date_filter" id="table_date_filter" class="form-input w-full" onchange="toggleTableDateRange()">
                <option value="30_days" {{ $tableDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="15_days" {{ $tableDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                <option value="7_days" {{ $tableDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="custom" {{ $tableDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div id="table_custom_date_range" class="{{ $tableDateFilter === 'custom' ? '' : 'hidden' }}">
            <label for="table_start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
            <input type="text" name="table_start_date" id="table_start_date" value="{{ $tableStartDate }}" class="form-input w-full flatpickr">
        </div>
        <div id="table_custom_date_range_end" class="{{ $tableDateFilter === 'custom' ? '' : 'hidden' }}">
            <label for="table_end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
            <input type="text" name="table_end_date" id="table_end_date" value="{{ $tableEndDate }}" class="form-input w-full flatpickr">
        </div>
        <div class="md:col-span-3 flex items-end space-x-4">
            <button type="submit" class="btn-primary flex-1">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
            <button type="button" onclick="resetTableFilters()" class="btn-secondary flex-1">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
        </div>
    </form>
</div>
<div id="leads-overview-section">
    @include('admin.partials.leads-overview')
</div>
