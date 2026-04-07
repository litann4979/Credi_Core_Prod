<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply for Leave</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #f97316; 
            --primary-hover: #ea580c;
            --secondary-bg: #f3f4f6;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --white: #ffffff;
            --border-color: #e5e7eb;
            --sidebar-width: 280px;
            --header-height: 80px;
        }

        body {
            background-color: var(--secondary-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            margin: 0;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .page-wrapper {
            padding: 1.5rem 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Card Styling */
        .content-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--white);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body-custom {
            padding: 2rem;
        }

        /* Form Styling */
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        /* Buttons */
        .btn-primary-custom {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary-custom:hover { background: var(--primary-hover); color: white; }

        /* Table Styling */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom th {
            background: #f9fafb; color: var(--text-muted); font-weight: 600;
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;
            padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); white-space: nowrap;
        }
        .table-custom td {
            padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color);
            vertical-align: middle; color: var(--text-dark); font-size: 0.875rem;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tr:hover td { background-color: #fff7ed; }

        /* Status Badges */
        .badge-custom { padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
        .status-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .status-approved { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .status-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        /* Alerts */
        .alert-custom { border: none; border-radius: 12px; padding: 1rem 1.5rem; font-size: 0.9rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
        .alert-success-custom { background-color: #ecfdf5; color: #065f46; }
        .alert-error-custom { background-color: #fef2f2; color: #991b1b; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .content-card { margin-bottom: 1rem; }
            .card-body-custom { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    @include('hr.Components.sidebar')

    <div class="main-content">
        @include('hr.Components.header')

        <div class="page-wrapper">
            
            @if (session('success'))
                <div class="alert alert-custom alert-success-custom">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-custom alert-error-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-plus text-warning"></i> Apply for Leave
                    </h5>
                </div>
                <div class="card-body-custom">
                    <form id="leaveForm" method="POST" action="{{ route('hr.leave.store') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="leave_type" class="form-label">Leave Type</label>
                                <select name="leave_type" id="leave_type" class="form-select" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="casual_leave">Casual Leave</option>
                                    <option value="sick_leave">Sick Leave</option>
                                    <option value="maternity_leave">Maternity Leave</option>
                                    <option value="paid_leave">Paid Leave</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar text-muted"></i></span>
                                    <input type="text" name="start_date" id="start_date" class="form-control border-start-0 flatpickr" required placeholder="Select date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar text-muted"></i></span>
                                    <input type="text" name="end_date" id="end_date" class="form-control border-start-0 flatpickr" required placeholder="Select date">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="reason" class="form-label">Reason for Leave</label>
                                <textarea name="reason" id="reason" rows="3" class="form-control" required placeholder="Please provide a detailed reason..."></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <a href="{{ route('hr.leave.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn-primary-custom">
                                    <i class="fas fa-paper-plane"></i> Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="card-title">
                        <i class="fas fa-history text-primary"></i> Recent Leave History
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Decision Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaveHistories as $leave)
                                <tr>
                                    <td>{{ ucwords(str_replace('_', ' ', $leave->leave_type)) }}</td>
                                    <td>{{ $leave->start_date->format('d M, Y') }}</td>
                                    <td>{{ $leave->end_date->format('d M, Y') }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td><span class="text-muted small">{{ Str::limit($leave->reason, 50) }}</span></td>
                                    <td>
                                        @if($leave->status === 'pending')
                                            <span class="badge-custom status-pending"><i class="fas fa-clock me-1"></i> Pending</span>
                                        @elseif($leave->status === 'approved')
                                            <span class="badge-custom status-approved"><i class="fas fa-check-circle me-1"></i> Approved</span>
                                        @else
                                            <span class="badge-custom status-rejected"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($leave->decision_date)
                                            {{ $leave->decision_date->format('d M, Y h:i A') }}
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No leave history available.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $leaveHistories->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        // Initialize Date Picker
        flatpickr(".flatpickr", {
            dateFormat: "Y-m-d",
            minDate: "today",
            allowInput: true
        });

        // Form Confirmation
        document.getElementById('leaveForm').addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to submit this leave application?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>