<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>

    <style>
        :root {
            --primary: #f97316;
            --primary-hover: #ea580c;
            --bg-light: #f3f4f6;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* Layout Alignment */
        .main-content {
            margin-left: 280px;
            padding-top: 80px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .page-wrapper {
            padding: 1.5rem 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Greeting Section */
        .greeting-section {
            background: white;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .greeting-text h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .greeting-text span {
            color: var(--primary);
        }

        .greeting-text p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* Cards & Containers */
        .card-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .employee-leads-legend {
            max-height: 280px;
        }
        @media (min-width: 768px) {
            .employee-leads-legend {
                max-height: 400px;
            }
        }

        .card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Elements */
        .form-input, .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.9rem;
            transition: all 0.2s;
            background-color: #f9fafb;
            width: 100%;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            outline: none;
            background-color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(249, 115, 22, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(249, 115, 22, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--text-dark);
            border: 1px solid var(--border);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        /* Stats Grid */
        .stats-overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            padding: 24px;
        }

        .stat-item {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        }

        .stat-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
        }

        /* Color variants for stats bottom border */
        .stat-blue::after { background: #3b82f6; }
        .stat-cyan::after { background: #06b6d4; }
        .stat-amber::after { background: #f59e0b; }
        .stat-purple::after { background: #8b5cf6; }
        .stat-red::after { background: #ef4444; }
        .stat-green::after { background: #10b981; }
        .stat-lime::after { background: #84cc16; }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 4px 0;
        }

        .stat-subtext {
            font-size: 0.8rem;
            font-weight: 500;
            color: #059669; /* Green for money */
        }

        /* Tables */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: #f9fafb;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 24px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .table-modern tbody td {
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr:hover td {
            background-color: #fcfcfc;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        /* Modal */
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Mobile */
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 20px; }
            .stats-overview-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    @include('admin.Components.sidebar')

    <div class="main-content">
        @include('admin.Components.header')

        <div class="page-wrapper">

            <div class="greeting-section">
                <div class="greeting-text">
                    <h1 id="greeting-title">Good Morning, <span>{{ Auth::user()->name ?? 'Admin' }}</span>!</h1>
                    <p id="greeting-subtitle">Here's what's happening with your leads and tasks today.</p>
                </div>
                <div class="greeting-icon text-3xl text-orange-500 bg-orange-50 p-3 rounded-full">
                    <i id="weather-icon" class="fas fa-sun"></i>
                </div>
            </div>

            <div class="card-box" id="leads-filter-section">
                <div class="card-header">
                    <h2 class="section-title"><i class="fas fa-filter text-orange-500"></i> Leads Overview & Filter</h2>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="tableFilterForm">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
                            <div class="relative">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">FROM DATE</label>
                                <input type="text" name="table_from_date" id="table_from_date" value="{{ $tableFromDate ?? '' }}" placeholder="YYYY-MM-DD" class="form-input flatpickr" {{ $tableMonth ? 'disabled' : '' }}>
                            </div>

                            <div class="relative">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">TO DATE</label>
                                <input type="text" name="table_to_date" id="table_to_date" value="{{ $tableToDate ?? '' }}" placeholder="YYYY-MM-DD" class="form-input flatpickr" {{ $tableMonth ? 'disabled' : '' }}>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">MONTH</label>
                                <select name="table_month" id="table_month" class="form-select" {{ $tableFromDate || $tableToDate ? 'disabled' : '' }}>
                                    <option value="" {{ $tableMonth === '' ? 'selected' : '' }}>All Months</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}" {{ $tableMonth === $month ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">LEAD TYPE</label>
                                <select name="table_lead_type" id="table_lead_type" class="form-select">
                                    <option value="" {{ $tableLeadType === '' ? 'selected' : '' }}>All Types</option>
                                    @foreach ($leadTypes as $leadTypeOption)
                                        @if($leadTypeOption !== 'creditcard_loan')
                                            <option value="{{ $leadTypeOption }}" {{ $tableLeadType === $leadTypeOption ? 'selected' : '' }}>
                                                {{ str_replace('_', ' ', strtoupper($leadTypeOption)) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative" id="team_lead_dropdown_container">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">TEAM LEAD</label>
                                <input type="hidden" name="table_team_lead_id" id="table_team_lead_id" value="{{ $tableTeamLeadId }}">
                                <button type="button" onclick="toggleTeamLeadDropdown()" class="form-input text-left flex justify-between items-center">
                                    <span id="selectedTeamLeadText" class="truncate">
                                        {{ $teamLeads->firstWhere('id', $tableTeamLeadId)->name ?? 'All Team Leads' }}
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </button>

                                <div id="teamLeadDropdownMenu" class="hidden absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-60 flex flex-col">
                                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                                        <input type="text" id="teamLeadSearchInput" onkeyup="filterTeamLeads()" class="w-full text-sm border border-gray-200 rounded p-2 focus:outline-none focus:border-orange-500" placeholder="Search...">
                                    </div>
                                    <ul class="overflow-y-auto flex-1 p-1" id="teamLeadOptionsList">
                                        <li onclick="selectTeamLead('', 'All Team Leads')" class="px-3 py-2 hover:bg-orange-50 rounded cursor-pointer text-sm">All Team Leads</li>
                                        @foreach ($teamLeads as $teamLead)
                                            <li onclick="selectTeamLead('{{ $teamLead->id }}', '{{ $teamLead->name }}')" class="team-lead-option px-3 py-2 hover:bg-orange-50 rounded cursor-pointer text-sm" data-name="{{ strtolower($teamLead->name) }}">
                                                {{ $teamLead->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="flex items-end gap-2">
                                <button type="submit" class="btn-primary flex-1"><i class="fas fa-filter"></i></button>
                                <button type="button" onclick="resetLeadTableFilters()" class="btn-secondary flex-1"><i class="fas fa-undo"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="stats-overview-grid mt-6">
                        <a href="{{ route('admin.leads-details', ['status' => 'total', 'lead_type' => $tableLeadType, 'team_lead_id' => $tableTeamLeadId, 'from_date' => $tableFromDate, 'to_date' => $tableToDate, 'month' => $tableMonth]) }}" class="stat-item stat-blue">
                            <div class="stat-icon text-blue-600"><i class="fas fa-folder-open"></i></div>
                            <div class="stat-label">Total Leads</div>
                            <div class="stat-value">{{ $totalLeads }}</div>
                            <div class="stat-subtext">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($totalValuation) }}</div>
                        </a>

                        @foreach ([
                            'personal_lead' => ['label' => 'Personal', 'icon' => 'fas fa-user', 'color' => 'blue', 'class' => 'stat-blue'],
                            'authorized' => ['label' => 'Authorized', 'icon' => 'fas fa-check-circle', 'color' => 'cyan', 'class' => 'stat-cyan'],
                            'login' => ['label' => 'Login', 'icon' => 'fas fa-sign-in-alt', 'color' => 'amber', 'class' => 'stat-amber'],
                            'approved' => ['label' => 'Approved', 'icon' => 'fas fa-thumbs-up', 'color' => 'purple', 'class' => 'stat-purple'],
                            'rejected' => ['label' => 'Rejected', 'icon' => 'fas fa-times-circle', 'color' => 'red', 'class' => 'stat-red'],
                            'disbursed' => ['label' => 'Disbursed', 'icon' => 'fas fa-money-bill-wave', 'color' => 'green', 'class' => 'stat-green'],
                            'future_lead' => ['label' => 'Future', 'icon' => 'fas fa-clock', 'color' => 'lime', 'class' => 'stat-lime'],
                        ] as $status => $meta)
                            @php
                                $count = $leadsByStatus[$status]['count'] ?? 0;
                                $valuation = $leadsByStatus[$status]['total_valuation'] ?? 0;
                            @endphp
                            <a href="{{ route('admin.leads-details', ['status' => $status, 'lead_type' => $tableLeadType, 'team_lead_id' => $tableTeamLeadId, 'from_date' => $tableFromDate, 'to_date' => $tableToDate, 'month' => $tableMonth]) }}" class="stat-item {{ $meta['class'] }}">
                                <div class="stat-icon text-{{ $meta['color'] }}-600"><i class="{{ $meta['icon'] }}"></i></div>
                                <div class="stat-label">{{ $meta['label'] }}</div>
                                <div class="stat-value">{{ $count }}</div>
                                @if($status !== 'future_lead')
                                <div class="stat-subtext">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($valuation) }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-box" id="pie-chart-section">
                <div class="card-header">
                    <h2 class="section-title"><i class="fas fa-chart-pie text-purple-600"></i> Employee Performance</h2>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="pieChartFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="relative">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">FROM DATE</label>
                            <input type="text" name="pie_from_date" id="pie_from_date" value="{{ $pieFromDate ?? '' }}" class="form-input flatpickr">
                        </div>
                        <div class="relative">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">TO DATE</label>
                            <input type="text" name="pie_to_date" id="pie_to_date" value="{{ $pieToDate ?? '' }}" class="form-input flatpickr">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">STATUS</label>
                            <select name="pie_status" id="pie_status" class="form-select">
                                <option value="" {{ $pieStatus === '' ? 'selected' : '' }}>All Statuses</option>
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ $pieStatus === $statusOption ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $statusOption)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-primary flex-1">Apply</button>
                            <button type="button" onclick="resetPieChartFilters()" class="btn-secondary flex-1">Reset</button>
                        </div>
                    </form>
                    <div class="flex flex-col md:flex-row gap-4 md:gap-6 items-stretch">
                        <div class="flex-shrink-0 mx-auto md:mx-0" style="width: min(100%, 320px); height: 400px;">
                            <canvas id="employeeLeadsPieChart"></canvas>
                        </div>
                        <div id="employeeLeadsLegend" class="employee-leads-legend flex-1 min-w-0 border border-gray-200 rounded-lg p-3 bg-gray-50/50 overflow-y-auto overflow-x-hidden" style="-webkit-overflow-scrolling: touch;">
                            <!-- Custom legend populated by JS -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box" id="todays-leads-section">
                <div class="card-header">
                    <h2 class="section-title"><i class="fas fa-calendar-day text-teal-600"></i> Today's Leads</h2>
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="todaysLeadsFilterForm" class="flex items-center gap-2">
                        <select name="todays_lead_type" id="todays_lead_type" class="form-select w-40 text-xs">
                            <option value="">ALL TYPES</option>
                            @foreach ($leadTypes as $leadTypeOption)
                                @if($leadTypeOption !== 'creditcard_loan')
                                    <option value="{{ $leadTypeOption }}" {{ $todays_lead_type === $leadTypeOption ? 'selected' : '' }}>{{ strtoupper($leadTypeOption) }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn-primary text-xs py-2 px-3"><i class="fas fa-filter"></i></button>
                        <button type="button" onclick="resetTodaysLeadsFilters()" class="btn-secondary text-xs py-2 px-3"><i class="fas fa-undo"></i></button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>STATUS</th>
                                <th>COUNT</th>
                                <th>TOTAL AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todaysLeadsByStatus as $status => $data)
                                @php
                                    // Soft background classes for rows
                                    $rowClass = match($status) {
                                        'personal_lead' => 'bg-blue-50',
                                        'authorized' => 'bg-cyan-50',
                                        'login' => 'bg-yellow-50',
                                        'approved' => 'bg-purple-50',
                                        'rejected' => 'bg-red-50',
                                        'disbursed' => 'bg-green-50',
                                        'future_lead' => 'bg-lime-50',
                                        default => '',
                                    };
                                @endphp
                                <tr class="cursor-pointer {{ $rowClass }} hover:bg-opacity-80 transition" onclick="window.location='{{ route('admin.today-leads', ['status' => $status, 'lead_type' => $todays_lead_type]) }}'">
                                    <td>
                                        <span class="status-badge bg-white shadow-sm text-gray-700 border border-gray-200">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>
                                    <td class="font-bold">{{ $data['count'] }}</td>
                                    <td class="font-bold text-green-600">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($data['total_valuation']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-box" id="credit-card-section">
                <div class="card-header">
                    <h2 class="section-title"><i class="fas fa-credit-card text-indigo-600"></i> Credit Card Applications</h2>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="creditCardFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">FROM DATE</label>
                            <input type="text" name="credit_card_from_date" id="credit_card_from_date" value="{{ $creditCardFromDate ?? '' }}" class="form-input flatpickr">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">TO DATE</label>
                            <input type="text" name="credit_card_to_date" id="credit_card_to_date" value="{{ $creditCardToDate ?? '' }}" class="form-input flatpickr">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">STATUS</label>
                            <select name="credit_card_status" id="credit_card_status" class="form-select">
                                <option value="">All</option>
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ $creditCardStatus === $statusOption ? 'selected' : '' }}>{{ strtoupper($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-primary flex-1">Apply</button>
                            <button type="button" onclick="resetCreditCardFilters()" class="btn-secondary flex-1">Reset</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>NAME</th>
                                    <th>PHONE</th>
                                    <th>EMAIL</th>
                                    <th>STATUS</th>
                                    <th>LOAN TYPE</th>
                                    <th>BANK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($creditCardLeads as $lead)
                                    <tr class="cursor-pointer hover:bg-gray-50 transition" onclick="window.location.href='{{ route('admin.creditcardlead-details') }}?leadId={{ $lead->id }}'">
                                        <td class="font-medium text-gray-900">{{ $lead->name }}</td>
                                        <td>{{ $lead->phone ?? '-' }}</td>
                                        <td class="text-sm text-gray-500">{{ $lead->email ?? '-' }}</td>
                                        <td>
                                            @php
                                                $ccStatusColor = match($lead->status) {
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="status-badge {{ $ccStatusColor }}">{{ $lead->status ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $lead->lead_type ?? '-' }}</td>
                                        <td>{{ $lead->bank_name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-box" id="tasks-section">
                <div class="card-header">
                    <h2 class="section-title"><i class="fas fa-tasks text-orange-600"></i> Admin Tasks</h2>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="taskFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">DATE RANGE</label>
                            <select name="task_date_filter" id="task_date_filter" class="form-select" onchange="toggleTaskDateRange()">
                                <option value="30_days" {{ $taskDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="15_days" {{ $taskDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                                <option value="7_days" {{ $taskDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="custom" {{ $taskDateFilter === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        <div id="task_custom_date_range" class="{{ $taskDateFilter === 'custom' ? '' : 'hidden' }}">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">START DATE</label>
                            <input type="text" name="task_start_date" id="task_start_date" value="{{ $taskStartDate }}" class="form-input flatpickr">
                        </div>
                        <div id="task_custom_date_range_end" class="{{ $taskDateFilter === 'custom' ? '' : 'hidden' }}">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">END DATE</label>
                            <input type="text" name="task_end_date" id="task_end_date" value="{{ $taskEndDate }}" class="form-input flatpickr">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">TARGET TYPE</label>
                            <select name="task_target_type" id="task_target_type" class="form-select">
                                <option value="">All</option>
                                @foreach ($taskTargetTypes as $targetType)
                                    <option value="{{ $targetType }}" {{ $taskTargetType === $targetType ? 'selected' : '' }}>{{ $targetType === 'individual' ? 'Individual' : ucfirst(str_replace('_', ' ', $targetType)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-primary flex-1">Apply</button>
                            <button type="button" onclick="resetTaskFilters()" class="btn-secondary flex-1">Reset</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>TITLE</th>
                                    <th>TARGET</th>
                                    <th>PRIORITY</th>
                                    <th>STATUS</th>
                                    <th>PROGRESS</th>
                                    <th>ASSIGNED</th>
                                    <th>DUE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr class="cursor-pointer hover:bg-gray-50 transition" onclick="showTaskUsers({{ $task->id }})">
                                        <td class="font-medium">{{ $task->title }}</td>
                                        <td>
                                            <span class="status-badge bg-purple-50 text-purple-700">
                                                {{ ucfirst(str_replace('_', ' ', $task->target_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $pClass = match($task->priority) {
                                                    'high' => 'bg-red-100 text-red-800',
                                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-green-100 text-green-800',
                                                };
                                            @endphp
                                            <span class="status-badge {{ $pClass }}">{{ ucfirst($task->priority) }}</span>
                                        </td>
                                        <td>
                                            <span class="status-badge bg-blue-50 text-blue-700">{{ ucfirst($task->status) }}</span>
                                        </td>
                                        <td class="w-32">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $task->progress }}%"></div>
                                                </div>
                                                <span class="text-xs font-semibold">{{ $task->progress }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-sm text-gray-500">{{ $task->assigned_date ? $task->assigned_date->format('Y-m-d') : '-' }}</td>
                                        <td class="text-sm text-gray-500">{{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-box" id="attendance-section">
                <div class="card-header">
                    <h2 class="section-title"><i class="fas fa-calendar-check text-green-600"></i> Daily Attendance</h2>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="attendanceFilterForm" class="flex justify-end gap-4 mb-4">
                        <div class="w-48">
                            <input type="text" name="attendance_date" id="attendance_date" value="{{ $attendanceDate }}" class="form-input flatpickr">
                        </div>
                        <div class="w-48">
                            <select name="attendance_role_filter" id="attendance_role_filter" class="form-select">
                                <option value="all" {{ $attendanceRoleFilter === 'all' ? 'selected' : '' }}>All Roles</option>
                                <option value="team_lead" {{ $attendanceRoleFilter === 'team_lead' ? 'selected' : '' }}>Team Leads</option>
                                <option value="employee" {{ $attendanceRoleFilter === 'employee' ? 'selected' : '' }}>Employees</option>
                                <option value="operation" {{ $attendanceRoleFilter === 'operation' ? 'selected' : '' }}>Operations</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary px-4"><i class="fas fa-filter"></i></button>
                        <button type="button" onclick="resetAttendanceFilters()" class="btn-secondary px-4"><i class="fas fa-undo"></i></button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>NAME</th>
                                    <th>ROLE</th>
                                    <th>CHECK IN</th>
                                    <th>CHECK OUT</th>
                                    <th>LOC (IN)</th>
                                    <th>LOC (OUT)</th>
                                    <th>NOTES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendances as $attendance)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="font-medium">{{ $attendance->employee->name ?? $attendance->teamLead->name ?? $attendance->operation->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge bg-gray-100 text-gray-700">
                                                {{ $attendance->employee->designation ?? $attendance->teamLead->designation ?? $attendance->operation->designation ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</td>
                                        <td class="text-xs text-gray-500 truncate max-w-xs">{{ $attendance->check_in_location ?? '-' }}</td>
                                        <td class="text-xs text-gray-500 truncate max-w-xs">{{ $attendance->check_out_location ?? '-' }}</td>
                                        <td class="text-xs text-gray-500">{{ $attendance->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> </div> <div id="leadHistoryModal" class="fixed inset-0 modal-overlay z-50 hidden flex items-center justify-center">
        <div class="modal-content w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i> Lead Progress Tracker
                </h3>
                <button onclick="closeModal('leadHistoryModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div id="leadHistoryContent" class="p-6"></div>
        </div>
    </div>

    <div id="taskUsersModal" class="fixed inset-0 modal-overlay z-50 hidden flex items-center justify-center">
        <div class="modal-content w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-users text-orange-600"></i> Task Assignments
                </h3>
                <button onclick="closeModal('taskUsersModal')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div id="taskUsersContent" class="p-6"></div>
        </div>
    </div>

    <div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-70 z-[60] hidden flex items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-orange-500"></div>
    </div>

    <script>
        // 1. Export CSV Logic
        document.getElementById('updateExportCsvBtn')?.addEventListener('click', function() {
            const btn = this;
            const statusDiv = document.getElementById('updateStatus'); // Ensure this div exists in header or somewhere if used

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            // statusDiv logic if element exists...

            fetch('{{ route("admin.leads.update.export.csv") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.ok) return response.blob();
                throw new Error('Network response was not ok');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'monthly_updates_' + new Date().toISOString().slice(0, 19).replace(/[-:T]/g, '') + '.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error(error))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Update & Export CSV';
            });
        });

        // 2. Flatpickr Init
        flatpickr('.flatpickr', {
            dateFormat: 'Y-m-d',
            defaultDate: function() { return this.element.value || null; }
        });

        // 3. Greeting Logic
        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            const userName = "{{ Auth::user()->name ?? 'User' }}";
            const titleEl = document.getElementById('greeting-title');
            const iconEl = document.getElementById('weather-icon');

            let greeting, icon;
            if (hour >= 5 && hour < 12) {
                greeting = `Good Morning, <span>${userName}</span>!`;
                icon = 'fa-sun';
            } else if (hour >= 12 && hour < 17) {
                greeting = `Good Afternoon, <span>${userName}</span>!`;
                icon = 'fa-cloud-sun';
            } else if (hour >= 17 && hour < 21) {
                greeting = `Good Evening, <span>${userName}</span>!`;
                icon = 'fa-moon';
            } else {
                greeting = `Good Night, <span>${userName}</span>!`;
                icon = 'fa-star';
            }
            if(titleEl) titleEl.innerHTML = greeting;
            if(iconEl) iconEl.className = `fas ${icon}`;
        }
        document.addEventListener('DOMContentLoaded', updateGreeting);

        // 4. Team Lead Dropdown Logic
        function toggleTeamLeadDropdown() {
            const menu = document.getElementById('teamLeadDropdownMenu');
            const searchInput = document.getElementById('teamLeadSearchInput');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                setTimeout(() => searchInput.focus(), 100);
            }
        }

        function filterTeamLeads() {
            const input = document.getElementById('teamLeadSearchInput');
            const filter = input.value.toLowerCase();
            const list = document.getElementById('teamLeadOptionsList');
            const items = list.getElementsByClassName('team-lead-option');
            for (let i = 0; i < items.length; i++) {
                const txtValue = items[i].getAttribute('data-name');
                items[i].style.display = txtValue.indexOf(filter) > -1 ? "" : "none";
            }
        }

        function selectTeamLead(id, name) {
            document.getElementById('table_team_lead_id').value = id;
            document.getElementById('selectedTeamLeadText').textContent = name;
            document.getElementById('teamLeadDropdownMenu').classList.add('hidden');
            document.getElementById('teamLeadSearchInput').value = '';
            filterTeamLeads();
        }

        document.addEventListener('click', function(event) {
            const container = document.getElementById('team_lead_dropdown_container');
            const menu = document.getElementById('teamLeadDropdownMenu');
            if (container && !container.contains(event.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });

        // 5. Chart Logic (Color Generator + Config)
        function generateDistinctColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = (i * 360 / count) % 360;
                colors.push(`hsl(${hue}, 85%, 65%)`);
            }
            return colors;
        }

      (function () {
  const el = document.getElementById('employeeLeadsPieChart'); // ensure this matches your existing canvas id
  if (!el || typeof Chart === 'undefined') return;

  // Keep your existing data sources and color generator unchanged
  const labels = {!! json_encode($pieChartData['employees'] ?? []) !!};
  const values = {!! json_encode($pieChartData['leadsCount'] ?? []) !!};
  const totalAmounts = {!! json_encode($pieChartData['totalAmounts'] ?? []) !!};
  const colors = (typeof generateDistinctColors === 'function')
    ? generateDistinctColors(labels.length)
    : {!! json_encode($pieChartData['colors'] ?? []) !!}; // fallback to any existing server-provided colors if present

  // Format currency to Indian format
  function formatToIndianCurrency(amount) {
    const num = parseFloat(amount) || 0;
    return '₹' + num.toLocaleString('en-IN', {
      maximumFractionDigits: 0,
      minimumFractionDigits: 0
    });
  }

  const data = {
    labels,
    datasets: [
      {
        data: values,
        backgroundColor: colors,
        // Subtle separation and modern look
        borderColor: '#ffffff',
        borderWidth: 2,
        borderRadius: 6, // rounded slice ends
        spacing: 2,      // gap between slices
        hoverOffset: 8,
        hoverBorderColor: '#ffffff',
        hoverBorderWidth: 2,
      },
    ],
  };

  // Create/replace chart instance (clean slate for only this chart)
  if (el._chartInstance) {
    el._chartInstance.destroy();
  }

  const chart = new Chart(el, {
    type: 'doughnut', // modern doughnut style
    data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '58%', // clean center hole for a contemporary appearance
      layout: { padding: 8 },
      interaction: { mode: 'nearest', intersect: true },
      plugins: {
        legend: { display: false },
        tooltip: {
          usePointStyle: true,
          callbacks: {
            // Show "Label: count (xx.x%) - Amount"
            label: function (ctx) {
              const label = ctx.label || '';
              const val = Number(ctx.raw) || 0;
              const amount = totalAmounts[ctx.dataIndex] || 0;
              const formattedAmount = formatToIndianCurrency(amount);
              const arr = (ctx.dataset?.data || []).map(Number);
              const total = arr.reduce((a, b) => a + (Number(b) || 0), 0);
              const pct = total ? ((val / total) * 100).toFixed(1) : 0;
              return `${label}: ${val} leads (${pct}%) - ${formattedAmount}`;
            },
          },
        },
      },
      animation: {
        animateRotate: true,
        animateScale: true,
        duration: 800,
        easing: 'easeOutQuart',
      },
    },
  });

  // store for safe re-init if needed
  el._chartInstance = chart;

  // Build scrollable custom legend (all employees visible)
  const legendEl = document.getElementById('employeeLeadsLegend');
  if (legendEl && labels.length) {
    const medals = ['\uD83E\uDD47 ', '\uD83E\uDD48 ', '\uD83E\uDD49 '];
    function esc(s) {
      const div = document.createElement('div');
      div.textContent = s;
      return div.innerHTML;
    }
    legendEl.innerHTML = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5 text-sm">' +
      labels.map(function(label, i) {
        const leadCount = values[i] || 0;
        const amount = totalAmounts[i] || 0;
        const formattedAmount = formatToIndianCurrency(amount);
        const prefix = medals[i] || '';
        const name = (label == null || label === '') ? 'null' : String(label);
        const isTop3 = i < 3;
        const fontClass = isTop3 ? 'font-bold text-gray-800' : 'text-gray-600';
        const title = name + ' (' + leadCount + ' leads, ' + formattedAmount + ')';
        return '<div class="flex items-center gap-2 py-1 truncate" title="' + esc(title) + '">' +
          '<span class="flex-shrink-0 rounded-full w-3 h-3 border border-white shadow" style="background-color:' + colors[i] + '"></span>' +
          '<span class="min-w-0 ' + fontClass + '">' + esc(prefix + name) + ' (' + leadCount + ' leads, ' + esc(formattedAmount) + ')</span>' +
          '</div>';
      }).join('') +
      '</div>';
  }
})();

        // 6. General Filter Resets & Toggles
        function resetLeadTableFilters() {
            const form = document.getElementById('tableFilterForm');
            if(form) {
                form.reset();
                // Clear hidden inputs if needed or specific fields
                document.getElementById('table_from_date')._flatpickr?.clear();
                document.getElementById('table_to_date')._flatpickr?.clear();
                document.getElementById('table_team_lead_id').value = '';
                document.getElementById('selectedTeamLeadText').textContent = 'All Team Leads';
                form.submit();
            }
        }

        function resetPieChartFilters() {
            const form = document.getElementById('pieChartFilterForm');
            if(form) {
                form.reset();
                document.getElementById('pie_from_date')._flatpickr?.clear();
                document.getElementById('pie_to_date')._flatpickr?.clear();
                form.submit();
            }
        }

        function resetTodaysLeadsFilters() {
            const form = document.getElementById('todaysLeadsFilterForm');
            if(form) {
                form.reset();
                form.submit();
            }
        }

        function resetCreditCardFilters() {
            window.location.href = "{{ route('admin.dashboard') }}";
        }

        function resetTaskFilters() {
            const form = document.getElementById('taskFilterForm');
            if(form) {
                form.reset();
                document.getElementById('task_custom_date_range').classList.add('hidden');
                document.getElementById('task_custom_date_range_end').classList.add('hidden');
                form.submit();
            }
        }

        function resetAttendanceFilters() {
            const form = document.getElementById('attendanceFilterForm');
            if(form) {
                form.reset();
                form.submit();
            }
        }

        // Toggles
        function toggleTaskDateRange() {
            const val = document.getElementById('task_date_filter').value;
            const start = document.getElementById('task_custom_date_range');
            const end = document.getElementById('task_custom_date_range_end');
            if(val === 'custom') {
                start.classList.remove('hidden');
                end.classList.remove('hidden');
            } else {
                start.classList.add('hidden');
                end.classList.add('hidden');
            }
        }

        // 7. Modals
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            const content = document.getElementById(id === 'leadHistoryModal' ? 'leadHistoryContent' : 'taskUsersContent');
            if(content) content.innerHTML = '';
        }

        function showLeadHistory(id) {
            fetch(`/admin/dashboard/lead-history/${id}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('leadHistoryContent').innerHTML = html;
                document.getElementById('leadHistoryModal').classList.remove('hidden');
                document.getElementById('leadHistoryModal').classList.add('flex'); // Ensure flex display
            });
        }

        function showTaskUsers(id) {
            if(!id) return;
            fetch(`/admin/dashboard/task-users/${id}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('taskUsersContent').innerHTML = html;
                document.getElementById('taskUsersModal').classList.remove('hidden');
                document.getElementById('taskUsersModal').classList.add('flex');
            });
        }

        // Scroll Restoration Logic
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(f => f.addEventListener('submit', () => {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            }));

            const pos = sessionStorage.getItem('scrollPosition');
            if(pos) {
                window.scrollTo(0, parseInt(pos));
                sessionStorage.removeItem('scrollPosition');
            }
        });
    </script>
</body>
</html>
