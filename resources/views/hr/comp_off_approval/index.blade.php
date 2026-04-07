<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CompOff Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Enhanced styling with modern design system */
        :root {
            --primary-color: #0e3a69;
            --primary-light: #1e4a79;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-bg: #f8f9fa;
            --card-shadow: 0 8px 25px rgba(0,0,0,0.1);
            --hover-shadow: 0 12px 35px rgba(0,0,0,0.15);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

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

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        .approval-container {
            width: 100%;
            max-width: 1100px;
            background: #fff;
            padding: 3rem;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .approval-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        }

        .approval-container:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-2px);
        }

        h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border-radius: 2px;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 0;
        }

        .table-faded th {
             background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%);
            color: #060606 !important;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.2rem 1rem;
            border: none;
            position: relative;
        }

        .table tbody tr {
            transition: var(--transition);
            border: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            border: none;
            border-bottom: 1px solid #e9ecef;
            font-weight: 500;
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .status-pending {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-approved {
            background: linear-gradient(135deg, #d4edda, #00b894);
            color: #155724;
            border: 1px solid #00b894;
        }

        .status-rejected {
            background: linear-gradient(135deg, #f8d7da, #e17055);
            color: #721c24;
            border: 1px solid #e17055;
        }

        /* Enhanced buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: var(--transition);
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .text-muted {
            color: #6c757d !important;
            font-style: italic;
            font-weight: 500;
        }

        /* Empty state styling */
        .empty-state {
            padding: 3rem 2rem;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .approval-container {
                padding: 2rem 1rem;
                margin: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            .table-responsive {
                border-radius: 12px;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
                margin: 0.2rem;
            }
        }

        /* Loading animation for form submissions */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Fade in animation */
        .approval-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    @include('hr.Components.sidebar')
    <div class="main-content">
        @include('hr.Components.header')

        <div class="main-wrapper">
            <div class="approval-container">
                <h1 class="text-center">
                    <i class="fas fa-clipboard-check me-3"></i>
                    CompOff Approval Requests
                </h1>

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table text-center align-middle">
                        <thead class="table-faded">
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Employee</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>Worked On</th>
                                <th><i class="fas fa-calendar-day me-2"></i>Requested For</th>
                                <th><i class="fas fa-calendar-times me-2"></i>Expires On</th>
                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($compOffRequests as $compOff)
                                <tr>
                                    <td>
                                        <strong>{{ $compOff->user->name }}</strong>
                                    </td>
                                    <td>{{ $compOff->worked_on->format('Y-m-d') }}</td>
                                    <td>{{ $compOff->requested_for->format('Y-m-d') }}</td>
                                    <td>{{ $compOff->expires_on ? $compOff->expires_on->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <!-- Added status badges instead of plain text -->
                                        @php
                                            $status = strtolower(trim($compOff->status));
                                            $badgeClass = match($status) {
                                                'pending' => 'status-pending',
                                                'approved' => 'status-approved',
                                                'rejected' => 'status-rejected',
                                                default => 'status-pending'
                                            };
                                        @endphp
                                        <span class="status-badge {{ $badgeClass }}">
                                            {{ ucfirst($compOff->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ((trim($compOff->status)) === 'Pending')
                                          <form action="{{ route('hr.compoff.approvals.update', $compOff->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to approve this CompOff request?');">
        @csrf
        @method('PATCH')
        <input type="hidden" name="action" value="Approved">
        <button type="submit" class="btn btn-success btn-sm">Approve</button>
    </form>
    <form action="{{ route('hr.compoff.approvals.update', $compOff->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to reject this CompOff request?');">
        @csrf
        @method('PATCH')
        <input type="hidden" name="action" value="Rejected">
        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
    </form>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-lock me-1"></i>
                                                No actions available
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <div class="mt-2">
                                            <strong>No CompOff requests found.</strong>
                                            <p class="mb-0">There are currently no pending approval requests.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Added interactive JavaScript for better UX -->



</body>
</html>
