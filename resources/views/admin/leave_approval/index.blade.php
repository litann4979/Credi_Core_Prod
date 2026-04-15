<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Leave Approval Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Enhanced professional styling with modern design system */
        :root {
            --primary-50: #f0f9ff;
            --primary-100: #e0f2fe;
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
            --success-50: #f0fdf4;
            --success-100: #dcfce7;
            --success-500: #22c55e;
            --success-600: #16a34a;
            --success-700: #15803d;
            --warning-50: #fffbeb;
            --warning-100: #fef3c7;
            --warning-500: #f59e0b;
            --warning-600: #d97706;
            --error-50: #fef2f2;
            --error-100: #fee2e2;
            --error-500: #ef4444;
            --error-600: #dc2626;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
            color: var(--gray-700);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Enhanced table styling with better visual hierarchy */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--gray-200);
        }

        .table-modern th {
            background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%);
            text-align: left;
            padding: 10px 14px;
            font-weight: 600;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary-700);
            border-bottom: 2px solid var(--primary-200);
            position: sticky;
            top: 0;
            z-index: 10;
            transition: all 0.2s ease;
        }

        .table-modern th:hover {
            background: linear-gradient(135deg, var(--primary-100) 0%, var(--primary-200) 100%);
        }

        .table-modern td {
            padding: 9px 14px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-600);
            vertical-align: middle;
            font-size: 0.8rem;
            font-weight: 400;
            transition: all 0.2s ease;
            line-height: 1.2;
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        .table-modern tr:hover {
            background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.1);
        }

        .table-modern tr:hover td {
            color: var(--gray-700);
        }

        /* Enhanced status badges with icons and animations */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.55rem;
            border-radius: 50px;
            font-size: 0.66rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .status-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .status-badge:hover::before {
            left: 100%;
        }

        .status-pending {
            background: linear-gradient(135deg, var(--warning-100) 0%, var(--warning-50) 100%);
            color: var(--warning-700);
            border-color: var(--warning-300);
        }

        .status-pending::after {
            content: '\f017';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .status-approved {
            background: linear-gradient(135deg, var(--success-100) 0%, var(--success-50) 100%);
            color: var(--success-700);
            border-color: var(--success-300);
        }

        .status-approved::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .status-rejected {
            background: linear-gradient(135deg, var(--error-100) 0%, var(--error-50) 100%);
            color: var(--error-700);
            border-color: var(--error-300);
        }

        .status-rejected::after {
            content: '\f00d';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Enhanced buttons with loading states and better interactions */
        .btn-primary {
            background: linear-gradient(135deg, var(--success-500) 0%, var(--success-600) 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.68rem;
            padding: 0.45rem 0.8rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--success-600) 0%, var(--success-700) 100%);
            box-shadow: 0 8px 25px -8px rgba(34, 197, 94, 0.5);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #ffffff 0%, var(--gray-50) 100%);
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.68rem;
            padding: 0.45rem 0.8rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: relative;
            overflow: hidden;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0,0,0,0.05), transparent);
            transition: left 0.5s ease;
        }

        .btn-secondary:hover::before {
            left: 100%;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            border-color: var(--gray-300);
            color: var(--gray-700);
            box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn-secondary:active {
            transform: translateY(0);
        }

        /* Enhanced alerts with better visual design */
        .alert {
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 12px 0 0 12px;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-50) 0%, #ffffff 100%);
            color: var(--success-700);
            border-color: var(--success-200);
        }

        .alert-success::before {
            background: var(--success-500);
        }

        .alert-error {
            background: linear-gradient(135deg, var(--error-50) 0%, #ffffff 100%);
            color: var(--error-700);
            border-color: var(--error-200);
        }

        .alert-error::before {
            background: var(--error-500);
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            margin-left: 1rem;
            color: inherit;
            opacity: 0.6;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .alert-close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.1);
            transform: scale(1.1);
        }

        /* Enhanced typography and layout */
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-700) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary-500);
            font-size: 1.25rem;
        }

        /* Enhanced card design */
        .dashboard-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-100);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }

        .card-header {
            border-bottom: 1px solid var(--gray-100);
            background: linear-gradient(135deg, #ffffff 0%, var(--gray-50) 100%);
            padding: 1rem 1.25rem;
        }

        .main-content {
            margin-left: 280px;
            padding: 1.2rem 1.4rem 1.6rem;
            margin-top: 36px;
            min-height: calc(100vh - 36px);
        }

        .page-head {
            margin-bottom: 0.7rem;
        }

        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
        }

        .table-modern {
            min-width: 1400px;
        }

        .action-buttons {
            display: flex;
            gap: 0.4rem;
            align-items: center;
            flex-wrap: nowrap;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-500);
            font-style: italic;
            font-size: 1rem;
        }

        .inline {
            display: inline-block;
        }

        /* Better alignment for numeric columns */
        .table-modern td:nth-child(5),
        .table-modern td:nth-child(10),
        .table-modern td:nth-child(11),
        .table-modern td:nth-child(12) {
            text-align: center;
            font-weight: 600;
            color: var(--gray-700);
        }

        .table-modern th:nth-child(5),
        .table-modern th:nth-child(10),
        .table-modern th:nth-child(11),
        .table-modern th:nth-child(12) {
            text-align: center;
        }

        /* Loading animation for buttons */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            display: inline-block;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }

        /* Responsive enhancements */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 0.85rem;
            }

            .page-title {
                font-size: 1.6rem;
                margin-bottom: 0.7rem;
            }

            .table-modern {
                min-width: 800px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    @include('admin.Components.sidebar')
    <div class="main-content p-6">
        @include('admin.Components.header')
        <div class="w-full max-w-7xl mx-auto">
            <div class="page-head">
                <h1 class="page-title">Leave Approval Requests</h1>
            </div>

            <!-- Leave Requests Table -->
            <div class="dashboard-card p-4">
                <div class="card-header p-0 -m-4 mb-4">
                    <h2 class="section-title mb-0">
                        <i class="fas fa-file-alt"></i>
                         Leave Requests
                    </h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success" id="success-alert">
                        <div>{{ session('success') }}</div>
                        <button type="button" class="alert-close" onclick="dismissAlert('success-alert')">&times;</button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error" id="error-alert">
                        <div>{{ session('error') }}</div>
                        <button type="button" class="alert-close" onclick="dismissAlert('error-alert')">&times;</button>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Total Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Applied To</th>
                                <th>Decision Date</th>
                                <th>Total Leaves</th>
                                <th>Used Leaves</th>
                                <th>Balance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaveRequests as $leave)
                                <tr>
                                    <td>{{ $leave->user->name ?? 'N/A' }}</td>
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
                                    <td>{{ $leave->balance_info->total ?? 'N/A' }}</td>
                                    <td>{{ $leave->balance_info->used ?? 'N/A' }}</td>
                                    <td>{{ $leave->balance_info->balance ?? 'N/A' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            @if ($leave->status === 'pending')
                                                <form action="{{ route('admin.leave.approvals.update', $leave->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approved">
                                                    <button type="submit" class="btn-primary" onclick="return confirm('Approve this leave?')">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.leave.approvals.update', $leave->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="rejected">
                                                    <button type="submit" class="btn-secondary" onclick="return confirm('Reject this leave?')">Reject</button>
                                                </form>
                                            @else
                                                <span class="text-gray-500">No actions</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="empty-state">No leave requests available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Function to dismiss alerts
        function dismissAlert(id) {
            const alert = document.getElementById(id);
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }
        }

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const successAlert = document.getElementById('success-alert');
                const errorAlert = document.getElementById('error-alert');

                if (successAlert) dismissAlert('success-alert');
                if (errorAlert) dismissAlert('error-alert');
            }, 5000);
        });

        document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.classList.contains('btn-loading')) return;

                this.classList.add('btn-loading');
                this.style.pointerEvents = 'none';

                // Remove loading state after form submission
                setTimeout(() => {
                    this.classList.remove('btn-loading');
                    this.style.pointerEvents = 'auto';
                }, 2000);
            });
        });
    </script>
</body>
</html>
