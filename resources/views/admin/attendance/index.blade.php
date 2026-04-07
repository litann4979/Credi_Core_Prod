<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3b82f6;
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
            padding-left: 240px
        }

        .content-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
            margin: 0 auto;
            max-width: 1600px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
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

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-card.present {
            border-left-color: var(--success-color);
        }

        .stat-card.absent {
            border-left-color: var(--danger-color);
        }

        .stat-card.late {
            border-left-color: var(--warning-color);
        }

        .stat-card.total {
            border-left-color: var(--primary-color);
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

        .filter-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filter-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }

        .btn-outline-secondary {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline-secondary:hover {
            background-color: #f8fafc;
            border-color: #d1d5db;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .table {
            margin: 0;
            font-size: 0.875rem;
        }

        .table th {
            background-color: #f8fafc;
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
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-present {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-absent {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-late {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-half-day {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-employee {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .role-team-lead {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #6b21a8;
        }

        .role-operation {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .time-badge {
            background-color: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.75rem;
        }

        .location-text {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .image-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .image-link:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .no-records {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        .no-records i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                margin: 1rem;
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.875rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
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
                        <i class="fas fa-clock"></i>
                        Attendance Management
                    </h1>
                    <p class="mb-0 opacity-90">Track and monitor employee attendance records</p>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-container">
                    @php
                        $totalRecords = count($attendanceRecords);
                        $presentCount = collect($attendanceRecords)->where('status', 'present')->count();
                        $absentCount = collect($attendanceRecords)->where('status', 'absent')->count();
                        $lateCount = collect($attendanceRecords)->where('status', 'late')->count();
                    @endphp

                    <div class="stat-card total" onclick="showFilteredRecords('all')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-number" style="color: var(--primary-color);">{{ $totalRecords }}</p>
                                <p class="stat-label mb-0">Total Records</p>
                            </div>
                            <div style="color: var(--primary-color);">
                                <i class="fas fa-list fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card present" onclick="showFilteredRecords('present')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-number text-success">{{ $presentCount }}</p>
                                <p class="stat-label mb-0">Present</p>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card absent" onclick="showFilteredRecords('absent')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-number text-danger">{{ $absentCount }}</p>
                                <p class="stat-label mb-0">Absent</p>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card late" onclick="showFilteredRecords('late')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-number text-warning">{{ $lateCount }}</p>
                                <p class="stat-label mb-0">Late</p>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-container">
                    <h5 class="filter-title">
                        <i class="fas fa-filter"></i>
                        Filter Attendance Records
                    </h5>

                    <form id="filter-form" method="GET" action="{{ route('admin.attendance.index') }}">
                        <div class="row g-3">
                            <!-- Date Range -->
                            <div class="col-md-3">
                                <label for="range" class="form-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Date Range
                                </label>
                                <select name="range" id="range" onchange="toggleCustomDateFields()" class="form-select">
                                    <option value="today" {{ $range == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="7_days" {{ $range == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                    <option value="15_days" {{ $range == '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                                    <option value="30_days" {{ $range == '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                    <option value="custom" {{ $range == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>

                            <!-- Custom Date Fields -->
                            <div id="custom-date-fields" class="col-md-4" style="display: {{ $range == 'custom' ? 'block' : 'none' }};">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="start_date" class="form-label">
                                            <i class="fas fa-calendar-day"></i>
                                            Start Date
                                        </label>
                                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label for="end_date" class="form-label">
                                            <i class="fas fa-calendar-day"></i>
                                            End Date
                                        </label>
                                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Role Filter -->
                            <div class="col-md-2">
                                <label for="attendance_role_filter" class="form-label">
                                    <i class="fas fa-user-tag"></i>
                                    Role
                                </label>
                                <select name="attendance_role_filter" id="attendance_role_filter" class="form-select">
                                    <option value="all" {{ $attendanceRoleFilter == 'all' ? 'selected' : '' }}>All Roles</option>
                                    <option value="employee" {{ $attendanceRoleFilter == 'employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="team_lead" {{ $attendanceRoleFilter == 'team_lead' ? 'selected' : '' }}>Team Lead</option>
                                    <option value="operation" {{ $attendanceRoleFilter == 'operation' ? 'selected' : '' }}>Operation</option>
                                </select>
                            </div>

                            <!-- User Filter -->
                            <div id="user-filter-container" class="col-md-2 fade-in" style="display: {{ in_array($attendanceRoleFilter, ['employee', 'team_lead', 'operation']) ? 'block' : 'none' }};"></div>

                            <!-- Hidden selects -->
                            <div class="d-none">
                                <select name="employee" id="employee"></select>
                                <select name="team_lead" id="team_lead"></select>
                                <select name="operation" id="operation"></select>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-3">
                            <button type="button" onclick="resetFilters()" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Reset Filters
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Attendance Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-table me-2"></i>Attendance Records
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-user me-1"></i>Employee</th>
                                    <th><i class="fas fa-user-tag me-1"></i>Role</th>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-calendar-day me-1"></i>Day</th>
                                    <th><i class="fas fa-check-circle me-1"></i>Status</th>
                                    <th><i class="fas fa-sign-in-alt me-1"></i>Check In</th>
                                    <th><i class="fas fa-sign-out-alt me-1"></i>Check Out</th>
                                    <th><i class="fas fa-clock me-1"></i>Total Hours</th>
                                    <th><i class="fas fa-map-marker-alt me-1"></i>In Location</th>
                                    <th><i class="fas fa-map-marker-alt me-1"></i>Out Location</th>
                                    <th><i class="fas fa-sticky-note me-1"></i>Notes</th>
                                    {{-- <th><i class="fas fa-camera me-1"></i>In Image</th>
                                    <th><i class="fas fa-camera me-1"></i>Out Image</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendanceRecords as $record)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $record['employee_name'] }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $roleClass = match($record['employee_role']) {
                                                    'employee' => 'role-employee',
                                                    'team_lead' => 'role-team-lead',
                                                    'operation', 'operations' => 'role-operation',
                                                    default => 'role-employee'
                                                };
                                            @endphp
                                            <span class="role-badge {{ $roleClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $record['employee_role'])) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $record['date'] }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $record['day'] }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($record['status']) {
                                                    'present' => 'status-present',
                                                    'absent' => 'status-absent',
                                                    'late' => 'status-late',
                                                    'half-day' => 'status-half-day',
                                                    default => 'status-present'
                                                };
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ ucfirst($record['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($record['check_in'])
                                                <span class="time-badge">
                                                    {{ \Carbon\Carbon::parse($record['check_in'])->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record['check_out'])
                                                <span class="time-badge">
                                                    {{ \Carbon\Carbon::parse($record['check_out'])->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record['total_hours'])
                                                <span class="time-badge">{{ $record['total_hours'] }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="location-text" title="{{ $record['check_in_location'] ?? '-' }}">
                                                {{ $record['check_in_location'] ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="location-text" title="{{ $record['check_out_location'] ?? '-' }}">
                                                {{ $record['check_out_location'] ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="location-text" title="{{ $record['notes'] ?? '-' }}">
                                                {{ $record['notes'] ?? '-' }}
                                            </div>
                                        </td>
                                        {{-- <td>
                                            @if ($record['checkin_image'])
                                                <a href="{{ $record['checkin_image'] }}" target="_blank" class="image-link">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td> --}}
                                        {{-- <td>
                                            @if ($record['checkout_image'])
                                                <a href="{{ $record['checkout_image'] }}" target="_blank" class="image-link">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="no-records">
                                            <i class="fas fa-calendar-times"></i>
                                            <p class="mb-0">No attendance records found for the selected criteria.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Attendance modal --}}
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="modalAttendanceTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Role</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Hours</th>
                            </tr>
                        </thead>
                        <tbody id="modalAttendanceBody">
                            <!-- Records will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
 const userData = {
    employees: @json($employees->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()),
    teamLeads: @json($teamLeads->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()),
    operations: @json($operations->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values())
};

function toggleCustomDateFields() {
    const range = document.getElementById('range').value;
    const customFields = document.getElementById('custom-date-fields');
    if (range === 'custom') {
        customFields.style.display = 'block';
        customFields.classList.add('fade-in');
    } else {
        customFields.style.display = 'none';
        // Auto-submit form when selecting predefined ranges
        document.getElementById('filter-form').submit();
    }
}

function updateUserDropdowns() {
    const role = document.getElementById('attendance_role_filter').value;
    const container = document.getElementById('user-filter-container');
    container.innerHTML = '';

    const dropdown = document.createElement('select');
    let users = [], name = '', label = '';
    let selectedId = null;

    // Determine the selected user ID based on the current filter
    if (role === 'employee' && '{{ $selectedUserType }}' === 'employee' && '{{ $filteredUserId }}') {
        selectedId = '{{ $filteredUserId }}';
    } else if (role === 'team_lead' && '{{ $selectedUserType }}' === 'team_lead' && '{{ $filteredUserId }}') {
        selectedId = '{{ $filteredUserId }}';
    } else if (role === 'operation' && '{{ $selectedUserType }}' === 'operation' && '{{ $filteredUserId }}') {
        selectedId = '{{ $filteredUserId }}';
    }

    if (role === 'employee') {
        users = userData.employees;
        name = 'employee';
        label = 'Employee';
    } else if (role === 'team_lead') {
        users = userData.teamLeads;
        name = 'team_lead';
        label = 'Team Lead';
    } else if (role === 'operation') {
        users = userData.operations;
        name = 'operation';
        label = 'Operation';
    }

    if (users.length) {
        dropdown.name = name;
        dropdown.id = name;
        dropdown.className = 'form-select';
        dropdown.innerHTML = `<option value="">All ${label}s</option>`;

        users.forEach(user => {
            const opt = document.createElement('option');
            opt.value = user.id;
            opt.textContent = user.name;
            if (selectedId && user.id == selectedId) {
                opt.selected = true;
            }
            dropdown.appendChild(opt);
        });

        const wrapper = document.createElement('div');
        const labelElement = document.createElement('label');
        labelElement.className = 'form-label';
        labelElement.setAttribute('for', name);
        labelElement.innerHTML = `<i class="fas fa-user me-1"></i>${label}`;

        wrapper.appendChild(labelElement);
        wrapper.appendChild(dropdown);
        container.appendChild(wrapper);
        container.style.display = 'block';
        container.classList.add('fade-in');

        dropdown.addEventListener('change', () => document.getElementById('filter-form').submit());
    } else {
        container.style.display = 'none';
    }

    // Clear hidden selects
    document.getElementById('employee').value = '';
    document.getElementById('team_lead').value = '';
    document.getElementById('operation').value = '';
    // Set the hidden select value if a user is selected
    if (selectedId) {
        document.getElementById(name).value = selectedId;
    }
}

function resetFilters() {
    const form = document.getElementById('filter-form');
    form.reset();
    document.getElementById('range').value = 'today';
    document.getElementById('attendance_role_filter').value = 'all';
    toggleCustomDateFields();
    updateUserDropdowns();
    document.getElementById('employee').value = '';
    document.getElementById('team_lead').value = '';
    document.getElementById('operation').value = '';
    form.submit();
}

// Add button to apply custom date range
function addApplyButton() {
    const customFields = document.getElementById('custom-date-fields');
    if (customFields && !document.getElementById('apply-custom-dates')) {
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'col-12 mt-2';
        buttonContainer.innerHTML = `
            <button type="button" id="apply-custom-dates" class="btn btn-primary btn-sm" onclick="applyCustomDateRange()">
                <i class="fas fa-check me-1"></i>Apply Date Range
            </button>
        `;
        customFields.appendChild(buttonContainer);
    }
}

function applyCustomDateRange() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    // Validate date range
    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be after end date');
        return;
    }

    document.getElementById('filter-form').submit();
}

function showFilteredRecords(status) {
    const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
    const modalTitle = document.getElementById('attendanceModalLabel');
    const modalBody = document.getElementById('modalAttendanceBody');

    let title = 'All Attendance Records';
    if (status === 'present') title = 'Present Records';
    else if (status === 'absent') title = 'Absent Records';
    else if (status === 'late') title = 'Late Records';
    modalTitle.textContent = title;

    const filteredRecords = @json($attendanceRecords).filter(record => {
        if (status === 'all') return true;
        return record.status === status;
    });

    modalBody.innerHTML = '';
    if (filteredRecords.length === 0) {
        modalBody.innerHTML = '<tr><td colspan="8" class="text-center">No records found</td></tr>';
    } else {
        filteredRecords.forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${record.employee_name}</td>
                <td>
                    <span class="role-badge ${getRoleClass(record.employee_role)}">
                        ${record.employee_role.replace('_', ' ')}
                    </span>
                </td>
                <td>${record.date}</td>
                <td>${record.day}</td>
                <td>
                    <span class="status-badge ${getStatusClass(record.status)}">
                        ${record.status}
                    </span>
                </td>
                <td>${record.check_in ? formatTime(record.check_in) : '-'}</td>
                <td>${record.check_out ? formatTime(record.check_out) : '-'}</td>
                <td>${record.total_hours}</td>
            `;
            modalBody.appendChild(row);
        });
    }

    modal.show();
}

// Helper functions
function getRoleClass(role) {
    switch(role) {
        case 'employee': return 'role-employee';
        case 'team_lead': return 'role-team-lead';
        case 'operation': return 'role-operation';
        default: return 'role-employee';
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'present': return 'status-present';
        case 'absent': return 'status-absent';
        case 'late': return 'status-late';
        case 'half-day': return 'status-half-day';
        default: return 'status-present';
    }
}

function formatTime(datetime) {
    return new Date(datetime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

document.addEventListener('DOMContentLoaded', () => {
    updateUserDropdowns();

    // Add apply button if custom range is selected
    if (document.getElementById('range').value === 'custom') {
        addApplyButton();
    }

    // Range dropdown - only submit if not custom
    document.getElementById('range').addEventListener('change', function() {
        toggleCustomDateFields();
        if (this.value === 'custom') {
            addApplyButton();
        }
    });

    // Role filter
    document.getElementById('attendance_role_filter').addEventListener('change', () => {
        updateUserDropdowns();
        document.getElementById('filter-form').submit();
    });

});
</script>
</body>
</html>
