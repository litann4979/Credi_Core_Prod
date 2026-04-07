<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #f8fafc;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --info-color: #06b6d4;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .main-container {
            min-height: 100vh;
            padding: 4rem 2rem;
            padding-left: 239px
        }

        .content-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
            margin: 0 auto;
            max-width: 1800px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .report-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border-left: 4px solid;
        }

        .report-section.lead-valuation {
            border-left-color: var(--success-color);
        }

        .report-section.lead-info {
            border-left-color: var(--primary-color);
        }

        .report-section.task-report {
            border-left-color: var(--warning-color);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .filter-container {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem;
            transition: border-color 0.2s;
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 12px -3px rgba(16, 185, 129, 0.4);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
            min-width: 0;
            border: 1px solid #e5e7eb;
        }

        .table {
            margin: 0;
            font-size: 0.875rem;
        }

        .table th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 600;
            color: var(--dark-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: #f1f5f9;
            white-space: nowrap;
            text-transform: capitalize; /* Capitalize table data */
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-personal-lead {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-authorized {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-login {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-disbursed {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .status-future-lead {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .priority-high {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .priority-medium {
            background-color: #fef3c7;
            color: #92400e;
        }

        .priority-low {
            background-color: #dcfce7;
            color: #166534;
        }

        .task-status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .task-status-in-progress {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .task-status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .progress-bar {
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
        }

        .amount-text {
            font-family: 'Monaco', 'Menlo', monospace;
            font-weight: 600;
            color: var(--success-color);
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .export-btn:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 8px 12px -3px rgba(16, 185, 129, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card.total {
            border-left-color: var(--primary-color);
        }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                margin: 1rem;
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.875rem;
            }

            .table-container {
                overflow-x: auto;
            }
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #e5e7eb;
            color: var(--primary-color);
            font-size: 0.875rem;
        }

        .pagination .page-link:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .pagination svg {
            width: 20px;
            height: 20px;
            max-width: 20px;
            max-height: 20px;
            vertical-align: middle;
        }
        #leadInfoTable td {
    text-transform: uppercase;
}

#tasksTable td {
    text-transform: uppercase;
}
    </style>
</head>
<body>
    @include('admin.Components.sidebar')

    <div class="main-container">
        <div class="container-fluid">
            @include('admin.Components.header')

            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="page-header">
                    <h1>
                        <i class="fas fa-chart-line"></i>
                        Admin Reports
                    </h1>
                    <p class="mb-0 opacity-90">Comprehensive reporting dashboard for leads, tasks, and analytics</p>
                </div>

                <!-- Lead Valuation Report -->
                <div class="report-section lead-valuation">
                    <h2 class="section-title">
                        <i class="fas fa-chart-bar text-success"></i>
                        Lead Valuation Report
                    </h2>

                    <div class="filter-container">
                        <form method="GET" action="{{ route('admin.report') }}" id="tableFilterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="table_date_filter" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Date Range
                                    </label>
                                    <select name="table_date_filter" id="table_date_filter" class="form-select" onchange="toggleTableDateRange()">
                                        <option value="30_days" {{ $tableDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                        <option value="15_days" {{ $tableDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                                        <option value="7_days" {{ $tableDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                        <option value="custom" {{ $tableDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
                                    </select>
                                </div>

                                <div id="table_custom_date_range" class="col-md-3 {{ $tableDateFilter === 'custom' ? '' : 'd-none' }}">
                                    <label for="table_start_date" class="form-label">
                                        <i class="fas fa-calendar-day me-1"></i>Start Date
                                    </label>
                                    <input type="text" name="table_start_date" id="table_start_date" value="{{ $tableStartDate }}" class="form-control flatpickr">
                                </div>

                                <div id="table_custom_date_range_end" class="col-md-3 {{ $tableDateFilter === 'custom' ? '' : 'd-none' }}">
                                    <label for="table_end_date" class="form-label">
                                        <i class="fas fa-calendar-day me-1"></i>End Date
                                    </label>
                                    <input type="text" name="table_end_date" id="table_end_date" value="{{ $tableEndDate }}" class="form-control flatpickr">
                                </div>

                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>Apply Filters
                                    </button>
                                    <button type="button" onclick="resetTableFilters()" class="btn btn-secondary">
                                        <i class="fas fa-undo me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                    <th><i class="fas fa-hashtag me-1"></i>Count</th>
                                    <th><i class="fas fa-rupee-sign me-1"></i>Total Valuation</th>
                                    <th><i class="fas fa-download me-1"></i>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed', 'future_lead'] as $status)
                                    <tr>
                                        <td>
                                            <span class="status-badge status-{{ str_replace('_', '-', $status) }}">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $leadsByStatus[$status]['count'] ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="amount-text">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($leadsByStatus[$status]['total_valuation'] ?? 0) }}</span>
                                        </td>
                                        <td>
<a href="{{ route('admin.export.lead', $status) }}?table_date_filter={{ $tableDateFilter }}&table_start_date={{ $tableStartDate }}&table_end_date={{ $tableEndDate }}" class="export-btn">
    <i class="fas fa-download"></i>Export
</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Lead Information Report -->
                <div class="report-section lead-info">
    <h2 class="section-title">
        <i class="fas fa-users text-primary"></i>
        Lead Information Report
    </h2>

    <div class="filter-container">
        <form method="GET" action="{{ route('admin.report') }}" id="leadFilterForm">
            <input type="hidden" name="section" value="lead_info"> 
            
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" id="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Search by Name, Email, or Phone...">
                    </div>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" {{ $status === '' ? 'selected' : '' }}>All Statuses</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="state" class="form-label">State</label>
                    <select name="state" id="state" class="form-select" onchange="updateDistricts()">
                        <option value="">All States</option>
                        @foreach ($states as $s)
                            <option value="{{ $s }}" {{ $state === $s ? 'selected' : '' }}>{{ ucwords($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="district" class="form-label">District</label>
                    <select name="district" id="district" class="form-select" onchange="updateCities()">
                        <option value="">All Districts</option>
                        @foreach ($districts as $d)
                            <option value="{{ $d }}" {{ $district === $d ? 'selected' : '' }}>{{ ucwords($d) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="city" class="form-label">City</label>
                    <select name="city" id="city" class="form-select">
                        <option value="">All Cities</option>
                        @foreach ($cities as $c)
                            <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ ucwords($c) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="lead_type" class="form-label">Lead Type</label>
                    <select name="lead_type" id="lead_type" class="form-select">
                        <option value="">All Lead Types</option>
                        @foreach ($leadTypes as $lt)
                            <option value="{{ $lt }}" {{ $leadType === $lt ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $lt)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="lead_date_filter" class="form-label">Date Range</label>
                    <select name="lead_date_filter" id="lead_date_filter" class="form-select" onchange="toggleLeadDateRange()">
                        <option value="" {{ $leadDateFilter === '' ? 'selected' : '' }}>All Time</option>
                        <option value="60_days" {{ $leadDateFilter === '60_days' ? 'selected' : '' }}>Last 60 Days</option>
                        <option value="30_days" {{ $leadDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="custom" {{ $leadDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div id="lead_custom_date_range" class="col-md-2 {{ $leadDateFilter === 'custom' ? '' : 'd-none' }}">
                    <label for="lead_start_date" class="form-label">Start Date</label>
                    <input type="text" name="lead_start_date" id="lead_start_date" value="{{ $leadStartDate }}" class="form-control flatpickr">
                </div>

                <div id="lead_custom_date_range_end" class="col-md-2 {{ $leadDateFilter === 'custom' ? '' : 'd-none' }}">
                    <label for="lead_end_date" class="form-label">End Date</label>
                    <input type="text" name="lead_end_date" id="lead_end_date" value="{{ $leadEndDate }}" class="form-control flatpickr">
                </div>

                <div class="col-md-2">
                    <label for="operation_id" class="form-label">Operation</label>
                    <select name="operation_id" id="operation_id" class="form-select" onchange="updateTeamLeads()">
                        <option value="">All Operations</option>
                        @foreach ($operations as $op)
                            <option value="{{ $op->id }}" {{ $operationId == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="team_lead_id" class="form-label">Team Lead</label>
                    <select name="team_lead_id" id="team_lead_id" class="form-select" onchange="updateEmployees()">
                        <option value="">All Team Leads</option>
                        @foreach ($teamLeads as $tl)
                            <option value="{{ $tl->id }}" {{ $teamLeadId == $tl->id ? 'selected' : '' }}>{{ $tl->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select name="employee_id" id="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                    <button type="button" onclick="resetLeadFilters()" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i>Reset
                    </button>
                    
                    <a href="{{ route('admin.export.lead.info', request()->query()) }}" class="btn btn-success ms-auto">
                        <i class="fas fa-download me-1"></i>Export Filtered
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover" id="leadInfoTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-user me-1"></i>Name</th>
                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                        <th><i class="fas fa-birthday-cake me-1"></i>DOB</th>
                        <th><i class="fas fa-city me-1"></i>City</th>
                        <th><i class="fas fa-map me-1"></i>District</th>
                        <th><i class="fas fa-map-marker-alt me-1"></i>State</th>
                        <th><i class="fas fa-building me-1"></i>Company</th>
                        <th><i class="fas fa-rupee-sign me-1"></i>Lead Amount</th>
                        <th><i class="fas fa-money-bill me-1"></i>Salary</th>
                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                        <th><i class="fas fa-tag me-1"></i>Lead Type</th>
                        <th><i class="fas fa-chart-line me-1"></i>Turnover</th>
                        <th><i class="fas fa-university me-1"></i>Bank</th>
                        <th><i class="fas fa-download me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody id="leadInfoTableBody">
                    @foreach ($leads as $lead)
                        <tr>
                            <td><div class="fw-bold">{{ ucwords($lead->name) }}</div></td>
                            <td>{{ $lead->email ?? 'N/A' }}</td>
                            <td>{{ $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ ucwords($lead->city ?? 'N/A') }}</td>
                            <td>{{ ucwords($lead->district ?? 'N/A') }}</td>
                            <td>{{ ucwords($lead->state ?? 'N/A') }}</td>
                            <td>{{ ucwords($lead->company_name ?? 'N/A') }}</td>
                            <td><span class="amount-text">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0) }}</span></td>
                            <td><span class="amount-text">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0) }}</span></td>
                            <td>
                                <span class="status-badge status-{{ str_replace('_', '-', $lead->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                </span>
                            </td>
                            <td>{{ ucwords(str_replace('_', ' ', $lead->lead_type ?? 'N/A')) }}</td>
                            <td><span class="amount-text">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->turnover_amount ?? 0) }}</span></td>
                            <td>{{ ucwords($lead->bank_name ?? 'N/A') }}</td>
                            <td>
                                <a href="{{ route('admin.export.lead.info') }}?id={{ $lead->id }}" class="export-btn">
                                    <i class="fas fa-download"></i>Export
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

                <!-- Task Report -->
                <div class="report-section task-report">
                    <h2 class="section-title">
                        <i class="fas fa-tasks text-warning"></i>
                        Task Report
                    </h2>

                    <div class="filter-container">
                        <form method="GET" action="{{ route('admin.report') }}" id="taskFilterForm">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label for="task_date_filter" class="form-label">Date Range</label>
                                    <select name="task_date_filter" id="task_date_filter" class="form-select" onchange="toggleTaskDateRange()">
                                        <option value="30_days" {{ $taskDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                        <option value="15_days" {{ $taskDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                                        <option value="7_days" {{ $taskDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                        <option value="custom" {{ $taskDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
                                    </select>
                                </div>

                                <div id="task_custom_date_range" class="col-md-2 {{ $taskDateFilter === 'custom' ? '' : 'd-none' }}">
                                    <label for="task_start_date" class="form-label">Start Date</label>
                                    <input type="text" name="task_start_date" id="task_start_date" value="{{ $taskStartDate }}" class="form-control flatpickr">
                                </div>

                                <div id="task_custom_date_range_end" class="col-md-2 {{ $taskDateFilter === 'custom' ? '' : 'd-none' }}">
                                    <label for="task_end_date" class="form-label">End Date</label>
                                    <input type="text" name="task_end_date" id="task_end_date" value="{{ $taskEndDate }}" class="form-control flatpickr">
                                </div>

                                <div class="col-md-2">
                                    <label for="task_target_type" class="form-label">Target Type</label>
                                    <select name="task_target_type" id="task_target_type" class="form-select">
                                        <option value="" {{ $taskTargetType === '' ? 'selected' : '' }}>All Target Types</option>
                                        @foreach ($taskTargetTypes as $tt)
                                            <option value="{{ $tt }}" {{ $taskTargetType === $tt ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $tt)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="task_operation_id" class="form-label">Operation</label>
                                    <select name="task_operation_id" id="task_operation_id" class="form-select" onchange="updateTaskTeamLeads()">
                                        <option value="" {{ $taskOperationId === '' ? 'selected' : '' }}>All Operations</option>
                                        @foreach ($operations as $op)
                                            <option value="{{ $op->id }}" {{ $taskOperationId == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="task_team_lead_id" class="form-label">Team Lead</label>
                                    <select name="task_team_lead_id" id="task_team_lead_id" class="form-select" onchange="updateTaskEmployees()">
                                        <option value="" {{ $taskTeamLeadId === '' ? 'selected' : '' }}>All Team Leads</option>
                                        @foreach ($teamLeads as $tl)
                                            <option value="{{ $tl->id }}" {{ $taskTeamLeadId == $tl->id ? 'selected' : '' }}>{{ $tl->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="task_employee_id" class="form-label">Employee</label>
                                    <select name="task_employee_id" id="task_employee_id" class="form-select">
                                        <option value="" {{ $taskEmployeeId === '' ? 'selected' : '' }}>All Employees</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ $taskEmployeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>Apply Filters
                                    </button>
                                    <button type="button" onclick="resetTaskFilters()" class="btn btn-secondary">
                                        <i class="fas fa-undo me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tasksTable">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-heading me-1"></i>Title</th>
                                        <th><i class="fas fa-bullseye me-1"></i>Target Type</th>
                                        <th><i class="fas fa-exclamation-triangle me-1"></i>Priority</th>
                                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        <th><i class="fas fa-chart-line me-1"></i>Progress</th>
                                        <th><i class="fas fa-calendar-plus me-1"></i>Assigned Date</th>
                                        <th><i class="fas fa-calendar-times me-1"></i>Due Date</th>
                                        {{-- <th><i class="fas fa-user-tie me-1"></i>Team Lead</th> --}}
                                        <th><i class="fas fa-download me-1"></i>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tasksTableBody">
                                    @foreach ($tasks as $task)
                                        <tr>
                                            <td class="uppercase"><div class="fw-bold">{{$task->title }}</div></td>
                                            <td class="uppercase">{{ str_replace('_', ' ', $task->target_type) }}</td>
                                            <td class="uppercase">
                                                <span class="status-badge priority-{{ $task->priority }}">
                                                    {{ $task->priority }}
                                                </span>
                                            </td>
                                            <td class="uppercase">
                                                <span class="status-badge task-status-{{ str_replace(' ', '-', strtolower($task->status)) }}">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress-bar" style="width: 60px;">
                                                        <div class="progress-fill" style="width: {{ $task->progress }}%;"></div>
                                                    </div>
                                                    <span class="fw-bold">{{ $task->progress }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $task->assigned_date ? $task->assigned_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A' }}</td>
                                            {{-- <td class="uppercase">{{ $task->teamLead ? $task->teamLead->name : 'N/A' }}</td> --}}
                                            <td>
                                                <a href="{{ route('admin.export.task', $task->id) }}" class="export-btn">
                                                    <i class="fas fa-download"></i>Export
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        flatpickr('.flatpickr', {
            dateFormat: 'Y-m-d',
            defaultDate: 'today',
            onChange: function(selectedDates, dateStr, instance) {
                if (instance.element.id.includes('end_date')) {
                    let startDateInput = document.querySelector(instance.element.id.replace('end_date', 'start_date'));
                    if (startDateInput && startDateInput._flatpickr) {
                        startDateInput._flatpickr.set('maxDate', dateStr);
                    }
                } else if (instance.element.id.includes('start_date')) {
                    let endDateInput = document.querySelector(instance.element.id.replace('start_date', 'end_date'));
                    if (endDateInput && endDateInput._flatpickr) {
                        endDateInput._flatpickr.set('minDate', dateStr);
                    }
                }
            }
        });

        function toggleTableDateRange() {
            let dateFilter = document.getElementById('table_date_filter').value;
            let customRange = document.getElementById('table_custom_date_range');
            let customRangeEnd = document.getElementById('table_custom_date_range_end');
            if (dateFilter === 'custom') {
                customRange.classList.remove('d-none');
                customRangeEnd.classList.remove('d-none');
                customRange.classList.add('fade-in');
                customRangeEnd.classList.add('fade-in');
                if (!document.getElementById('table_end_date').value) {
                    document.getElementById('table_end_date')._flatpickr.setDate(new Date());
                }
            } else {
                customRange.classList.add('d-none');
                customRangeEnd.classList.add('d-none');
            }
        }

        function toggleLeadDateRange() {
            let dateFilter = document.getElementById('lead_date_filter').value;
            let customRange = document.getElementById('lead_custom_date_range');
            let customRangeEnd = document.getElementById('lead_custom_date_range_end');
            if (dateFilter === 'custom') {
                customRange.classList.remove('d-none');
                customRangeEnd.classList.remove('d-none');
                customRange.classList.add('fade-in');
                customRangeEnd.classList.add('fade-in');
                if (!document.getElementById('lead_end_date').value) {
                    document.getElementById('lead_end_date')._flatpickr.setDate(new Date());
                }
            } else {
                customRange.classList.add('d-none');
                customRangeEnd.classList.add('d-none');
            }
        }

        function toggleTaskDateRange() {
            let dateFilter = document.getElementById('task_date_filter').value;
            let customRange = document.getElementById('task_custom_date_range');
            let customRangeEnd = document.getElementById('task_custom_date_range_end');
            if (dateFilter === 'custom') {
                customRange.classList.remove('d-none');
                customRangeEnd.classList.remove('d-none');
                customRange.classList.add('fade-in');
                customRangeEnd.classList.add('fade-in');
                if (!document.getElementById('task_end_date').value) {
                    document.getElementById('task_end_date')._flatpickr.setDate(new Date());
                }
            } else {
                customRange.classList.add('d-none');
                customRangeEnd.classList.add('d-none');
            }
        }

        function updateTeamLeads() {
            let operationId = document.getElementById('operation_id').value;
            let teamLeadSelect = document.getElementById('team_lead_id');
            teamLeadSelect.innerHTML = '<option value="">All Team Leads</option>';
            fetch(`/admin/dashboard/team-leads?operation_id=${operationId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(response => response.json()).then(data => {
                data.forEach(tl => {
                    let option = document.createElement('option');
                    option.value = tl.id;
                    option.textContent = tl.name;
                    teamLeadSelect.appendChild(option);
                });
                updateEmployees();
            }).catch(error => console.error('Error fetching team leads:', error));
        }

        function updateEmployees() {
            let teamLeadId = document.getElementById('team_lead_id').value;
            let employeeSelect = document.getElementById('employee_id');
            employeeSelect.innerHTML = '<option value="">All Employees</option>';
            fetch(`/admin/dashboard/employees?team_lead_id=${teamLeadId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(response => response.json()).then(data => {
                data.forEach(emp => {
                    let option = document.createElement('option');
                    option.value = emp.id;
                    option.textContent = emp.name;
                    employeeSelect.appendChild(option);
                });
            }).catch(error => console.error('Error fetching employees:', error));
        }

        function updateTaskTeamLeads() {
            let operationId = document.getElementById('task_operation_id').value;
            let teamLeadSelect = document.getElementById('task_team_lead_id');
            teamLeadSelect.innerHTML = '<option value="">All Team Leads</option>';
            fetch(`/admin/dashboard/team-leads?operation_id=${operationId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(response => response.json()).then(data => {
                data.forEach(tl => {
                    let option = document.createElement('option');
                    option.value = tl.id;
                    option.textContent = tl.name;
                    teamLeadSelect.appendChild(option);
                });
                updateTaskEmployees();
            }).catch(error => console.error('Error fetching team leads:', error));
        }

        function updateTaskEmployees() {
            let teamLeadId = document.getElementById('task_team_lead_id').value;
            let employeeSelect = document.getElementById('task_employee_id');
            employeeSelect.innerHTML = '<option value="">All Employees</option>';
            fetch(`/admin/dashboard/employees?team_lead_id=${teamLeadId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(response => response.json()).then(data => {
                data.forEach(emp => {
                    let option = document.createElement('option');
                    option.value = emp.id;
                    option.textContent = emp.name;
                    employeeSelect.appendChild(option);
                });
            }).catch(error => console.error('Error fetching employees:', error));
        }

        function updateDistricts() {
            let state = document.getElementById('state').value;
            let districtSelect = document.getElementById('district');
            districtSelect.innerHTML = '<option value="">All Districts</option>';
            if (state) {
                fetch(`/admin/dashboard/districts?state=${encodeURIComponent(state)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => response.json()).then(data => {
                    data.forEach(district => {
                        let option = document.createElement('option');
                        option.value = district;
                        option.textContent = district;
                        districtSelect.appendChild(option);
                    });
                    updateCities();
                }).catch(error => console.error('Error fetching districts:', error));
            }
        }

        function updateCities() {
            let district = document.getElementById('district').value;
            let citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">All Cities</option>';
            if (district) {
                fetch(`/admin/dashboard/cities?district=${encodeURIComponent(district)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => response.json()).then(data => {
                    data.forEach(city => {
                        let option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }).catch(error => console.error('Error fetching cities:', error));
            }
        }

        function resetTableFilters() {
            let form = document.getElementById('tableFilterForm');
            form.reset();
            form.querySelector('#table_date_filter').value = '30_days';
            document.getElementById('table_custom_date_range').classList.add('d-none');
            document.getElementById('table_custom_date_range_end').classList.add('d-none');
            form.submit();
        }

        function resetLeadFilters() {
            let form = document.getElementById('leadFilterForm');
            form.reset();
            form.querySelector('#status').value = '';
            form.querySelector('#state').value = '';
            form.querySelector('#district').value = '';
            form.querySelector('#city').value = '';
            form.querySelector('#lead_type').value = '';
            form.querySelector('#min_amount').value = '';
            form.querySelector('#max_amount').value = '';
            form.querySelector('#lead_date_filter').value = '';
            form.querySelector('#operation_id').value = '';
            form.querySelector('#team_lead_id').value = '';
            form.querySelector('#employee_id').value = '';
            document.getElementById('lead_custom_date_range').classList.add('d-none');
            document.getElementById('lead_custom_date_range_end').classList.add('d-none');
            updateDistricts();
            updateTeamLeads();
            form.submit();
        }

        function resetTaskFilters() {
            let form = document.getElementById('taskFilterForm');
            form.reset();
            form.querySelector('#task_date_filter').value = '30_days';
            form.querySelector('#task_target_type').value = '';
            form.querySelector('#task_operation_id').value = '';
            form.querySelector('#task_team_lead_id').value = '';
            form.querySelector('#task_employee_id').value = '';
            document.getElementById('task_custom_date_range').classList.add('d-none');
            document.getElementById('task_custom_date_range_end').classList.add('d-none');
            updateTaskTeamLeads();
            form.submit();
        }
    </script>
</body>
</html>
