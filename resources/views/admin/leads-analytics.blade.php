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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        body {
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #334155;
        }
        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            min-height: calc(100vh - 80px);
            padding: 32px;
            background-color: #f8fafc;
        }
        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background: #f1f5f9;
            border-radius: 16px 16px 0 0;
            border-bottom: 1px solid #e2e8f0;
            padding: 24px;
        }
        .form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .btn-primary {
            background: #3b82f6;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }
        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-secondary {
            background: #64748b;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
        }
        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }
        .form-input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }
        .table-modern {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .table-modern thead {
            background: #1e293b;
        }
        .table-modern thead th {
            color: white;
            font-weight: 600;
            padding: 16px 20px;
            font-size: 12px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-align: left;
        }
        .table-modern tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-modern tbody tr:last-child {
            border-bottom: none;
        }
        .table-modern tbody tr:hover {
            background: #f8fafc;
            transform: none;
        }
        .table-modern tbody td {
            padding: 16px 20px;
            font-weight: 500;
            color: #475569;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .chart-container {
            position: relative;
            padding: 20px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .page-title {
            font-size: 36px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 40px;
            background: linear-gradient(90deg, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .modal-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
        }
        .modal-content {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }
        .status-badge.bg-blue-100 { background-color: #dbeafe; color: #1e40af; }
        .status-badge.bg-emerald-100 { background-color: #00c853; color: #ffffff; }
        .status-badge.bg-amber-100 { background-color: #fef3c7; color: #92400e; }
        .status-badge.bg-violet-100 { background-color: #ede9fe; color: #5b21b6; }
        .status-badge.bg-red-100 { background-color: #fee2e2; color: #991b1b; }
        .status-badge.bg-cyan-100 { background-color: #cffafe; color: #0e7490; }
        .status-badge.bg-lime-100 { background-color: #ecfccb; color: #3f6212; }
        .status-badge.bg-purple-100 { background-color: #f3e8ff; color: #6b21a8; }
        .status-badge.bg-teal-100 { background-color: #ccfbf1; color: #0f766e; }
        .status-badge.bg-yellow-100 { background-color: #fefce8; color: #854d09; }
        .bg-blue-50 { background-color: #eff6ff; }
        .text-blue-800 { color: #1e40af; }
        .text-blue-900 { color: #1e3a8a; }
        .bg-green-50 { background-color: #ecfdf5; }
        .text-green-800 { color: #065f46; }
        .text-green-900 { color: #047857; }
        .bg-emerald-50 { background-color: #00c853; }
        .bg-emerald-100 { background-color: #24d06c; }
        .text-emerald-800 { color: #ffffff; }
        .bg-amber-50 { background-color: #fffbeb; }
        .text-amber-800 { color: #92400e; }
        .bg-violet-50 { background-color: #f5f3ff; }
        .text-violet-800 { color: #5b21b6; }
        .bg-red-50 { background-color: #fef2f2; }
        .text-red-800 { color: #991b1b; }
        .bg-cyan-50 { background-color: #ecfeff; }
        .text-cyan-800 { color: #0e7490; }
        .bg-lime-50 { background-color: #f7fee7; }
        .text-lime-800 { color: #3f6212; }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .page-title {
                font-size: 28px;
                margin-bottom: 24px;
            }
            .section-title {
                font-size: 20px;
            }
        }
        #tableFilterForm .form-input {
    background-color: #f9fafb;
    border-color: #e5e7eb;
}

#tableFilterForm .form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

#tableFilterForm .bg-gray-50 {
    background-color: #f9fafb;
}

#tableFilterForm .border-gray-200 {
    border-color: #e5e7eb;
}
    </style>
</head>
<body>
    @include('admin.Components.sidebar')
    <div class="main-content">
        @include('admin.Components.header')
        <div class="w-full max-w-7xl mx-auto">



 <!-- Combined Leads Filter and Overview -->
{{-- <div class="dashboard-card p-8 mb-8">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">Leads Filter & Overview</h2>
    </div>

    <!-- Leads Filter Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-4" id="tableFilterForm">
            <div class="space-y-2">
                <label for="table_from_date" class="block text-sm font-semibold text-gray-700">From Date</label>
                <input type="text" name="table_from_date" id="table_from_date" value="{{ $tableFromDate ?? '' }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500" {{ $tableMonth ? 'disabled' : '' }}>
            </div>
            <div class="space-y-2">
                <label for="table_to_date" class="block text-sm font-semibold text-gray-700">To Date</label>
                <input type="text" name="table_to_date" id="table_to_date" value="{{ $tableToDate ?? '' }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500" {{ $tableMonth ? 'disabled' : '' }}>
            </div>
            <div class="space-y-2">
                <label for="table_month" class="block text-sm font-semibold text-gray-700">Month</label>
                <select name="table_month" id="table_month" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500" {{ $tableFromDate || $tableToDate ? 'disabled' : '' }}>
                    <option value="" {{ $tableMonth === '' ? 'selected' : '' }}>All Months</option>
                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                        <option value="{{ $month }}" {{ $tableMonth === $month ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label for="table_lead_type" class="block text-sm font-semibold text-gray-700">Lead Type</label>
                <select name="table_lead_type" id="table_lead_type" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="" {{ $tableLeadType === '' ? 'selected' : '' }}>All Lead Types</option>
                    @foreach ($leadTypes as $leadTypeOption)
                        @if($leadTypeOption !== 'creditcard_loan')
                            <option value="{{ $leadTypeOption }}" {{ $tableLeadType === $leadTypeOption ? 'selected' : '' }}>{{ $leadTypeOption }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="space-y-4 flex flex-col justify-end">
                <button type="submit" class="btn-primary w-full flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <button type="button" onclick="resetLeadTableFilters()" class="btn-secondary w-full flex items-center justify-center mt-2">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Leads Overview Section -->
    <div class="card-header p-6 -mb-6 rounded-b-none">

    </div>
   <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    <!-- Total Leads Card -->
    <a href="{{ route('admin.leads-details', ['status' => 'total', 'lead_type' => $tableLeadType, 'from_date' => $tableFromDate, 'to_date' => $tableToDate, 'month' => $tableMonth]) }}"
       class="bg-blue-50 p-4 rounded-xl shadow-sm flex flex-col items-start text-left cursor-pointer h-32">
        <div class="flex items-center space-x-2 mb-2">
            <i class="fas fa-users text-blue-600 text-xl"></i>
            <h3 class="text-2xl font-semibold text-blue-800">Total Leads</h3>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">
            {{ $totalLeads }} / {{ \App\Helpers\FormatHelper::formatToIndianCurrency($totalValuation) }}
        </p>
    </a>

    <!-- Other Status Cards -->
    @foreach (['personal_lead', 'authorized', 'login', 'approved', 'rejected', 'disbursed', 'future_lead'] as $status)
        @php
            $count = $leadsByStatus[$status]['count'] ?? 0;
            $valuation = $leadsByStatus[$status]['total_valuation'] ?? 0;
            $statusName = ucfirst(str_replace('_', ' ', $status));
            $data = match($status) {
                'personal_lead' => ['icon' => 'fas fa-user-plus', 'bg' => 'bg-blue-50', 'text' => 'text-blue-800'],
                'authorized' => ['icon' => 'fas fa-check-circle', 'bg' => 'bg-cyan-50', 'text' => 'text-cyan-800'],
                'login' => ['icon' => 'fas fa-sign-in-alt', 'bg' => 'bg-amber-50', 'text' => 'text-amber-800'],
                'approved' => ['icon' => 'fas fa-thumbs-up', 'bg' => 'bg-violet-50', 'text' => 'text-violet-800'],
                'rejected' => ['icon' => 'fas fa-times-circle', 'bg' => 'bg-red-50', 'text' => 'text-red-800'],
                'disbursed' => ['icon' => 'fas fa-money-bill-wave', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-800'],
                'future_lead' => ['icon' => 'fas fa-clock', 'bg' => 'bg-lime-50', 'text' => 'text-lime-800'],
                default => ['icon' => 'fas fa-info-circle', 'bg' => 'bg-gray-50', 'text' => 'text-gray-800'],
            };
        @endphp
        <a href="{{ route('admin.leads-details', ['status' => $status, 'lead_type' => $tableLeadType, 'from_date' => $tableFromDate, 'to_date' => $tableToDate, 'month' => $tableMonth]) }}"
           class="{{ $data['bg'] }} p-4 rounded-xl shadow-sm flex flex-col items-start text-left cursor-pointer h-32">
            <div class="flex items-center space-x-2 mb-2">
                <i class="{{ $data['icon'] }} text-2xl {{ $data['text'] }}"></i>
                <h3 class="text-2xl font-semibold {{ $data['text'] }}">{{ $statusName }}</h3>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">
                {{ $count }} @if($status !== 'future_lead') / {{ \App\Helpers\FormatHelper::formatToIndianCurrency($valuation) }} @endif
            </p>
        </a>
    @endforeach
</div>


</div> --}}


<!-- Today's Leads Section -->
{{-- <div class="dashboard-card p-8 mb-8">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">
            <i class="fas fa-calendar-day mr-2 text-teal-600"></i>
            Today's Leads
        </h2>
    </div>

    <!-- Lead Type Filter Form for Today's Leads -->
    <div class="bg-white rounded-xl shadow-sm p-2 mb-5 border border-gray-100">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end justify-end space-x-4" id="todaysLeadsFilterForm">
            <div class="space-y-2 w-64">
                <label for="todays_lead_type" class="block text-sm font-semibold text-gray-700">Lead Type</label>
                <select name="todays_lead_type" id="todays_lead_type" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="" {{ $todays_lead_type === '' ? 'selected' : '' }}>All Lead Types</option>
                    @foreach ($leadTypes as $leadTypeOption)
                        @if($leadTypeOption !== 'creditcard_loan')
                            <option value="{{ $leadTypeOption }}" {{ $todays_lead_type === $leadTypeOption ? 'selected' : '' }}>{{ $leadTypeOption }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="space-y-4">
                <button type="submit" class="btn-primary w-full flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <button type="button" onclick="resetTodaysLeadsFilters()" class="btn-secondary w-full flex items-center justify-center mt-2">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th>STATUS</th>
                    <th>COUNT</th>
                    <th>TOTAL LEAD AMOUNT</th>
                </tr>
            </thead>
            <tbody id="todays-leads-table-body">
                @foreach ($todaysLeadsByStatus as $status => $data)
                    @php
                        $colorMap = [
                            'personal_lead' => 'bg-blue-100',
                            'authorized' => 'bg-cyan-50',
                            'login' => 'bg-amber-50',
                            'approved' => 'bg-violet-50',
                            'rejected' => 'bg-red-100',
                            'disbursed' => 'bg-emerald-100',
                            'future_lead' => 'bg-lime-50',
                        ];
                        $rowColorClass = $colorMap[$status] ?? 'bg-gray-100';
                    @endphp
                    <tr class="cursor-pointer {{ $rowColorClass }}" onclick="window.location='{{ route('admin.today-leads', ['status' => $status, 'lead_type' => $todays_lead_type]) }}'">
                        <td class="uppercase">
                            <span class="status-badge text-black">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="font-semibold uppercase">{{ $data['count'] }}</td>
                        <td class="font-semibold text-green-600 uppercase">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($data['total_valuation']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}






    <!-- Credit Card Loan Leads Section -->
{{-- <div class="dashboard-card p-8 mb-8">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">
            <i class="fas fa-credit-card mr-2 text-indigo-600"></i>
            Credit Card
        </h2>
    </div>
    <!-- Date Filter Form for Credit Card -->
<div class="bg-white rounded-xl shadow-sm p-2 mb-5 border border-gray-100">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end justify-end space-x-4" id="creditCardFilterForm">
        <div class="space-y-2 w-64">
            <label for="credit_card_date_filter" class="block text-sm font-semibold text-gray-700">Date Range</label>
            <select name="credit_card_date_filter" id="credit_card_date_filter" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500" onchange="toggleCreditCardDateRange()">
                <option value="30_days" {{ $creditCardDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="15_days" {{ $creditCardDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                <option value="7_days" {{ $creditCardDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="custom" {{ $creditCardDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div class="space-y-2 w-64 {{ $creditCardDateFilter === 'custom' ? '' : 'hidden' }}" id="credit_card_custom_date_range">
            <label for="credit_card_start_date" class="block text-sm font-semibold text-gray-700">Start Date</label>
            <input type="text" name="credit_card_start_date" id="credit_card_start_date" value="{{ $creditCardStartDate }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="space-y-2 w-64 {{ $creditCardDateFilter === 'custom' ? '' : 'hidden' }}" id="credit_card_custom_date_range_end">
            <label for="credit_card_end_date" class="block text-sm font-semibold text-gray-700">End Date</label>
            <input type="text" name="credit_card_end_date" id="credit_card_end_date" value="{{ $creditCardEndDate }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="space-y-4">
            <button type="submit" class="btn-primary w-full flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i>Apply
            </button>
            <button type="button" onclick="resetCreditCardFilters()" class="btn-secondary w-full flex items-center justify-center mt-2">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
        </div>
    </form>
</div>
    <!-- Credit Card Loan Leads Table -->
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>DOB</th>
                    <th>CITY</th>
                    <th>DISTRICT</th>
                    <th>STATE</th>
                    <th>COMPANY</th>
                    <th>LOAN AMOUNT</th>
                    <th>SALARY</th>
                    <th>LOAN TYPE</th>
                    <th>BANK</th>
                </tr>
            </thead>
            <tbody id="credit-card-leads-table-body">
                @foreach ($creditCardLeads as $lead)
                    <tr class="cursor-pointer" onclick="showLeadHistory({{ $lead->id }})">
                        <td class="font-semibold uppercase">{{ $lead->name }}</td>
                        <td class="uppercase">{{ $lead->email ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A' }}</td>
                        <td class="uppercase">{{ $lead->city ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $lead->district ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $lead->state ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $lead->company_name ?? 'N/A' }}</td>
                        <td class="font-semibold text-green-600 uppercase">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0) }}</td>
                        <td class="font-semibold text-blue-600 uppercase">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0) }}</td>
                        <td class="uppercase">{{ $lead->lead_type ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $lead->bank_name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}




                       {{-- <form method="GET" action="{{ route('admin.lead-analytics') }}" class="grid grid-cols-1 md:grid-cols-5 gap-6" id="chartFilterForm" onsubmit="applyChartFilter(event)">
                <div>
                    <label for="chart_date_filter" class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
                    <select name="charts_date_filter" id="chart_date_filter" class="form-input w-full" onchange="toggleChartDateRange()">
                        <option value="30_days" {{ $chartDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="15_days" {{ $chartDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                        <option value="7_days" {{ $chartDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="custom" {{ $chartDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div id="chart_custom_date_range" class="{{ $chartDateFilter === 'custom' ? '' : 'hidden' }}">
                    <label for="chart_start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input type="text" name="charts_start_date" id="chart_start_date" value="{{ $chartStartDate }}" class="form-input w-full flatpickr">
                </div>
                <div id="chart_custom_date_range_end" class="{{ $chartDateFilter === 'custom' ? '' : 'hidden' }}">
                    <label for="chart_end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input type="text" name="charts_end_date" id="chart_end_date" value="{{ $chartEndDate }}" class="form-input w-full flatpickr">
                </div>
                <div>
                    <label for="operation_id" class="block text-sm font-semibold text-gray-700 mb-2">Operation</label>
                    <select name="charts_operation_id" id="operation_id" class="form-input w-full" onchange="updateTeamLeads()">
                        <option value="">All Operations</option>
                        @foreach ($operations as $operation)
                            <option value="{{ $operation->id }}">{{ $operation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="team_lead_id" class="block text-sm font-semibold text-gray-700 mb-2">Team Lead</label>
                    <select name="charts_team_lead_id" id="team_lead_id" class="form-input w-full" onchange="updateEmployees()">
                        <option value="">All Team Leads</option>
                    </select>
                </div>
                <div>
                    <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">Employee</label>
                    <select name="charts_employee_id" id="employee_id" class="form-input w-full">
                        <option value="">All Employees</option>
                    </select>
                </div>
                <div class="md:col-span-5 flex items-end space-x-4">
                    <button type="submit" class="btn-primary flex-1">
                        <i class="fas fa-chart-bar mr-2"></i>Apply Filters
                    </button>
                    <button type="button" onclick="resetChartFilters()" class="btn-secondary flex-1">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </button>
                </div>
            </form>
        </div>
        <div id="charts-section">
            @include('admin.partials.charts')
        </div> --}}


            <!-- Lead Information Table -->
            <div class="dashboard-card p-8 mb-8">
                <div class="card-header p-6 -m-8 mb-6">
                    <h2 class="section-title mb-0">
                        <i class="fas fa-users mr-2 text-purple-600"></i>
                        Lead Information
                    </h2>
                </div>
                <!-- Search Form -->
                <form method="GET" action="{{ route('admin.lead-analytics') }}" class="mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..." class="form-input w-full">
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <button type="button" onclick="resetLeadFilters()" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Reset
                        </button>
                    </div>
                </form>
                <!-- Filter Form -->
                <form method="GET" action="{{ route('admin.lead-analytics') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6" id="leadFilterForm">
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="form-input w-full">
                           <option value="" {{ !isset($status) || $status === '' ? 'selected' : '' }}>All Statuses</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ $status === $statusOption ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statusOption)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                        <select name="state" id="state" class="form-input w-full" onchange="updateDistricts()">
                            <option value="">All States</option>
                            @foreach ($states as $stateOption)
                                <option value="{{ $stateOption->state_title }}" {{ $state === $stateOption->state_title ? 'selected' : '' }}>{{ $stateOption->state_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">District</label>
                        <select name="district" id="district" class="form-input w-full" onchange="updateCities()">
                            <option value="">All Districts</option>
                            @foreach ($districts as $districtOption)
                                <option value="{{ $districtOption->district_title }}" {{ $district === $districtOption->district_title ? 'selected' : '' }}>{{ $districtOption->district_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                        <select name="city" id="city" class="form-input w-full">
                            <option value="">All Cities</option>
                            @foreach ($cities as $cityOption)
                                <option value="{{ $cityOption->name }}" {{ $city === $cityOption->name ? 'selected' : '' }}>{{ $cityOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lead_type" class="block text-sm font-semibold text-gray-700 mb-2">Lead Type</label>
                        <select name="lead_type" id="lead_type" class="form-input w-full">
                            <option value="">All Lead Types</option>
                            @foreach ($leadTypes as $leadTypeOption)
                                <option value="{{ $leadTypeOption }}" {{ $leadType === $leadTypeOption ? 'selected' : '' }}>{{ $leadTypeOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lead_operation_id" class="block text-sm font-semibold text-gray-700 mb-2">Operation</label>
                        <select name="lead_operation_id" id="lead_operation_id" class="form-input w-full" onchange="updateLeadTeamLeads()">
                            <option value="">All Operations</option>
                            @foreach ($operations as $operation)
                                <option value="{{ $operation->id }}" {{ $leadOperationId == $operation->id ? 'selected' : '' }}>{{ $operation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lead_team_lead_id" class="block text-sm font-semibold text-gray-700 mb-2">Team Lead</label>
                        <select name="lead_team_lead_id" id="lead_team_lead_id" class="form-input w-full" onchange="updateLeadEmployees()">
                            <option value="">All Team Leads</option>
                            @foreach ($teamLeads as $teamLead)
                                <option value="{{ $teamLead->id }}" {{ $leadTeamLeadId == $teamLead->id ? 'selected' : '' }}>{{ $teamLead->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lead_employee_id" class="block text-sm font-semibold text-gray-700 mb-2">Employee</label>
                        <select name="lead_employee_id" id="lead_employee_id" class="form-input w-full">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $leadEmployeeId == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="min_amount" class="block text-sm font-semibold text-gray-700 mb-2">Min Amount</label>
                        <input type="number" name="min_amount" id="min_amount" value="{{ $minAmount }}" class="form-input w-full">
                    </div>
                    <div>
                        <label for="max_amount" class="block text-sm font-semibold text-gray-700 mb-2">Max Amount</label>
                        <input type="number" name="max_amount" id="max_amount" value="{{ $maxAmount }}" class="form-input w-full">
                    </div>
                    <div>
                        <label for="lead_date_filter" class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
                        <select name="lead_date_filter" id="lead_date_filter" class="form-input w-full" onchange="toggleLeadDateRange()">
                            <option value="" {{ $leadDateFilter === '' ? 'selected' : '' }}>All Time</option>
                            <option value="60_days" {{ $leadDateFilter === '60_days' ? 'selected' : '' }}>Last 60 Days</option>
                            <option value="30_days" {{ $leadDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="15_days" {{ $leadDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                            <option value="7_days" {{ $leadDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="custom" {{ $leadDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div id="lead_custom_date_range" class="{{ $leadDateFilter === 'custom' ? '' : 'hidden' }}">
                        <label for="lead_start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                        <input type="text" name="lead_start_date" id="lead_start_date" value="{{ $leadStartDate }}" class="form-input w-full flatpickr">
                    </div>
                    <div id="lead_custom_date_range_end" class="{{ $leadDateFilter === 'custom' ? '' : 'hidden' }}">
                        <label for="lead_end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                        <input type="text" name="lead_end_date" id="lead_end_date" value="{{ $leadEndDate }}" class="form-input w-full flatpickr">
                    </div>
                    <div class="md:col-span-3 lg:col-span-5 flex items-end space-x-4">
                        <button type="submit" class="btn-primary flex-1">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <button type="button" onclick="resetLeadFilters()" class="btn-secondary flex-1">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </form>
                <!-- Lead Information Table -->
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
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
                                    <td class="font-semibold uppercase">{{ $lead->name }}</td>
                                    <td class="uppercase">{{ $lead->email ?? 'N/A' }}</td>
                                    <td class="uppercase">{{ $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="uppercase">{{ $lead->city ?? 'N/A' }}</td>
                                    <td class="uppercase">{{ $lead->district ?? 'N/A' }}</td>
                                    <td class="uppercase">{{ $lead->state ?? 'N/A' }}</td>
                                    <td class="uppercase">{{ $lead->company_name ?? 'N/A' }}</td>
                                    <td class="font-semibold text-green-600 uppercase">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0) }}</td>
                                    <td class="font-semibold text-blue-600 uppercase">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->salary ?? 0) }}</td>
                                    <td class="uppercase">
                                        @php
                                            $statusColorClass = match($lead->status) {
                                                'personal_lead' => 'bg-blue-100 text-blue-800',
                                                'authorized' => 'bg-emerald-100 text-emerald-800',
                                                'login' => 'bg-amber-100 text-amber-800',
                                                'approved' => 'bg-violet-100 text-violet-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                'disbursed' => 'bg-cyan-100 text-cyan-800',
                                                'future_lead' => 'bg-lime-100 text-lime-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="status-badge {{ $statusColorClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                        </span>
                                    </td>
                                    <td class="uppercase">{{ $lead->lead_type ?? 'N/A' }}</td>
                                    <td class="font-semibold text-purple-600">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->turnover_amount ?? 0) }}</td>
                                    <td class="uppercase">{{ $lead->bank_name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

          <!-- Tasks Section -->
{{-- <div class="dashboard-card p-8 mb-8">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">
            <i class="fas fa-tasks mr-2 text-orange-600"></i>
            Tasks Assigned by Admin
        </h2>
    </div>
    <!-- Filter Form for Tasks -->
<div class="bg-white rounded-xl shadow-sm p-2 mb-5 border border-gray-100">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end justify-end space-x-4" id="taskFilterForm">
        <div class="space-y-2 w-64">
            <label for="task_date_filter" class="block text-sm font-semibold text-gray-700">Date Range</label>
            <select name="task_date_filter" id="task_date_filter" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500" onchange="toggleTaskDateRange()">
                <option value="30_days" {{ $taskDateFilter === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="15_days" {{ $taskDateFilter === '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                <option value="7_days" {{ $taskDateFilter === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="custom" {{ $taskDateFilter === 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div class="space-y-2 w-64 {{ $taskDateFilter === 'custom' ? '' : 'hidden' }}" id="task_custom_date_range">
            <label for="task_start_date" class="block text-sm font-semibold text-gray-700">Start Date</label>
            <input type="text" name="task_start_date" id="task_start_date" value="{{ $taskStartDate }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="space-y-2 w-64 {{ $taskDateFilter === 'custom' ? '' : 'hidden' }}" id="task_custom_date_range_end">
            <label for="task_end_date" class="block text-sm font-semibold text-gray-700">End Date</label>
            <input type="text" name="task_end_date" id="task_end_date" value="{{ $taskEndDate }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="space-y-2 w-64">
            <label for="task_target_type" class="block text-sm font-semibold text-gray-700">Target Type</label>
            <select name="task_target_type" id="task_target_type" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Target Types</option>
                @foreach ($taskTargetTypes as $targetType)
                    <option value="{{ $targetType }}" {{ $taskTargetType === $targetType ? 'selected' : '' }}>{{ $targetType === 'individual' ? 'Individual Employee' : ucfirst(str_replace('_', ' ', $targetType)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-4">
            <button type="submit" class="btn-primary w-full flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i>Apply
            </button>
            <button type="button" onclick="resetTaskFilters()" class="btn-secondary w-full flex items-center justify-center mt-2">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
        </div>
    </form>
</div>
    <!-- Tasks Table -->
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th>TITLE</th>
                    <th>TARGET TYPE</th>
                    <th>PRIORITY</th>
                    <th>STATUS</th>
                    <th>PROGRESS</th>
                    <th>ASSIGNED DATE</th>
                    <th>DUE DATE</th>
                </tr>
            </thead>
            <tbody id="tasks-table-body">
                @foreach ($tasks as $task)
                    <tr class="cursor-pointer" onclick="showTaskUsers({{ $task->id }})">
                        <td class="font-semibold uppercase">{{ $task->title }}</td>
                        <td>
                            <span class="status-badge bg-purple-100 text-purple-800 uppercase">
                                {{ ucfirst(str_replace('_', ' ', $task->target_type)) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge
                                @if($task->priority === 'high') bg-red-100 text-red-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif uppercase">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge bg-blue-100 text-blue-800 uppercase">
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
                        <td class="uppercase">{{ $task->assigned_date ? $task->assigned_date->format('Y-m-d') : 'N/A' }}</td>
                        <td class="uppercase">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}

          <!-- Today's Attendance Details -->
{{-- <div class="dashboard-card p-8 mb-8">
    <div class="card-header p-6 -m-8 mb-6">
        <h2 class="section-title mb-0">
            <i class="fas fa-calendar-check mr-2 text-teal-600"></i>
            Today's Attendance Details
        </h2>
    </div>
   <!-- Filter Form for Today's Attendance -->
<div class="bg-white rounded-xl shadow-sm p-2 mb-5 border border-gray-100">
    <form method="GET" action="{{ route('admin.lead-analytics') }}" class="flex items-end justify-end space-x-4" id="attendanceFilterForm">
        <div class="space-y-2 w-64">
            <label for="attendance_date" class="block text-sm font-semibold text-gray-700">Date</label>
            <input type="text" name="attendance_date" id="attendance_date" value="{{ $attendanceDate }}" class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="space-y-2 w-64">
            <label for="attendance_role_filter" class="block text-sm font-semibold text-gray-700">Role wise Filter</label>
            <select name="attendance_role_filter" id="attendance_role_filter" class="form-input w-full bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                <option value="all" {{ $attendanceRoleFilter === 'all' ? 'selected' : '' }}>All</option>
                <option value="team_lead" {{ $attendanceRoleFilter === 'team_lead' ? 'selected' : '' }}>Team Leads</option>
                <option value="employee" {{ $attendanceRoleFilter === 'employee' ? 'selected' : '' }}>Employees</option>
                <option value="operation" {{ $attendanceRoleFilter === 'operation' ? 'selected' : '' }}>Operations</option>
            </select>
        </div>
        <div class="space-y-4">
            <button type="submit" class="btn-primary w-full flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i>Apply
            </button>
            <button type="button" onclick="resetAttendanceFilters()" class="btn-secondary w-full flex items-center justify-center mt-2">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
        </div>
    </form>
</div>
    <!-- Attendance Table -->
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>ROLE</th>
                    <th>CHECK IN</th>
                    <th>CHECK OUT</th>
                    <th>CHECK IN LOCATION</th>
                    <th>CHECK OUT LOCATION</th>
                    <th>NOTES</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                @foreach ($attendances as $attendance)
                    <tr>
                        <td class="font-semibold uppercase">
                            {{ $attendance->employee->name ?? $attendance->teamLead->name ?? $attendance->operation->name ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="status-badge bg-teal-100 text-teal-800 uppercase">
                                {{ $attendance->employee->designation ?? $attendance->teamLead->designation ?? $attendance->operation->designation ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="uppercase">{{ $attendance->check_in ? $attendance->check_in->format('H:i') : 'N/A' }}</td>
                        <td class="uppercase">{{ $attendance->check_out ? $attendance->check_out->format('H:i') : 'N/A' }}</td>
                        <td class="uppercase">{{ $attendance->check_in_location ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $attendance->check_out_location ?? 'N/A' }}</td>
                        <td class="uppercase">{{ $attendance->notes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}

            <!-- Modal for Lead History -->
            <div id="leadHistoryModal" class="fixed inset-0 modal-overlay overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 ml-96 mx-auto p-5 border w-3/4 shadow-lg modal-content">
                    <div class="flex justify-between items-center mb-6 p-6 -m-5 mb-5 card-header">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-history mr-2 text-blue-600"></i>
                            Lead Progress Tracker
                        </h3>
                        <button onclick="closeModal('leadHistoryModal')" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">×</button>
                    </div>
                    <div id="leadHistoryContent" class="p-6 -m-5"></div>
                </div>
            </div>

            <!-- Modal for Task Users -->
            {{-- <div id="taskUsersModal" class="fixed inset-0 modal-overlay overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 ml-96 mx-auto p-5 border w-3/4 shadow-lg modal-content">
                    <div class="flex justify-between items-center mb-6 p-6 -m-5 mb-5 card-header">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-users mr-2 text-orange-600"></i>
                            Task Assignments
                        </h3>
                        <button onclick="closeModal('taskUsersModal')" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">×</button>
                    </div>
                    <div id="taskUsersContent" class="p-6 -m-5"></div>
                </div>
            </div> --}}
        </div>
    </div>

  <script>
// Initialize Flatpickr for date pickers
flatpickr('.flatpickr', {
    dateFormat: 'Y-m-d',
    defaultDate: function() {
        const input = this.element;
        return input.value || null; // Use existing value if available
    }
});



function resetTodaysLeadsFilters() {
        const form = document.getElementById('todaysLeadsFilterForm');
        if (form) {
            form.reset();
            document.getElementById('todays_lead_type').value = '';
            form.submit();
        }
    }

// Toggle mutual exclusivity for Leads Filter
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('table_from_date');
    const toDateInput = document.getElementById('table_to_date');
    const monthSelect = document.getElementById('table_month');


    function applyChartFilter(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('chartFilterForm'));
            $.ajax({
                url: '{{ route("admin.getChartsData") }}',
                method: 'GET',
                data: formData,
                success: function(response) {
                    $('#charts-section').html(`
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <div class="dashboard-card">
                                <div class="card-header p-6">
                                    <h2 class="section-title mb-0"><i class="fas fa-chart-bar mr-2 text-blue-600"></i>Leads per Employee</h2>
                                </div>
                                <div class="chart-container"><canvas id="leadsPerEmployeeChart"></canvas></div>
                            </div>
                            <div class="dashboard-card">
                                <div class="card-header p-6">
                                    <h2 class="section-title mb-0"><i class="fas fa-chart-pie mr-2 text-green-600"></i>Lead Status Distribution</h2>
                                </div>
                                <div class="chart-container"><canvas id="leadStatusChart"></canvas></div>
                            </div>
                        </div>
                    `);
                    new Chart(document.getElementById('leadsPerEmployeeChart'), {
                        type: 'bar',
                        data: { labels: response.leadsPerEmployee.map(item => item.name), datasets: [{ label: 'Leads', data: response.leadsPerEmployee.map(item => item.lead_count), backgroundColor: '#3b82f6' }] },
                        options: { scales: { y: { beginAtZero: true } } }
                    });
                    new Chart(document.getElementById('leadStatusChart'), {
                        type: 'pie',
                        data: { labels: Object.keys(response.leadStatusDistribution), datasets: [{ data: Object.values(response.leadStatusDistribution), backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6'] }] }
                    });
                }
            });
        }


    function toggleFilters() {
        if (monthSelect.value !== '') {
            fromDateInput.disabled = true;
            toDateInput.disabled = true;
        } else {
            fromDateInput.disabled = false;
            toDateInput.disabled = false;
        }
        if (fromDateInput.value || toDateInput.value) {
            monthSelect.disabled = true;
        } else {
            monthSelect.disabled = false;
        }
    }

    fromDateInput.addEventListener('change', toggleFilters);
    toDateInput.addEventListener('change', toggleFilters);
    monthSelect.addEventListener('change', toggleFilters);

    // Initial check on page load
    toggleFilters();
});

// Reset Leads Filter
function resetLeadTableFilters() {
    const form = document.getElementById('tableFilterForm');
    if (form) {
        form.reset();
        document.getElementById('table_from_date').value = '';
        document.getElementById('table_to_date').value = '';
        document.getElementById('table_month').value = '';
        document.getElementById('table_lead_type').value = '';
        document.getElementById('table_from_date').disabled = false;
        document.getElementById('table_to_date').disabled = false;
        document.getElementById('table_month').disabled = false;
        // Clear any existing Flatpickr instances
        flatpickr('#table_from_date').clear();
        flatpickr('#table_to_date').clear();
        form.submit();
    }
}

// Toggle date range fields for credit card loans
function toggleCreditCardDateRange() {
    const dateFilter = document.getElementById('credit_card_date_filter').value;
    const customDateRange = document.getElementById('credit_card_custom_date_range');
    const customDateRangeEnd = document.getElementById('credit_card_custom_date_range_end');
    if (dateFilter === 'custom') {
        customDateRange.classList.remove('hidden');
        customDateRangeEnd.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
        customDateRangeEnd.classList.add('hidden');
    }
}

// Reset credit card filters
function resetCreditCardFilters() {
    const form = document.getElementById('creditCardFilterForm');
    if (form) {
        form.reset();
        document.getElementById('credit_card_date_filter').value = '30_days';
        document.getElementById('credit_card_start_date').value = '';
        document.getElementById('credit_card_end_date').value = '';
        toggleCreditCardDateRange();
        form.submit();
    }
}

// Toggle date range fields for lead information
function toggleLeadDateRange() {
    const dateFilter = document.getElementById('lead_date_filter').value;
    const customDateRange = document.getElementById('lead_custom_date_range');
    const customDateRangeEnd = document.getElementById('lead_custom_date_range_end');
    if (dateFilter === 'custom') {
        customDateRange.classList.remove('hidden');
        customDateRangeEnd.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
        customDateRangeEnd.classList.add('hidden');
    }
}

// Toggle date range fields for tasks
function toggleTaskDateRange() {
    const dateFilter = document.getElementById('task_date_filter').value;
    const customDateRange = document.getElementById('task_custom_date_range');
    const customDateRangeEnd = document.getElementById('task_custom_date_range_end');
    if (dateFilter === 'custom') {
        customDateRange.classList.remove('hidden');
        customDateRangeEnd.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
        customDateRangeEnd.classList.add('hidden');
    }
}

// Update team leads for charts
function updateTeamLeads() {
    const operationId = document.getElementById('operation_id').value;
    const teamLeadSelect = document.getElementById('team_lead_id');
    teamLeadSelect.innerHTML = '<option value="">All Team Leads</option>';
    if (operationId) {
        fetch(`/admin/leads-analytics/team-leads?operation_id=${operationId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(teamLead => {
                const option = document.createElement('option');
                option.value = teamLead.id;
                option.textContent = teamLead.name;
                teamLeadSelect.appendChild(option);
            });
            updateEmployees();
        })
        .catch(error => console.error('Error fetching team leads:', error));
    } else {
        updateEmployees();
    }
}

// Update employees for charts
function updateEmployees() {
    const teamLeadId = document.getElementById('team_lead_id').value;
    const employeeSelect = document.getElementById('employee_id');
    employeeSelect.innerHTML = '<option value="">All Employees</option>';
    if (teamLeadId) {
        fetch(`/admin/dleads-analytics/employees?team_lead_id=${teamLeadId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(employee => {
                const option = document.createElement('option');
                option.value = employee.id;
                option.textContent = employee.name;
                employeeSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching employees:', error));
    }
}

// Update team leads for lead information
function updateLeadTeamLeads() {
    const operationId = document.getElementById('lead_operation_id').value;
    const teamLeadSelect = document.getElementById('lead_team_lead_id');
    teamLeadSelect.innerHTML = '<option value="">All Team Leads</option>';
    if (operationId) {
        fetch(`/admin/leads-analytics/team-leads?operation_id=${operationId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(teamLead => {
                const option = document.createElement('option');
                option.value = teamLead.id;
                option.textContent = teamLead.name;
                teamLeadSelect.appendChild(option);
            });
            updateLeadEmployees();
        })
        .catch(error => console.error('Error fetching team leads:', error));
    } else {
        updateLeadEmployees();
    }
}

// Update employees for lead information
function updateLeadEmployees() {
    const teamLeadId = document.getElementById('lead_team_lead_id').value;
    const employeeSelect = document.getElementById('lead_employee_id');
    employeeSelect.innerHTML = '<option value="">All Employees</option>';
    if (teamLeadId) {
        fetch(`/admin/leads-analytics/employees?team_lead_id=${teamLeadId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(employee => {
                const option = document.createElement('option');
                option.value = employee.id;
                option.textContent = employee.name;
                employeeSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching employees:', error));
    }
}

// Update districts
function updateDistricts() {
    const state = document.getElementById('state').value;
    const districtSelect = document.getElementById('district');
    const citySelect = document.getElementById('city');
    districtSelect.innerHTML = '<option value="">All Districts</option>';
    citySelect.innerHTML = '<option value="">All Cities</option>';
    if (state) {
        fetch(`/admin/leads-analytics/districts?state=${encodeURIComponent(state)}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(district => {
                const option = document.createElement('option');
                option.value = district.district_title;
                option.textContent = district.district_title;
                districtSelect.appendChild(option);
            });
            updateCities();
        })
        .catch(error => console.error('Error fetching districts:', error));
    } else {
        updateCities();
    }
}

// Update cities
function updateCities() {
    const district = document.getElementById('district').value;
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">All Cities</option>';
    if (district) {
        fetch(`/admin/leads-analytics/cities?district=${encodeURIComponent(district)}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(city => {
                const option = document.createElement('option');
                option.value = city.name;
                option.textContent = city.name;
                citySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching cities:', error));
    }
}

// Reset lead filters
function resetLeadFilters() {
    const form = document.getElementById('leadFilterForm');
    if (form) {
        form.reset();
        document.getElementById('status').value = '';
        document.getElementById('state').value = '';
        document.getElementById('district').value = '';
        document.getElementById('city').value = '';
        document.getElementById('lead_type').value = '';
        document.getElementById('lead_operation_id').value = '';
        document.getElementById('lead_team_lead_id').innerHTML = '<option value="">All Team Leads</option>';
        document.getElementById('lead_employee_id').innerHTML = '<option value="">All Employees</option>';
        document.getElementById('min_amount').value = '';
        document.getElementById('max_amount').value = '';
        document.getElementById('lead_date_filter').value = '';
        toggleLeadDateRange();
        updateDistricts();
        updateLeadTeamLeads();
        form.submit();
    }
}

// Reset task filters
function resetTaskFilters() {
    const form = document.getElementById('taskFilterForm');
    if (form) {
        form.reset();
        document.getElementById('task_date_filter').value = '30_days';
        document.getElementById('task_target_type').value = '';
        toggleTaskDateRange();
        form.submit();
    }
}

// Reset attendance filters
function resetAttendanceFilters() {
    const form = document.getElementById('attendanceFilterForm');
    if (form) {
        form.reset();
        document.getElementById('attendance_date').value = '{{ $attendanceDate }}';
        document.getElementById('attendance_role_filter').value = 'all';
        form.submit();
    }
}

// Show lead history modal
function showLeadHistory(leadId) {
    fetch(`/admin/leads-analytics/lead-history/${leadId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('leadHistoryContent').innerHTML = data;
        document.getElementById('leadHistoryModal').classList.remove('hidden');
    })
    .catch(error => console.error('Error fetching lead history:', error));
}

// Show task users modal
function showTaskUsers(taskId) {
    if (!taskId) {
        console.error('Invalid taskId:', taskId);
        return;
    }
    fetch(`/admin/dashboard/task-users/${taskId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
    })
    .then(data => {
        document.getElementById('taskUsersContent').innerHTML = data;
        document.getElementById('taskUsersModal').classList.remove('hidden');
    })
    .catch(error => console.error('Error fetching task users:', error));
}

// Close modal
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId === 'leadHistoryModal' ? 'leadHistoryContent' : 'taskUsersContent').innerHTML = '';
}
</script>
</body>
</html>
