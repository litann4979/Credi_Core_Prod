
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply for Comp Off</title>
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
        .form-input[readonly] {
            background: #e2e8f0; /* slate-200 */
            cursor: not-allowed;
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
        .status-Pending {
            background: #fef3c7; /* amber-100 */
            color: #d97706; /* amber-600 */
        }
        .status-Approved {
            background: #dcfce7; /* green-100 */
            color: #15803d; /* green-700 */
        }
        .status-Rejected {
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
    @include('Opearation.Components.sidebar')
    <div class="main-content">
        @include('Opearation.Components.header')
        <div class="w-full max-w-7xl mx-auto">
            <h1 class="page-title">Apply for Comp Off</h1>

            <!-- Comp Off Application Form -->
            <div class="dashboard-card p-8 mb-8">
                <div class="card-header p-6 -m-8 mb-6">
                    <h2 class="section-title mb-0"><i class="fas fa-calendar-check mr-2 text-blue-600"></i>Comp Off Application Form</h2>
                </div>
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="compOffForm" method="POST" action="{{ route('operations.comp-off.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" class="form-input w-full" value="{{ $userName }}" readonly>
                    </div>
                    <div>
                        <label for="worked_on" class="block text-sm font-medium text-gray-700 mb-1">Worked On</label>
                        <input type="text" name="worked_on" id="worked_on" class="form-input w-full flatpickr" required>
                    </div>
                    <div>
                        <label for="requested_for" class="block text-sm font-medium text-gray-700 mb-1">Requested For</label>
                        <input type="text" name="requested_for" id="requested_for" class="form-input w-full flatpickr" required>
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-4">
                        <button type="button" onclick="window.location.href='{{ route('operations.comp-off.index') }}'" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Submit Comp Off</button>
                    </div>
                </form>
            </div>

            <!-- Recent Comp Off Histories -->
            <div class="dashboard-card p-8">
                <div class="card-header p-6 -m-8 mb-6">
                    <h2 class="section-title mb-0"><i class="fas fa-history mr-2 text-teal-600"></i>Recent Comp Off Histories</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Worked On</th>
                                <th>Requested For</th>
                                <th>Status</th>
                                <th>Approver Name</tr>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($compOffHistories as $compOff)
                                <tr>
                                    <td>{{ $compOff->worked_on ? $compOff->worked_on->format('Y-m-d') : 'N/A' }}</td>
                                    <td>{{ $compOff->requested_for ? $compOff->requested_for->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $compOff->status }}">
                                            {{ ucfirst($compOff->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $compOff->approver->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No comp-off history available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- <div class="pagination-controls">
                    {{ $compOffHistories->links() }}
                </div> --}}
            </div>
        </div>
    </div>

    <script>
        // Initialize Flatpickr for date inputs
        flatpickr('#worked_on', {
            dateFormat: 'Y-m-d',
            maxDate: 'today',
        });
        flatpickr('#requested_for', {
            dateFormat: 'Y-m-d',
            minDate: 'tomorrow',
        });

        // Confirmation prompt before form submission
        document.getElementById('compOffForm').addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to submit this comp-off application?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
