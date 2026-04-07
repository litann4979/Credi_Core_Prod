<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Leads Details</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            overflow-x: hidden;
        }

        /* Layout */
        .main-content {
            margin-left: 280px;
            padding-top: 80px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .page-wrapper {
            padding: 1.5rem 2rem;
            max-width: 100%;
            margin: 0 auto;
        }

        /* Cards */
        .card-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            margin-bottom: 24px;
            overflow: hidden;
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
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: var(--text-dark);
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary-custom:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: var(--primary);
        }

        /* Status & Action Buttons inside Modal */
        .btn-authorize { background: #8b5cf6; color: white; }
        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #ef4444; color: white; }
        .btn-disburse { background: #059669; color: white; }
        .btn-future { background: #f59e0b; color: white; }
        
        .modal-footer button {
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .modal-footer button:hover { transform: translateY(-2px); opacity: 0.9; color: white;}
        .modal-footer button:disabled { background: #e5e7eb; color: #9ca3af; transform: none; cursor: not-allowed; }

        /* Modern Table */
        .table-responsive {
            overflow-x: auto;
        }

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: #f9fafb;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
            min-width: 140px;
        }

        .table-modern tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
            font-size: 0.85rem;
            vertical-align: middle;
            font-weight: 500;
        }

        .table-modern tbody tr:hover td {
            background-color: #fff7ed; /* Very light orange tint */
        }

        /* Filter Inputs inside Table Headers */
        .filter-item label {
            display: block;
            font-size: 0.7rem;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .filter-item select, .filter-item input {
            width: 100%;
            padding: 6px 8px;
            font-size: 0.8rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            background-color: white;
            color: var(--text-dark);
        }

        .filter-item select:focus, .filter-item input:focus {
            border-color: var(--primary);
            outline: none;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-badge.personal_lead { background: #dbeafe; color: #1e40af; }
        .status-badge.authorized { background: #ede9fe; color: #5b21b6; }
        .status-badge.login { background: #fef3c7; color: #92400e; }
        .status-badge.approved { background: #dcfce7; color: #166534; }
        .status-badge.rejected { background: #fee2e2; color: #991b1b; }
        .status-badge.disbursed { background: #ccfbf1; color: #0f766e; }
        .status-badge.future_lead { background: #ffedd5; color: #9a3412; }

        /* Modal Styles (Custom to match your JS logic but restyled) */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }

        .modal-container {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 1100px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .modal-overlay.active .modal-container { transform: translateY(0); }
        .modal-container.modal-sm { max-width: 500px; }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            border-radius: 16px 16px 0 0;
        }
        .modal-title { font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin: 0; }
        .modal-close { background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer; }
        .modal-close:hover { color: var(--primary); }

        .modal-content {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            background: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-radius: 0 0 16px 16px;
        }

        /* Lead Detail specific grid */
        .lead-detail-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
        }
        
        /* Left Column Profile */
        .lead-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--bg-light);
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .contact-item i { color: var(--primary); }
        .contact-item input { border: none; background: transparent; width: 100%; outline: none; }

        /* Detail Items */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .detail-item {
            background: #fff;
            border: 1px solid var(--border);
            padding: 12px;
            border-radius: 10px;
        }
        .detail-item label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .editable-field {
            width: 100%;
            border: none;
            background: transparent;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
            outline: none;
        }
        .editable-field:disabled { background: transparent; }
        .editable-field:not(:disabled) {
            border-bottom: 2px solid var(--primary);
            background: #fff7ed;
        }

        /* Document List */
        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 8px;
            background: #fff;
        }
        .document-item a { color: var(--primary); text-decoration: none; font-weight: 500; font-size: 0.85rem;}
        .document-item button { font-size: 0.75rem; padding: 4px 8px; }

        /* Loading Spinner */
        #loadingOverlay {
            display: none;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10000;
            justify-content: center; align-items: center;
        }
        #loadingOverlay.active { display: flex; }
        .spinner {
            width: 40px; height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Notification Toast */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100000;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
            transition: opacity 0.3s ease;
        }
        .notification.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .notification.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Mobile */
        @media (max-width: 992px) {
            .lead-detail-grid { grid-template-columns: 1fr; }
            .modal-footer { flex-direction: column; }
            .modal-footer button { width: 100%; }
            .main-content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>

    @include('admin.Components.sidebar')

    <div class="main-content">
        @include('admin.Components.header')

        <div class="page-wrapper">
            
            <div class="card-box p-4 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div>
                        <h1 class="section-title mb-1">
                            <i class="fas fa-users text-warning me-2"></i> 
                            {{ ucfirst(str_replace('_', ' ', $status)) }} Leads
                        </h1>
                        <p class="text-muted small mb-0">Manage and track your lead status details</p>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <div class="bg-orange-50 text-orange-700 px-3 py-2 rounded-3 border border-orange-100 shadow-sm text-sm fw-bold">
                            Total: <span id="totalLeadsCount">{{ count($leads) }}</span>
                        </div>
                        <div class="bg-green-50 text-green-700 px-3 py-2 rounded-3 border border-green-100 shadow-sm text-sm fw-bold">
                            Value: <span id="totalAmountDisplay">₹0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <a href="{{ route('admin.dashboard', [
                    'table_lead_type' => $leadType,
                    'table_from_date' => $fromDate,
                    'table_to_date' => $toDate,
                    'table_month' => $month
                ]) }}" class="btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>

                <div class="d-flex gap-2 flex-grow-1 justify-content-md-end">
                    <div class="position-relative" style="max-width: 300px; width: 100%;">
                        <input type="text" id="executiveSearch" placeholder="Search rows..." class="form-control ps-5" onkeyup="applyFilters()">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>
                    <a href="{{ route('admin.leads-details', ['status' => $status, 'lead_type' => $leadType, 'from_date' => $fromDate, 'to_date' => $toDate, 'month' => $month]) }}" class="btn-secondary-custom text-danger border-danger">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>

            <div class="card-box">
                <form method="GET" action="{{ route('admin.leads-details') }}" id="filterForm">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="hidden" name="lead_type" value="{{ $leadType }}">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
                    <input type="hidden" name="month" value="{{ $month }}">

                    <div class="table-responsive">
                        <table class="table-modern leads-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="filter-item">
                                            <label>EXECUTIVE</label>
                                            <select name="executive" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($executives as $exec)
                                                    <option value="{{ $exec['id'] }}" {{ $executiveFilter == $exec['id'] ? 'selected' : '' }}>{{ strtoupper($exec['name']) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>NAME</label>
                                            <select name="name" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($names as $name)
                                                    <option value="{{ $name }}" {{ $nameFilter == $name ? 'selected' : '' }}>{{ strtoupper($name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>PHONE</label>
                                            <select name="phone" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($phones as $phone)
                                                    <option value="{{ $phone }}" {{ $phoneFilter == $phone ? 'selected' : '' }}>{{ $phone }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>LOAN AC</label>
                                            <select name="loan_account" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($loanAccounts as $account)
                                                    <option value="{{ $account }}" {{ $loanAccountFilter == $account ? 'selected' : '' }}>{{ $account }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>COMPANY</label>
                                            <select name="company" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company }}" title="{{ $company }}" {{ $companyFilter == $company ? 'selected' : '' }}>
                                                        {{ Str::limit(strtoupper($company), 15) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>AMOUNT</label>
                                            <select name="loan_amount" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                <option value="1-1000" {{ request('loan_amount') == '1-1000' ? 'selected' : '' }}>&lt; 1K</option>
                                                <option value="1000-10000" {{ request('loan_amount') == '1000-10000' ? 'selected' : '' }}>1K - 10K</option>
                                                <option value="10000-100000" {{ request('loan_amount') == '10000-100000' ? 'selected' : '' }}>10K - 1L</option>
                                                <option value="100000-1000000" {{ request('loan_amount') == '100000-1000000' ? 'selected' : '' }}>1L - 10L</option>
                                                <option value="1000000+" {{ request('loan_amount') == '1000000+' ? 'selected' : '' }}>&gt; 10L</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>STATUS</label>
                                            <select name="status_filter" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($statuses as $statusValue)
                                                    <option value="{{ $statusValue }}" {{ $statusFilter == $statusValue ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statusValue)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>TYPE</label>
                                            <select name="lead_type_filter" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($formattedLeadTypes as $type)
                                                    <option value="{{ $type['value'] }}" {{ $leadTypeFilter == $type['value'] ? 'selected' : '' }}>{{ $type['display'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item">
                                            <label>BANK</label>
                                            <select name="bank" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">All</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank }}" {{ $bankFilter == $bank ? 'selected' : '' }}>{{ strtoupper($bank) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="filter-item" style="cursor: pointer;" onclick="toggleDateFilter()">
                                            <label>UPDATED AT <i class="fas fa-filter ms-1"></i></label>
                                            <span>Date</span>
                                            <div id="dateFilterContainer" class="position-absolute bg-white shadow p-2 border rounded" style="display: none; right: 10px; z-index: 10; min-width: 180px;">
                                                <input type="date" id="fromDate" class="form-control form-control-sm mb-1" onclick="event.stopPropagation()">
                                                <input type="date" id="toDate" class="form-control form-control-sm mb-1" onclick="event.stopPropagation()">
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="btn btn-sm btn-primary flex-grow-1" onclick="event.stopPropagation(); applyDateFilter()">Apply</button>
                                                    <button type="button" class="btn btn-sm btn-light flex-grow-1" onclick="event.stopPropagation(); clearDateFilter()">Clear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                    <tr class="cursor-pointer" data-lead-id="{{ $lead['id'] }}" data-date="{{ $lead['updated_at'] }}">
                                        <td>{{ strtoupper($lead['employee_name']) }}</td>
                                        <td class="fw-bold">{{ strtoupper($lead['name']) }}</td>
                                        <td>{{ $lead['phone'] }}</td>
                                        <td>{{ $lead['loan_account_number'] ? strtoupper($lead['loan_account_number']) : '-' }}</td>
                                        <td title="{{ $lead['company_name'] }}">
                                            <div class="text-truncate" style="max-width: 150px;">{{ strtoupper($lead['company_name']) }}</div>
                                        </td>
                                        <td class="text-success fw-bold">{{ $lead['lead_amount'] }}</td>
                                        <td><span class="status-badge {{ $lead['status'] }}">{{ ucfirst(str_replace('_', ' ', $lead['status'])) }}</span></td>
                                        <td>{{ strtoupper($lead['lead_type']) }}</td>
                                        <td>{{ strtoupper($lead['bank_name']) }}</td>
                                        <td>{{ $lead['updated_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="leadDetailModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Lead Details</h2>
                <button class="modal-close" onclick="closeModal('leadDetailModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-content">
                <div class="lead-detail-grid">
                    <div class="bg-white">
                        <div class="text-center mb-4">
                            <div class="lead-avatar-large mx-auto" id="modalLeadInitials">AB</div>
                            <h3 class="h5 fw-bold mb-1" id="modalLeadName">--</h3>
                            <p class="text-muted small">Lead ID: <span id="documentLeadIdDisplay"></span></p>
                        </div>

                        <div class="vstack gap-2">
                            <div class="contact-item"><i class="fas fa-phone"></i> <input id="modalLeadPhone" class="editable-field" disabled></div>
                            <div class="contact-item"><i class="fas fa-envelope"></i> <input id="modalLeadEmail" class="editable-field" disabled></div>
                            
                            <div class="contact-item">
                                <i class="fas fa-university"></i>
                                <div class="w-100">
                                    <input type="text" id="modalLeadBank" class="editable-field" disabled>
                                    <select id="modalLeadBankDropdown" class="form-select form-select-sm" style="display:none;" onchange="handleBankChange(this)">
                                        <option value="">Select Bank</option>
                                        @foreach($banksName as $bank)
                                            <option value="{{ $bank }}">{{ strtoupper($bank) }}</option>
                                        @endforeach
                                        <option value="Other">Other</option>
                                    </select>
                                    <input type="text" id="modalLeadBankCustom" class="form-control form-control-sm mt-1" placeholder="Enter Bank Name" style="display:none;">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded border">
                            <h6 class="small fw-bold text-uppercase mb-3"><i class="fas fa-history text-primary me-1"></i> Follow-ups</h6>
                            <div id="followupList" class="small text-muted" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-3 text-uppercase small fw-bold text-muted border-bottom pb-2">Financial Info</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Loan Amount</label>
                                <input type="number" id="modalLeadAmount" class="editable-field" disabled>
                            </div>
                            <div class="detail-item">
                                <label>In Words</label>
                                <span id="modalLeadAmountInWords" class="text-primary fw-bold small"></span>
                            </div>
                            <div class="detail-item">
                                <label>Salary</label>
                                <input type="number" id="modalLeadSalary" class="editable-field" disabled>
                            </div>
                            <div class="detail-item">
                                <label>Turnover</label>
                                <input type="number" id="modalLeadTurnoverAmount" class="editable-field" disabled>
                            </div>
                            <div class="detail-item">
                                <label>Expected Month</label>
                                <span id="modalLeadExpectedMonthDisplay" class="fw-medium text-body">N/A</span>
                                <select id="modalLeadExpectedMonth" class="form-select form-select-sm" style="display:none;">
                                    <option value="">Select month</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
                        </div>

                        <h4 class="mb-3 text-uppercase small fw-bold text-muted border-bottom pb-2 mt-4">Personal Info</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>DOB</label>
                                <input type="date" id="modalLeadDob" class="editable-field" disabled>
                            </div>
                            <div class="detail-item">
                                <label>Company</label>
                                <input type="text" id="modalLeadCompany" class="editable-field" disabled>
                            </div>
                            
                            <div class="detail-item">
                                <label>State</label>
                                <input type="text" id="modalLeadState" class="editable-field" disabled>
                                <select id="modalLeadStateDropdown" class="form-select form-select-sm" style="display:none;" onchange="loadDistricts(this.value)"></select>
                            </div>
                            <div class="detail-item">
                                <label>District</label>
                                <input type="text" id="modalLeadDistrict" class="editable-field" disabled>
                                <select id="modalLeadDistrictDropdown" class="form-select form-select-sm" style="display:none;" onchange="loadCities(this.value)"></select>
                            </div>
                            <div class="detail-item">
                                <label>City</label>
                                <input type="text" id="modalLeadCity" class="editable-field" disabled>
                                <select id="modalLeadCityDropdown" class="form-select form-select-sm" style="display:none;"></select>
                            </div>
                        </div>

                        <h4 class="mb-3 text-uppercase small fw-bold text-muted border-bottom pb-2 mt-4">Status & Documents</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Current Status</label>
                                <span id="modalLeadStatus" class="status-badge"></span>
                            </div>
                            <div class="detail-item">
                                <label>Type</label>
                                <span id="modalLeadTypeDisplay"></span>
                                <select id="modalLeadType" class="form-select form-select-sm" style="display:none;">
                                    <option value="personal_loan">Personal Loan</option>
                                    <option value="business_loan">Business Loan</option>
                                    <option value="home_loan">Home Loan</option>
                                </select>
                            </div>
                            <div class="detail-item">
                                <label>Loan Account No</label>
                                <input type="text" id="modalLeadAccountNumber" class="editable-field" disabled>
                            </div>
                            <div class="detail-item">
                                <label>Voice Recording</label>
                                <span id="modalLeadVoiceRecording"></span>
                            </div>
                        </div>

                         <input type="hidden" id="modalName">
                         
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-bold">Documents</label>
                                <button class="btn btn-sm btn-outline-primary" id="addDocumentButton" onclick="openAddDocumentModal()">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                            <div id="documentList" class="bg-light p-3 rounded border"></div>
                        </div>
                        
                        <div id="rejectionReasonSection" style="display:none;" class="mt-3 p-3 bg-danger-subtle text-danger rounded border border-danger">
                            <strong>Reason for Rejection:</strong> <p class="mb-0" id="modalLeadReason"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary-custom" id="editLeadButton" onclick="enableLeadEdit()"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn-success" id="saveLeadButton" style="display:none;" onclick="saveLeadChanges()"><i class="fas fa-save"></i> Save</button>
                <button class="btn-reject" id="deleteButton" onclick="showDeleteModal(currentLeadId)"><i class="fas fa-trash"></i> Delete</button>
                
                @if(auth()->user()->hasDesignation('admin'))
                    <button class="btn-secondary-custom" id="loginButton" onclick="showLoginModal(currentLeadId)"><i class="fas fa-sign-in-alt"></i> Login</button>
                    <button class="btn-authorize" id="authorizeButton" onclick="showAuthorizeModal(currentLeadId)"><i class="fas fa-check-double"></i> Authorize</button>
                    <button class="btn-approve" id="approveButton" onclick="showApproveModal(currentLeadId)"><i class="fas fa-check-circle"></i> Approve</button>
                    <button class="btn-reject" id="rejectButton" onclick="showRejectModal(currentLeadId)"><i class="fas fa-times-circle"></i> Reject</button>
                    <button class="btn-disburse" id="disburseButton" onclick="showDisburseModal(currentLeadId)"><i class="fas fa-rupee-sign"></i> Disburse</button>
                    <button class="btn-future" id="futureLeadButton" onclick="showFutureLeadModal(currentLeadId)"><i class="fas fa-clock"></i> Future</button>
                    <button class="btn-primary-custom" id="forwardOperationsButton" onclick="forwardToOperations(currentLeadId)"><i class="fas fa-share"></i> Forward Ops</button>
                @endif
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="authorizeModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Authorize Lead</h2>
                <button class="modal-close" onclick="closeDocModal('authorizeModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <i class="fas fa-check-double text-primary fa-3x mb-3"></i>
                <p>Are you sure you want to authorize this lead?</p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('authorizeModal')">Cancel</button>
                <button class="btn-authorize" onclick="confirmAuthorize()">Authorize</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="loginModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Login Lead</h2>
                <button class="modal-close" onclick="closeDocModal('loginModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p class="text-center mb-3" id="loginModalMessage"></p>
                <input type="text" id="loanAccountNumber" class="form-control" placeholder="Enter Loan Account No">
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('loginModal')">Cancel</button>
                <button class="btn-primary-custom" onclick="confirmLogin()">Login Lead</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="approveModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Approve Lead</h2>
                <button class="modal-close" onclick="closeDocModal('approveModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p class="text-center mb-3" id="approveModalMessage"></p>
                <input type="text" id="loanAccountNumber1" class="form-control" placeholder="Confirm Loan Account No">
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('approveModal')">Cancel</button>
                <button class="btn-approve" onclick="confirmApprove()">Approve</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="rejectModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Reject Lead</h2>
                <button class="modal-close" onclick="closeDocModal('rejectModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p class="text-center mb-2">Please provide a reason for rejection:</p>
                <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('rejectModal')">Cancel</button>
                <button class="btn-reject" onclick="confirmReject()">Reject</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="disburseModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Disburse Lead</h2>
                <button class="modal-close" onclick="closeDocModal('disburseModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p class="text-center mb-3" id="disburseModalMessage"></p>
                <input type="text" id="loanAccountNumber2" class="form-control" placeholder="Confirm Loan Account No">
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('disburseModal')">Cancel</button>
                <button class="btn-disburse" onclick="confirmDisburse()">Disburse</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="futureLeadModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Mark as Future</h2>
                <button class="modal-close" onclick="closeDocModal('futureLeadModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <i class="fas fa-clock text-warning fa-3x mb-3"></i>
                <p>Mark this lead as Future Lead?</p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('futureLeadModal')">Cancel</button>
                <button class="btn-future" onclick="confirmFutureLead()">Confirm</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="forwardOperationsModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Forward to Operations</h2>
                <button class="modal-close" onclick="closeDocModal('forwardOperationsModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p class="mb-2">Optional Remarks:</p>
                <textarea id="operation-remarks" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('forwardOperationsModal')">Cancel</button>
                <button class="btn-primary-custom" onclick="submitForwardToOperations()">Forward</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="addDocumentModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Add Document</h2>
                <button class="modal-close" onclick="closeDocModal('addDocumentModal')">&times;</button>
            </div>
            <div class="modal-content">
                <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="lead_id" id="documentLeadId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Name</label>
                        <input type="text" id="documentName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Type</label>
                        <select id="documentType" name="type" class="form-select">
                            <option value="id_proof">ID Proof</option>
                            <option value="address_proof">Address Proof</option>
                            <option value="financial">Financial</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">File</label>
                        <input type="file" id="documentFile" name="document_file" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea id="documentDescription" name="description" class="form-control"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn-secondary-custom" onclick="closeDocModal('addDocumentModal')">Cancel</button>
                        <button type="submit" class="btn-primary-custom">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container modal-sm">
            <div class="modal-header bg-danger text-white">
                <h2 class="modal-title">Delete Lead</h2>
                <button class="modal-close text-white" onclick="closeDocModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                <p>Are you sure? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-custom" onclick="closeDocModal('deleteModal')">Cancel</button>
                <button class="btn-reject" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <div id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <script>
        // Forward to operations
        function forwardToOperations(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('operation-remarks').value = '';
            const modal = document.getElementById('forwardOperationsModal');
            if (modal) modal.classList.add('active');
        }

        function submitForwardToOperations() {
            const remarks = document.getElementById('operation-remarks').value;
            showLoading(true);

            fetch(`/admin/leads/${currentLeadId}/forward-to-operations-by-admin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ remarks })
            })
            .then(async response => {
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                showNotification(data.message || 'Lead forwarded to operations team successfully', 'success');
                closeDocModal('forwardOperationsModal');
                closeModal('leadDetailModal');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                console.error('Error forwarding lead:', error);
                showNotification(`Error forwarding lead: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        function numberToWords(number) {
            const units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const thousands = ['', 'Thousand', 'Lakh', 'Crore'];

            if (number === 0) return 'Zero Rupees';

            function convertLessThanThousand(num) {
                if (num === 0) return '';
                if (num < 10) return units[num];
                if (num < 20) return teens[num - 10];
                if (num < 100) {
                    return tens[Math.floor(num / 10)] + (num % 10 ? ' ' + units[num % 10] : '');
                }
                return units[Math.floor(num / 100)] + ' Hundred' + (num % 100 ? ' and ' + convertLessThanThousand(num % 100) : '');
            }

            let result = '';
            let thousandIndex = 0;

            while (number > 0) {
                if (thousandIndex === 0) {
                    if (number % 1000 > 0) {
                        result = convertLessThanThousand(number % 1000) + (result ? ' ' + thousands[thousandIndex] + ' ' + result : '');
                    }
                    number = Math.floor(number / 1000);
                } else {
                    if (number % 100 > 0) {
                        result = convertLessThanThousand(number % 100) + ' ' + thousands[thousandIndex] + (result ? ' ' + result : '');
                    }
                    number = Math.floor(number / 100);
                }
                thousandIndex++;
            }
            return result.trim() + ' Rupees';
        }

        window.currentLeadId = null;

        document.addEventListener('DOMContentLoaded', function () {
            calculateTotalAmount();
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function() {
                    // Logic handled by page reload
                });
            }
            document.querySelectorAll('tbody tr[data-lead-id]').forEach(row => {
                row.addEventListener('click', function (e) {
                    if (e.target.closest('button')) return;
                    const leadId = this.dataset.leadId;
                    if (leadId) viewLeadDetails(leadId);
                });
            });

            document.getElementById('addDocumentForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const fileInput = document.getElementById('documentFile');
                const file = fileInput.files[0];
                const maxSize = 9 * 1024 * 1024;
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

                if (file && file.size > maxSize) {
                    showNotification('File size exceeds 9MB limit.', 'error');
                    return;
                }
                if (file && !allowedTypes.includes(file.type)) {
                    showNotification('Only PDF, JPG, and PNG files are allowed.', 'error');
                    return;
                }

                const formData = new FormData(this);
                const leadId = document.getElementById('documentLeadId').value;
                showLoading(true);

                fetch(`/admin/leads/${leadId}/documents`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
                    return res.json();
                })
                .then(data => {
                    showNotification(data.message || 'Document added successfully.', 'success');
                    closeDocModal('addDocumentModal');
                    setTimeout(() => viewLeadDetails(window.currentLeadId), 1000);
                })
                .catch(error => {
                    console.error("Error adding document:", error);
                    showNotification(`Failed to add document: ${error.message}`, 'error');
                })
                .finally(() => showLoading(false));
            });

            const leadAmountInput = document.getElementById('modalLeadAmount');
            leadAmountInput.addEventListener('input', function () {
                if (!this.disabled) {
                    const amount = parseInt(this.value);
                    document.getElementById('modalLeadAmountInWords').textContent = isNaN(amount) || amount <= 0 ? 'N/A' : numberToWords(amount);
                }
            });

            const nameInput = document.getElementById('modalName');
            nameInput.addEventListener('input', function () {
                if (!this.disabled) {
                    this.value = this.value.toUpperCase();
                    const name = this.value.trim();
                    const properName = name ? name.toLowerCase().replace(/(^|\s)\w/g, char => char.toUpperCase()) : 'N/A';
                    document.getElementById('modalLeadName').textContent = properName;
                    document.getElementById('modalLeadInitials').textContent = properName && properName !== 'N/A' ? properName.charAt(0).toUpperCase() : '';
                }
            });
        });

        function setButtonStates(lead) {
            const isDisbursedOrFuture = ['disbursed', 'future_lead'].includes(lead.status);
            const btns = ['loginButton','authorizeButton','approveButton','rejectButton','disburseButton','futureLeadButton','forwardOperationsButton','addDocumentButton'];
            btns.forEach(id => {
                const el = document.getElementById(id);
                if(el) el.disabled = isDisbursedOrFuture;
            });
        }

        function viewLeadDetails(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('documentLeadId').value = leadId;
            const modal = document.getElementById('leadDetailModal');
            const docList = document.getElementById('documentList');

            if (!modal || !docList) {
                console.error('Modal or document list element not found.');
                showNotification('Failed to open lead details.', 'error');
                return;
            }

            modal.classList.add('active');
            docList.innerHTML = '<p>Loading documents...</p>';
            showLoading(true);

            fetch(`/admin/leads/${leadId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) return res.text().then(text => { throw new Error(`HTTP ${res.status}: ${text}`); });
                const contentType = res.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) return res.json();
                else return res.text().then(text => { throw new Error('Response is not JSON'); });
            })
            .then(data => {
                const lead = data.lead;
                document.getElementById('documentLeadIdDisplay').textContent = lead.id;
                document.getElementById('modalLeadName').textContent = lead.name ?? 'N/A';
                document.getElementById('modalName').value = lead.name ? lead.name.toUpperCase() : 'N/A';
                document.getElementById('modalLeadInitials').textContent = lead.name ? lead.name.charAt(0).toUpperCase() : '';
                document.getElementById('modalLeadCompany').value = lead.company_name ?? 'N/A';
                document.getElementById('modalLeadPhone').value = lead.phone ?? 'N/A';
                document.getElementById('modalLeadEmail').value = lead.email ?? '';
                document.getElementById('modalLeadDob').value = lead.dob ?? '';
                document.getElementById('modalLeadState').value = lead.state ?? '';
                document.getElementById('modalLeadDistrict').value = lead.district ?? '';
                document.getElementById('modalLeadCity').value = lead.city ?? '';
                document.getElementById('modalLeadAmount').value = lead.lead_amount ? `${lead.lead_amount}` : '';
                document.getElementById('modalLeadAmountInWords').textContent = lead.lead_amount ? numberToWords(parseInt(lead.lead_amount)) : 'N/A';
                
                const statusEl = document.getElementById('modalLeadStatus');
                statusEl.textContent = lead.status ? lead.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';
                statusEl.className = `status-badge ${lead.status}`;
                
                setExpectedMonthUI(lead.expected_month);
                const leadTypeDisplay = lead.lead_type ? lead.lead_type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';
                document.getElementById('modalLeadTypeDisplay').textContent = leadTypeDisplay;
                document.getElementById('modalLeadType').value = lead.lead_type || '';
                document.getElementById('modalLeadAccountNumber').value = lead.loan_account_number ?? '';
                document.getElementById('modalLeadTurnoverAmount').value = lead.turnover_amount ? `${lead.turnover_amount}` : '';
                document.getElementById('modalLeadSalary').value = lead.salary ? `${lead.salary}` : '';
                document.getElementById('modalLeadBank').value = lead.bank_name ?? '';

                const voiceElement = document.getElementById('modalLeadVoiceRecording');
                if (lead.voice_recording) {
                    voiceElement.innerHTML = `<audio controls style="width:100%;"><source src="${lead.voice_recording}" type="audio/mpeg"></audio>`;
                } else {
                    voiceElement.textContent = 'N/A';
                }


                const employeeNameEl = document.getElementById('modalLeadEmployeeName');
                if (employeeNameEl) employeeNameEl.textContent = lead.employee_name ?? 'N/A';
                
                const teamLeadNameEl = document.getElementById('modalLeadTeamLeadName');
                if (teamLeadNameEl) teamLeadNameEl.textContent = lead.team_lead_name ?? 'N/A';


                const rejectionReasonSection = document.getElementById('rejectionReasonSection');
                document.getElementById('modalLeadReason').textContent = lead.rejection_reason ?? 'N/A';
                rejectionReasonSection.style.display = lead.status === 'rejected' ? 'block' : 'none';

                setButtonStates(lead);

                docList.innerHTML = '';
                if (data.documents && data.documents.length > 0) {
                    data.documents.forEach(doc => {
                        const docItem = document.createElement('div');
                        docItem.className = 'document-item';
                        docItem.innerHTML = `
                            <div>
                                <label>${doc.document_name}</label>
                                ${doc.filepath ?
                                    `<a href="${doc.filepath}" target="_blank"><i class="fas fa-eye"></i> View</a>` :
                                    `<form id="uploadForm_${doc.document_id}" method="POST" enctype="multipart/form-data" action="/admin/leads/${leadId}/documents/${doc.document_id}/upload">
                                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-upload"></i></button>
                                    </form>`}
                            </div>
                            ${doc.filepath && !['disbursed'].includes(lead.status) ? `<button class="btn btn-sm btn-danger" onclick="deleteDocument(${leadId}, ${doc.document_id})"><i class="fas fa-trash"></i></button>` : ''}
                        `;
                        docList.appendChild(docItem);
                    });
                } else {
                    docList.innerHTML = '<p class="text-muted p-2">No documents.</p>';
                }

                // Update follow-up section
                const followupList = document.getElementById('followupList');
                followupList.innerHTML = '';
                if (data.followUps && data.followUps.length > 0) {
                    data.followUps.forEach(fu => {
                        const formattedDate = new Date(fu.timestamp).toLocaleString('en-IN', {
                            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });
                        followupList.innerHTML += `
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1">${fu.message || 'No message'}</p>
                                ${fu.recording_path ? `<audio controls src="${fu.recording_path}" style="height:30px;width:100%"></audio>` : ''}
                                <div class="d-flex justify-content-between small text-muted mt-1">
                                    <span>${fu.user?.name ?? 'Unknown'}</span>
                                    <span>${formattedDate}</span>
                                </div>
                            </div>`;
                    });
                } else {
                    followupList.innerHTML = '<div class="text-center p-3">No follow-ups found.</div>';
                }

                // Attach upload listeners for new dynamic forms
              document.querySelectorAll('form[id^="uploadForm_"]').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        showLoading(true);
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
        .then(res => res.ok ? res.json() : res.json().then(e=> { throw new Error(e.message) }))
        .then(d => {
            showNotification(d.message || 'Uploaded.', 'success');
            setTimeout(() => viewLeadDetails(window.currentLeadId), 1000);
        })
        .catch(e => showNotification(e.message, 'error'))
        .finally(() => showLoading(false));
    });
});
            })
            .catch(error => {
                console.error("Error fetching lead details:", error);
                showNotification(`Failed to load: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        const EXPECTED_MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        function normalizeExpectedMonth(value) {
            if (value == null || String(value).trim() === '') return '';
            const t = String(value).trim();
            const found = EXPECTED_MONTH_NAMES.find(m => m.toLowerCase() === t.toLowerCase());
            return found || t;
        }

        function setExpectedMonthUI(monthRaw) {
            const disp = document.getElementById('modalLeadExpectedMonthDisplay');
            const sel = document.getElementById('modalLeadExpectedMonth');
            if (!disp || !sel) return;
            const normalized = normalizeExpectedMonth(monthRaw);
            sel.value = normalized && EXPECTED_MONTH_NAMES.includes(normalized) ? normalized : '';
            if (monthRaw != null && String(monthRaw).trim() !== '') {
                disp.textContent = normalized && EXPECTED_MONTH_NAMES.includes(normalized) ? normalized : String(monthRaw).trim();
            } else {
                disp.textContent = 'N/A';
            }
        }

        function enableLeadEdit() {
            const currentStatus = document.getElementById('modalLeadStatus').textContent.toLowerCase().replace(' ', '_');
            const isReadOnlyStatus = ['disbursed', 'rejected', 'future_lead'].includes(currentStatus);

            const fields = document.querySelectorAll('.editable-field');
            fields.forEach(field => {
                field.disabled = false;
            });

            document.getElementById('editLeadButton').style.display = 'none';
            document.getElementById('saveLeadButton').style.display = 'inline-flex';
            
            document.getElementById('modalLeadTypeDisplay').style.display = 'none';
            document.getElementById('modalLeadType').style.display = 'block';

            const expDisp = document.getElementById('modalLeadExpectedMonthDisplay');
            const expSel = document.getElementById('modalLeadExpectedMonth');
            if (expDisp && expSel) {
                if (isReadOnlyStatus) {
                    expDisp.style.display = 'inline-block';
                    expSel.style.display = 'none';
                    expSel.disabled = true;
                } else {
                    expDisp.style.display = 'none';
                    expSel.style.display = 'block';
                    expSel.disabled = false;
                }
            }

            // Show dropdowns
            ['State', 'District', 'City'].forEach(type => {
                document.getElementById(`modalLead${type}`).style.display = 'none';
                document.getElementById(`modalLead${type}Dropdown`).style.display = 'block';
            });

            // Bank Logic
            const currentBank = document.getElementById('modalLeadBank').value;
            const bankDropdown = document.getElementById('modalLeadBankDropdown');
            const bankCustom = document.getElementById('modalLeadBankCustom');
            
            document.getElementById('modalLeadBank').style.display = 'none';
            bankDropdown.style.display = 'block';
            bankDropdown.disabled = false;

            let exists = false;
            for(let i=0; i<bankDropdown.options.length; i++) {
                if(bankDropdown.options[i].value === currentBank) {
                    bankDropdown.selectedIndex = i;
                    exists = true; break;
                }
            }

            if(exists) {
                bankCustom.style.display = 'none';
            } else if (currentBank && currentBank !== 'N/A') {
                bankDropdown.value = 'Other';
                bankCustom.style.display = 'block';
                bankCustom.value = currentBank;
                bankCustom.disabled = false;
            } else {
                bankDropdown.value = "";
                bankCustom.style.display = 'none';
            }

            if (document.getElementById('modalLeadStateDropdown').options.length <= 1) loadStates();
        }

        function disableLeadEdit() {
            const fields = document.querySelectorAll('.editable-field');
            fields.forEach(field => field.disabled = true);
            document.getElementById('editLeadButton').style.display = 'inline-flex';
            document.getElementById('saveLeadButton').style.display = 'none';
            
            document.getElementById('modalLeadTypeDisplay').style.display = 'inline-block';
            document.getElementById('modalLeadType').style.display = 'none';

            const expDisp = document.getElementById('modalLeadExpectedMonthDisplay');
            const expSel = document.getElementById('modalLeadExpectedMonth');
            if (expDisp && expSel) {
                expDisp.style.display = 'inline-block';
                expSel.style.display = 'none';
                expSel.disabled = false;
            }

            // Revert dropdowns
            ['State', 'District', 'City'].forEach(type => {
                document.getElementById(`modalLead${type}`).style.display = 'block';
                document.getElementById(`modalLead${type}Dropdown`).style.display = 'none';
            });

            document.getElementById('modalLeadBank').style.display = 'block';
            document.getElementById('modalLeadBankDropdown').style.display = 'none';
            document.getElementById('modalLeadBankCustom').style.display = 'none';
        }

        // --- Location Logic (Keep existing implementation) ---
     async function loadStates() {
    try {
        console.log('Loading states...');

        const response = await fetch('/admin/location/states', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            },
            credentials: 'include'
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to load states');
        }

        const data = await response.json();
        console.log('States data:', data);

        const dropdown = document.getElementById('modalLeadStateDropdown');
        if (!dropdown) throw new Error('State dropdown element not found');

        // Clear and populate dropdown
        dropdown.innerHTML = '<option value="">Select State</option>';

        if (data.data && data.data.length > 0) {
            data.data.forEach(state => {
                const option = new Option(state.state_title, state.state_id);
                dropdown.add(option);
            });

            // Set current selection if available
            const currentState = document.getElementById('modalLeadState').value;
            if (currentState) {
                const selectedOption = [...dropdown.options].find(
                    opt => opt.text === currentState
                );
                if (selectedOption) {
                    selectedOption.selected = true;
                    await loadDistricts(selectedOption.value);
                }
            }
        } else {
            dropdown.innerHTML += '<option value="" disabled>No states available</option>';
        }
    } catch (error) {
        console.error('Error loading states:', error);
        showNotification(error.message || 'Failed to load states', 'error');
    }
}


async function loadDistricts(stateId) {
    if (!stateId) return;

    try {
        console.log(`Loading districts for state ${stateId}...`);

        const response = await fetch(`/admin/location/districts/${stateId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        console.log('Districts response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to load districts');
        }

        const data = await response.json();
        console.log('Districts data:', data);

        const dropdown = document.getElementById('modalLeadDistrictDropdown');
        if (!dropdown) throw new Error('District dropdown element not found');

        // Clear and populate dropdown
        dropdown.innerHTML = '<option value="">Select District</option>';

        if (data.data && data.data.length > 0) {
            data.data.forEach(district => {
                const option = new Option(district.district_title, district.districtid);
                dropdown.add(option);
            });

            // Set current selection if available
            const currentDistrict = document.getElementById('modalLeadDistrict').value;
            if (currentDistrict) {
                const selectedOption = [...dropdown.options].find(
                    opt => opt.text === currentDistrict
                );
                if (selectedOption) {
                    selectedOption.selected = true;
                    await loadCities(selectedOption.value);
                }
            }
        } else {
            dropdown.innerHTML += '<option value="" disabled>No districts available</option>';
        }
    } catch (error) {
        console.error('Error loading districts:', error);
        showNotification(error.message || 'Failed to load districts', 'error');
    }
}


async function loadCities(districtId) {
    if (!districtId) return;

    try {
        console.log(`Loading cities for district ${districtId}...`);

        const response = await fetch(`/admin/location/cities/${districtId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        console.log('Cities response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to load cities');
        }

        const data = await response.json();
        console.log('Cities data:', data);

        const dropdown = document.getElementById('modalLeadCityDropdown');
        if (!dropdown) throw new Error('City dropdown element not found');

        // Clear and populate dropdown
        dropdown.innerHTML = '<option value="">Select City</option>';

        if (data.data && data.data.length > 0) {
            data.data.forEach(city => {
                const option = new Option(city.name, city.id);
                dropdown.add(option);
            });

            // Set current selection if available
            const currentCity = document.getElementById('modalLeadCity').value;
            if (currentCity) {
                const selectedOption = [...dropdown.options].find(
                    opt => opt.text === currentCity
                );
                if (selectedOption) {
                    selectedOption.selected = true;
                }
            }
        } else {
            dropdown.innerHTML += '<option value="" disabled>No cities available</option>';
        }
    } catch (error) {
        console.error('Error loading cities:', error);
        showNotification(error.message || 'Failed to load cities', 'error');
    }
}


        // Placeholder for existing location functions to prevent "not defined" errors if you copy-paste the whole block.
        // In actual implementation, keep your existing async loadStates, loadDistricts, loadCities logic here.
        // For brevity, I assume the logic in your prompt is correct and should be pasted here.

        function saveLeadChanges() {
            const bankDropdown = document.getElementById('modalLeadBankDropdown');
            let finalBankName = bankDropdown.value === 'Other' ? document.getElementById('modalLeadBankCustom').value.trim() : bankDropdown.value;
            
            if (bankDropdown.value === 'Other' && finalBankName === '') {
                showNotification('Please enter a Bank Name', 'error'); return;
            }

            const data = {
                name: document.getElementById('modalName').value,
                phone: document.getElementById('modalLeadPhone').value,
                email: document.getElementById('modalLeadEmail').value,
                dob: document.getElementById('modalLeadDob').value,
                state: document.getElementById('modalLeadStateDropdown').options[document.getElementById('modalLeadStateDropdown').selectedIndex]?.text || '',
                district: document.getElementById('modalLeadDistrictDropdown').options[document.getElementById('modalLeadDistrictDropdown').selectedIndex]?.text || '',
                city: document.getElementById('modalLeadCityDropdown').options[document.getElementById('modalLeadCityDropdown').selectedIndex]?.text || '',
                company_name: document.getElementById('modalLeadCompany').value,
                lead_amount: document.getElementById('modalLeadAmount').value,
                expected_month: document.getElementById('modalLeadExpectedMonth').value || null,
                lead_type: document.getElementById('modalLeadType').value,
                loan_account_number: document.getElementById('modalLeadAccountNumber').value,
                turnover_amount: document.getElementById('modalLeadTurnoverAmount').value,
                salary: document.getElementById('modalLeadSalary').value,
                bank_name: finalBankName,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            showLoading(true);
            fetch(`/admin/leads/${window.currentLeadId}/update`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': data._token },
                body: JSON.stringify(data)
            })
            .then(res => res.ok ? res.json() : res.json().then(e => { throw new Error(e.error || e.message) }))
            .then(d => {
                showNotification(d.message || 'Updated.', 'success');
                // Update UI text
                if(document.getElementById('modalLeadStateDropdown').selectedIndex !== -1) 
                    document.getElementById('modalLeadState').value = document.getElementById('modalLeadStateDropdown').options[document.getElementById('modalLeadStateDropdown').selectedIndex].text;
                
                // ... (Update other UI fields similarly) ...
                document.getElementById('modalLeadBank').value = finalBankName;
                const emSel = document.getElementById('modalLeadExpectedMonth');
                const emVal = emSel ? emSel.value : '';
                setExpectedMonthUI(emVal || null);
                disableLeadEdit();
                const lt = document.getElementById('modalLeadType').value;
                document.getElementById('modalLeadTypeDisplay').textContent = lt ? lt.replace(/_/g, ' ').toUpperCase() : 'N/A';
            })
            .catch(e => showNotification(e.message, 'error'))
            .finally(() => showLoading(false));
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            if (id === 'rejectModal') document.getElementById('rejectionReason').value = '';
            if (id === 'loginModal') document.getElementById('loanAccountNumber').value = '';
        }
        function closeDocModal(id) { closeModal(id); }

        function showLoginModal(id) {
            window.currentLeadId = id;
            const acc = document.getElementById('modalLeadAccountNumber').value;
            document.getElementById('loginModalMessage').textContent = acc ? "Update existing account number:" : "Please provide account number:";
            document.getElementById('loanAccountNumber').value = acc || '';
            document.getElementById('loginModal').classList.add('active');
        }
        function confirmLogin() {
            const acc = document.getElementById('loanAccountNumber').value || document.getElementById('modalLeadAccountNumber').value;
            if(!acc) { showNotification('Loan Account Number required', 'error'); return; }
            updateLeadStatus(window.currentLeadId, 'login', null, null, acc);
        }

        // ... Reuse showAuthorizeModal, confirmAuthorize, showApproveModal, confirmApprove etc. from original code ...
        // ... Reuse updateLeadStatus logic ...
        
        function updateLeadStatus(leadId, status, reason = null, expectedMonth = null, loanAccountNumber = null) {
            const data = { status, reason, loan_account_number: loanAccountNumber, _token: document.querySelector('meta[name="csrf-token"]').content };
            showLoading(true);
            fetch(`/admin/leads/${leadId}/status`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': data._token },
                body: JSON.stringify(data)
            })
            .then(res => res.ok ? res.json() : res.json().then(e => { throw new Error(e.message) }))
            .then(d => {
                showNotification(d.message || 'Status updated.', 'success');
                // Close modals
                ['authorizeModal','approveModal','rejectModal','disburseModal','futureLeadModal','loginModal'].forEach(m => closeModal(m));
                viewLeadDetails(leadId);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(e => showNotification(e.message, 'error'))
            .finally(() => showLoading(false));
        }

        // Add missing handlers for Approve/Reject/Disburse/Future logic here using the pattern above.
        function showAuthorizeModal(id) { window.currentLeadId = id; document.getElementById('authorizeModal').classList.add('active'); }
        function confirmAuthorize() { updateLeadStatus(window.currentLeadId, 'authorized'); }
        
        function showApproveModal(id) { 
             window.currentLeadId = id; 
             const acc = document.getElementById('modalLeadAccountNumber').value;
             document.getElementById('approveModalMessage').textContent = acc ? "Update existing account:" : "Enter account:";
             document.getElementById('loanAccountNumber1').value = acc || '';
             document.getElementById('approveModal').classList.add('active'); 
        }
        function confirmApprove() {
            const acc = document.getElementById('loanAccountNumber1').value || document.getElementById('modalLeadAccountNumber').value;
            if(!acc) { showNotification('Account Number required', 'error'); return; }
            updateLeadStatus(window.currentLeadId, 'approved', null, null, acc);
        }

        function showRejectModal(id) { window.currentLeadId = id; document.getElementById('rejectModal').classList.add('active'); }
        function confirmReject() {
            const r = document.getElementById('rejectionReason').value;
            if(!r) { showNotification('Reason required', 'error'); return; }
            updateLeadStatus(window.currentLeadId, 'rejected', r);
        }

        function showDisburseModal(id) { 
            window.currentLeadId = id; 
            const acc = document.getElementById('modalLeadAccountNumber').value;
            document.getElementById('disburseModalMessage').textContent = acc ? "Verify account:" : "Enter account:";
            document.getElementById('loanAccountNumber2').value = acc || '';
            document.getElementById('disburseModal').classList.add('active'); 
        }
        function confirmDisburse() {
            const acc = document.getElementById('loanAccountNumber2').value || document.getElementById('modalLeadAccountNumber').value;
            if(!acc) { showNotification('Account Number required', 'error'); return; }
            updateLeadStatus(window.currentLeadId, 'disbursed', null, null, acc);
        }

        function showFutureLeadModal(id) { window.currentLeadId = id; document.getElementById('futureLeadModal').classList.add('active'); }
        function confirmFutureLead() { updateLeadStatus(window.currentLeadId, 'future_lead'); }
        
        function showDeleteModal(id) { window.currentLeadId = id; document.getElementById('deleteModal').classList.add('active'); }
        function confirmDelete() {
            showLoading(true);
            fetch(`/admin/leads/${window.currentLeadId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            })
            .then(res => res.ok ? res.json() : res.json().then(e => { throw new Error(e.message) }))
            .then(d => {
                showNotification(d.message || 'Deleted.', 'success');
                closeModal('deleteModal'); closeModal('leadDetailModal');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(e => showNotification(e.message, 'error'))
            .finally(() => showLoading(false));
        }

        // Helpers
        function showLoading(b) { document.getElementById('loadingOverlay').classList.toggle('active', b); }
        function showNotification(msg, type) {
            const n = document.createElement('div');
            n.className = `notification ${type}`;
            n.innerHTML = `<i class="fas fa-${type==='error'?'exclamation-circle':'check-circle'}"></i> ${msg}`;
            document.body.appendChild(n);
            setTimeout(() => { n.style.opacity='0'; setTimeout(()=>n.remove(),300); }, 3000);
        }
        function applyFilters() {
        const input = document.getElementById('executiveSearch');
        const filter = input ? input.value.toUpperCase() : '';
        const table = document.querySelector('.leads-table');
        const tr = table.getElementsByTagName('tr');
        let count = 0;

        // Get all filter values
        const fromDateStr = document.getElementById('fromDate') ? document.getElementById('fromDate').value : '';
        const toDateStr = document.getElementById('toDate') ? document.getElementById('toDate').value : '';
        
        // Create Date objects for comparison (set time to midnight for accurate day comparison)
        const fromDate = fromDateStr ? new Date(fromDateStr) : null;
        if(fromDate) fromDate.setHours(0,0,0,0);
        
        const toDate = toDateStr ? new Date(toDateStr) : null;
        if(toDate) toDate.setHours(23,59,59,999); // Include the whole end day

        // Get dropdown filter values
        const executiveFilter = document.getElementById('executive-filter') ? document.getElementById('executive-filter').value.toUpperCase().trim() : '';
        const clientNameFilter = document.getElementById('client-name-filter') ? document.getElementById('client-name-filter').value.toUpperCase().trim() : '';
        const loanAccountFilter = document.getElementById('loan-account-filter') ? document.getElementById('loan-account-filter').value.toUpperCase().trim() : '';
        const mobileFilter = document.getElementById('mobile-filter') ? document.getElementById('mobile-filter').value.toUpperCase().trim() : '';
        const companyFilter = document.getElementById('company-filter') ? document.getElementById('company-filter').value.toUpperCase().trim() : '';
        const loanAmountFilter = document.getElementById('loan-amount-filter') ? document.getElementById('loan-amount-filter').value : '';
        const statusFilter = document.getElementById('status-filter') ? document.getElementById('status-filter').value.toUpperCase().trim() : '';
        const leadTypeFilter = document.getElementById('lead-type-filter') ? document.getElementById('lead-type-filter').value.toUpperCase().trim() : '';
        const bankFilter = document.getElementById('bank-filter') ? document.getElementById('bank-filter').value.toUpperCase().trim() : '';

        for (let i = 0; i < tr.length; i++) { // Changed to 0 if your tbody doesn't have a header inside it (your HTML structure puts header in THEAD, so tbody tr starts at data)
            const row = tr[i];
            const tds = row.getElementsByTagName('td');
            if (tds.length === 0) continue;

            // --- 1. Search Filter (Global Search) ---
            let matchesSearch = !filter;
            if (filter) {
                matchesSearch = false;
                for (let j = 0; j < tds.length; j++) {
                    const cellText = tds[j].textContent || tds[j].innerText;
                    if (cellText.toUpperCase().indexOf(filter) > -1) {
                        matchesSearch = true;
                        break;
                    }
                }
            }

            // --- 2. Date Filter ---
            let matchesDate = true;
            // Retrieve the raw timestamp from the data-date attribute on the <tr class="lead-row">
            const dateAttr = row.getAttribute('data-date'); 
            
            if (dateAttr) {
                const rowDate = new Date(dateAttr);
                // Check if date is valid
                if (!isNaN(rowDate.getTime())) {
                    // Check From Date
                    if (fromDate && rowDate < fromDate) {
                        matchesDate = false;
                    }
                    // Check To Date
                    if (toDate && rowDate > toDate) {
                        matchesDate = false;
                    }
                }
            }

            // --- 3. Dropdown Filters ---
            let matchesFilters = true;

            // Helper function to check column content safely
            const checkColumn = (index, filterValue) => {
                if (!filterValue) return true; // No filter selected
                if (!tds[index]) return false; // Cell doesn't exist
                const text = (tds[index].textContent || tds[index].innerText).toUpperCase().trim();
                return text === filterValue || text.includes(filterValue);
            };

            // Executive (Column 0)
            if (!checkColumn(0, executiveFilter)) matchesFilters = false;

            // Client Name (Column 1)
            if (!checkColumn(1, clientNameFilter)) matchesFilters = false;

            // Mobile (Column 2 - Phone) Note: Your table order is Executive, Name, Phone, Loan AC...
            // Let's verify index based on your HTML structure:
            // 0: Executive, 1: Name, 2: Phone, 3: Loan AC, 4: Company, 5: Amount, 6: Status, 7: Type, 8: Bank, 9: Date
            
            if (!checkColumn(2, mobileFilter)) matchesFilters = false;
            
            if (!checkColumn(3, loanAccountFilter)) matchesFilters = false;

            if (!checkColumn(4, companyFilter)) matchesFilters = false;

            // Loan Amount (Column 5) - Numeric Comparison
            if (loanAmountFilter && tds[5]) {
                const amountText = (tds[5].textContent || tds[5].innerText).trim();
                const amount = parseIndianAmount(amountText);
                let matchesAmount = false;
                
                if (loanAmountFilter === '1-1000' && amount >= 1 && amount <= 1000) matchesAmount = true;
                else if (loanAmountFilter === '1000-10000' && amount >= 1000 && amount <= 10000) matchesAmount = true;
                else if (loanAmountFilter === '10000-100000' && amount >= 10000 && amount <= 100000) matchesAmount = true;
                else if (loanAmountFilter === '100000-1000000' && amount >= 100000 && amount <= 1000000) matchesAmount = true;
                else if (loanAmountFilter === '1000000+' && amount >= 1000000) matchesAmount = true;
                
                if (!matchesAmount) matchesFilters = false;
            }

            if (!checkColumn(6, statusFilter)) matchesFilters = false;

            // Lead Type (Column 7) - Handle Mapping
            if (leadTypeFilter && tds[7]) {
                const rowType = (tds[7].textContent || tds[7].innerText).toUpperCase().trim();
                let searchType = leadTypeFilter;
                
                // Handle the mapping logic you have in PHP (personal_loan -> PL)
                if (leadTypeFilter === 'PERSONAL_LOAN') searchType = 'PL';
                else if (leadTypeFilter === 'BUSINESS_LOAN') searchType = 'BL';
                else if (leadTypeFilter === 'HOME_LOAN') searchType = 'HL';
                else if (leadTypeFilter === 'CREDITCARD_LOAN') searchType = 'CC'; // Example

                if (rowType !== searchType && !rowType.includes(searchType)) matchesFilters = false;
            }

            if (!checkColumn(8, bankFilter)) matchesFilters = false;

            // --- Final Decision ---
            const shouldDisplay = matchesSearch && matchesDate && matchesFilters;
            row.style.display = shouldDisplay ? '' : 'none';
            if (shouldDisplay) count++;
        }

        // Update UI Counters
        const countSpan = document.getElementById('totalLeadsCount');
        if(countSpan) countSpan.textContent = count;
        
        calculateTotalAmount();
    }
        function applyDateFilter() {
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            
            if (fromDate && toDate) {
                applyFilters();
            } else if (fromDate || toDate) {
                showNotification('Please select both start and end dates', 'error');
            } else {
                showNotification('Please select date range to filter', 'error');
            }
        }
        function toggleDateFilter() {
            const el = document.getElementById('dateFilterContainer');
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
        function clearDateFilter() {
            document.getElementById('fromDate').value = '';
            document.getElementById('toDate').value = '';
            applyFilters();
        }
        function handleBankChange(select) {
            const custom = document.getElementById('modalLeadBankCustom');
            if(select.value === 'Other') { custom.style.display='block'; custom.required=true; custom.value=''; custom.focus(); }
            else { custom.style.display='none'; custom.required=false; custom.value=''; }
        }
        function parseIndianAmount(txt) {
            txt = txt.trim().toUpperCase();
            let val = parseFloat(txt.replace(/[^\d.]/g, ''));
            if(isNaN(val)) return 0;
            if(txt.includes('K')) val *= 1000;
            else if(txt.includes('L')) val *= 100000;
            else if(txt.includes('CR')) val *= 10000000;
            return val;
        }
        function formatIndianCurrency(num) {
            if(isNaN(num)) return '₹0';
            num = parseFloat(num);
            if(num >= 10000000) return '₹' + (num/10000000).toFixed(2) + ' Cr';
            if(num >= 100000) return '₹' + (num/100000).toFixed(2) + ' L';
            if(num >= 1000) return '₹' + (num/1000).toFixed(2) + ' K';
            return '₹' + num.toLocaleString('en-IN');
        }
        function calculateTotalAmount() {
            const rows = document.querySelectorAll('.leads-table tbody tr');
            let total = 0;
            rows.forEach(r => {
                // Only count visible (not filtered out) rows
                if (r.style.display === 'none') return;
                const txt = r.cells[5]?.textContent || '';
                total += parseIndianAmount(txt);
            });
            document.getElementById('totalAmountDisplay').textContent = formatIndianCurrency(total);
        }
      function openAddDocumentModal() {
            const modal = document.getElementById('addDocumentModal');
            if (modal) {
                modal.classList.add('active');
                document.getElementById('documentName').value = '';
                document.getElementById('documentType').value = '';
                document.getElementById('documentDescription').value = '';
                document.getElementById('documentFile').value = '';
            }
        }

        function deleteDocument(leadId, documentId) {
            if (!confirm('Are you sure you want to delete this document?')) return;

            showLoading(true);

            fetch(`/admin/leads/${leadId}/documents/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
                }
                return res.json();
            })
            .then(data => {
                showNotification('Document deleted successfully.', 'success');
                viewLeadDetails(leadId);
            })
            .catch(error => {
                console.error("Error deleting document:", error);
                showNotification(`Failed to delete document: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                if (modalId === 'rejectModal') {
                    document.getElementById('rejectionReason').value = '';
                } else if (modalId === 'loginModal') {
                    document.getElementById('loanAccountNumber').value = '';
                }

            }
                 setTimeout(() => location.reload(), 500);
        }


     function closeDocModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                if (modalId === 'rejectModal') {
                    document.getElementById('rejectionReason').value = '';
                } else if (modalId === 'loginModal') {
                    document.getElementById('loanAccountNumber').value = '';
                }

            }
                //  setTimeout(() => location.reload(), 500);
        }



        function showAuthorizeModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('authorizeModal').classList.add('active');
        }

        function confirmAuthorize() {
            updateLeadStatus(window.currentLeadId, 'authorized');
        }

       function showLoginModal(leadId) {
    window.currentLeadId = leadId;
    const loanAccountNumber = document.getElementById('modalLeadAccountNumber').value;
    const message = loanAccountNumber ?
        "This lead already has a loan account number. You can update it below if needed:" :
        "Please provide the loan account number:";

    document.getElementById('loginModalMessage').textContent = message;
    document.getElementById('loanAccountNumber').value = loanAccountNumber || '';
    document.getElementById('loanAccountNumber').required = !loanAccountNumber;
    document.getElementById('loginModal').classList.add('active');
}

    </script>
</body>
</html>