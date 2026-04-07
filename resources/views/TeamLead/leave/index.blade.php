
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply for Leave</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <style>
        body {
            background: #f8fafc; /* slate-50 */
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #334155; /* slate-700 */
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
            border: 1px solid #e2e8f0; /* slate-200 */
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }
        .card-header {
            background: #f1f5f9; /* slate-100 */
            border-radius: 16px 16px 0 0;
            border-bottom: 1px solid #e2e8f0;
            padding: 24px;
        }
        .btn-primary {
            background: #3b82f6; /* blue-500 */
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }
        .btn-primary:hover {
            background: #2563eb; /* blue-600 */
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-secondary {
            background: #64748b; /* slate-500 */
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
        }
        .btn-secondary:hover {
            background: #475569; /* slate-700 */
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }
        .form-input {
            border: 1px solid #cbd5e1; /* slate-300 */
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        .form-input:focus {
            border-color: #3b82f6; /* blue-500 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b; /* slate-800 */
            margin-bottom: 16px;
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
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        .table-modern th,
        .table-modern td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0; /* slate-200 */
        }
        .table-modern th {
            background: #f1f5f9; /* slate-100 */
            font-weight: 600;
            color: #1e293b; /* slate-800 */
            font-size: 14px;
        }
        .table-modern td {
            font-size: 14px;
            color: #334155; /* slate-700 */
        }
        .table-modern tr:last-child td {
            border-bottom: none;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-pending {
            background: #fef3c7; /* amber-100 */
            color: #d97706; /* amber-600 */
        }
        .status-approved {
            background: #dcfce7; /* green-100 */
            color: #15803d; /* green-700 */
        }
        .status-rejected {
            background: #fee2e2; /* red-100 */
            color: #b91c1c; /* red-700 */
        }
        .pagination-controls {
            margin-top: 16px;
            display: flex;
            justify-content: center;
        }
        .pagination-controls .pagination {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .pagination-controls a,
        .pagination-controls span {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            color: #334155;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .pagination-controls a:hover {
            background: #f1f5f9;
            border-color: #3b82f6;
        }
        .pagination-controls .current {
            background: #3b82f6;
            color: #ffffff;
            border-color: #3b82f6;
        }
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
            .table-modern {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    @include('TeamLead.Components.sidebar')
    <div class="main-content">
        @include('TeamLead.Components.header')
        <div class="w-full max-w-7xl mx-auto">
            <h1 class="page-title">Apply for Leave</h1>

            <!-- Leave Application Form -->
            <div class="dashboard-card p-8 mb-8">
                <div class="card-header p-6 -m-8 mb-6">
                    <h2 class="section-title mb-0"><i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Leave Application Form</h2>
                </div>
               <!-- Replace this section in your Blade template -->
@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        {{ session('success') }}
    </div>
@endif
@if ($errors->any() || session('error')) <!-- Add check for session error -->
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            @if (session('error')) <!-- Display session error if it exists -->
                <li>{{ session('error') }}</li>
            @endif
        </ul>
    </div>
@endif
                <form id="leaveForm" method="POST" action="{{ route('team_lead.leave.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                        <select name="leave_type" id="leave_type" class="form-input w-full" required>
                            <option value="" disabled selected>Select Leave Type</option>
                            <option value="casual_leave">Casual Leave</option>
                            <option value="sick_leave">Sick Leave</option>
                            {{-- <option value="earned_leave">Earned Leave</option> --}}
                            <option value="maternity_leave">Maternity Leave</option>
                            <option value="paid_leave">paid Leave</option>
                        </select>
                    </div>
                    <div>
                        <label for="applied_to" class="block text-sm font-medium text-gray-700 mb-1">Applied To (HR)</label>
                        <select name="applied_to" id="applied_to" class="form-input w-full" required>
                            <option value="" disabled selected>Select HR</option>
                            @foreach ($hrUsers as $hr)
                                <option value="{{ $hr->id }}">{{ $hr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="text" name="start_date" id="start_date" class="form-input w-full flatpickr" required>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="text" name="end_date" id="end_date" class="form-input w-full flatpickr" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea name="reason" id="reason" rows="4" class="form-input w-full" required></textarea>
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-4">
                        <button type="button" onclick="window.location.href='{{ route('team_lead.leave.index') }}'" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Submit Leave</button>
                    </div>
                </form>
            </div>

            <!-- Recent Leave Histories -->
            <div class="dashboard-card p-8">
                <div class="card-header p-6 -m-8 mb-6">
                    <h2 class="section-title mb-0"><i class="fas fa-history mr-2 text-teal-600"></i>Recent Leave Histories</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Total Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Applied To</th>
                                <th>Decision Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaveHistories as $leave)
                                <tr>
                                    <td>{{ ucwords(str_replace('_', ' ', $leave->leave_type)) }}</td>
                                    <td>{{ $leave->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $leave->end_date->format('Y-m-d') }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td>{{ $leave->reason }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $leave->status }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $leave->appliedTo->name ?? 'N/A' }}</td>
                                    <td>{{ $leave->decision_date ? $leave->decision_date->format('Y-m-d H:i') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No leave history available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination-controls">
                    {{ $leaveHistories->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Flatpickr for date inputs
        flatpickr('.flatpickr', {
            dateFormat: 'Y-m-d',
            minDate: 'today',
        });

        // Confirmation prompt before form submission
        document.getElementById('leaveForm').addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to submit this leave application?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>

