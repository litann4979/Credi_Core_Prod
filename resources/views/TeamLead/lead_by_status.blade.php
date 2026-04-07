
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Lead Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.jpg') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
   <style>
        /* Existing styles remain unchanged */

         :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --primary-900: #1e3a8a;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --success-50: #ecfdf5;
            --success-100: #d1fae5;
            --success-500: #10b981;
            --success-600: #059669;
            --success-700: #047857;
            --warning-50: #fffbeb;
            --warning-100: #fef3c7;
            --warning-500: #f59e0b;
            --warning-600: #d97706;
            --error-50: #fef2f2;
            --error-100: #fee2e2;
            --error-500: #ef4444;
            --error-600: #dc2626;
            --purple-50: #faf5ff;
            --purple-100: #f3e8ff;
            --purple-500: #8b5cf6;
            --purple-600: #7c3aed;
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }

        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            min-height: calc(100vh - 80px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed + .main-content {
            margin-left: 80px;
        }

        .dashboard-container {
            padding: 24px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .leads-table-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .leads-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .leads-table-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .leads-table-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-back {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .leads-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            text-transform: uppercase;
            font-variant: small-caps;
        }

        .leads-table th {
            padding: 1rem 1.5rem;
            text-align: center;
            vertical-align: middle;
            font-size: 0.875rem;
            font-weight: 600;
            color: black;
            background: linear-gradient(135deg, #f8f9fa 0%, #c4d6e9 100%);
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .leads-table td {
            padding: 16px;
            font-size: 14px;
            color: #1f2937;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            vertical-align: middle;
        }

        .lead-row:hover {
            background: #f9fafb;
            cursor: pointer;
        }

        .lead-status {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-personal_lead {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .status-authorized {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #16a34a;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #dc2626;
        }

        .status-disbursed {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #059669;
        }

        .status-future_lead {
            background: #fef9c3;
            color: #713f12;
            border: 1px solid #ca4d04;
        }

        .status-login {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #6366f1;
        }

        .empty-state {
            text-align: center;
            padding: 32px;
            text-transform: none;
        }

        .empty-state i {
            font-size: 32px;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .empty-state p {
            font-size: 16px;
            color: #6b7280;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
        }

        .modal-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #f3f4f6;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #e5e7eb;
            color: #4b5563;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .lead-detail-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 24px;
        }

        .lead-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .lead-basic-info h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .lead-basic-info p {
            color: #6b7280;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .lead-contact {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .contact-item i {
            width: 16px;
            color: #3b82f6;
        }

        .detail-section {
            margin-bottom: 24px;
        }

        .detail-section h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-item label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
        }

        .detail-item span {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }

        .remarks-box {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #e5e7eb;
        }

        .remarks-box p {
            color: #4b5563;
            line-height: 1.6;
            margin: 0;
        }

        .confirm-message {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 16px;
        }

        .confirm-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .btn-primary, .btn-secondary, .btn-danger {
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-primary:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }



        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-danger:disabled {
            background: #f87171;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .dashboard-container {
                padding: 16px;
            }

            .leads-table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .leads-table th,
            .leads-table td {
                padding: 12px;
            }

            .lead-detail-grid {
                grid-template-columns: 1fr;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-item label {
            font-weight: 500;
            white-space: nowrap;
            margin-bottom: 0;
        }

        .filter-item select {
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .leads-table .filter-item,
        .leads-table .empty-state {
            text-transform: none;
        }

        .editable-field {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            width: 100%;
            margin-bottom: 5px;
        }

        .uneditable-field {
            padding: 8px;
            display: inline-block;
        }

        select.editable-field {
            padding: 8px;
            height: auto;
        }

        .lead-status.uneditable-field {
            padding: 6px 12px;
        }

        .document-list {
            margin-top: 16px;
        }

        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #f9fafb;
        }

        .document-info {
            flex: 1;
        }

        .document-info p {
            margin: 0;
            font-size: 14px;
            color: #1f2937;
        }

        .document-info small {
            color: #6b7280;
            font-size: 12px;
        }

        .document-actions {
            display: flex;
            gap: 8px;
        }

        .btn-document {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-document-view {
            background: #3b82f6;
            color: white;
        }

        .btn-document-view:hover {
            background: #2563eb;
        }

        .btn-document-delete {
            background: #ef4444;
            color: white;
        }

        .btn-document-delete:hover {
            background: #dc2626;
        }

        #executiveSearch {
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border-radius: 12px;
            border: 2px solid var(--gray-200);
            transition: all 0.3s ease;
            width: 300px;
            background-color: white;
        }

        #executiveSearch:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        #totalLeadsCount {
            font-size: 1.1rem;
            margin-left: 0.25rem;
        }

        /* Ensure button stays on the right */
        .title-button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 24px;
        }

        .total-leads-counter {
    background: var(--primary-100);
    color: var(--primary-800);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: auto; /* Pushes it to the right */
}

.table-container {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    max-width: 100%;
    white-space: nowrap; /* prevents column wrapping */
}

/* Force table to take minimum width so scroll works */
.leads-table {
    min-width: 1200px; /* adjust as needed */
    border-collapse: collapse;
}

/* Optional: reduce padding for tighter columns */
.leads-table th,
    .leads-table td {
                padding: 0.65rem 1rem;
                font-size: 0.95rem;
            }

  .btn-secondary1 {
            background: linear-gradient(135deg, var(--gray-500) 0%, var(--gray-600) 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.3);
        }

        .btn-secondary1:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--gray-600) 0%, var(--gray-700) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(107, 114, 128, 0.4);
        }
          .followup-section {
            margin-top: 24px;
            padding: 16px;
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
        }

        .followup-section h4 {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 16px;
            position: relative;
            padding-left: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .followup-section h4::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, var(--purple-500) 0%, var(--purple-600) 100%);
            border-radius: 2px;
        }

        .followup-section h4 i {
            color: var(--purple-500);
            font-size: 16px;
        }

        .followup-item {
            margin-bottom: 16px;
            padding: 16px;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-xs);
            transition: all 0.2s ease;
        }

        .followup-item:hover {
            box-shadow: var(--shadow-sm);
            transform: translateY(-1px);
        }

        .followup-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        .followup-message {
            color: var(--gray-700);
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 12px 0;
            padding: 0;
        }

        .followup-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 8px;
        }

        .followup-user {
            font-weight: 600;
            color: var(--gray-700);
        }

        .followup-date {
            color: var(--gray-500);
        }

        .followup-audio {
            width: 100%;
            margin-top: 12px;
            border-radius: var(--radius-md);
            background: var(--gray-100);
        }

        .followup-audio::-webkit-media-controls-panel {
            background: var(--gray-100);
        }

        .no-followups {
            text-align: center;
            padding: 24px;
            color: var(--gray-500);
            font-style: italic;
        }

        .no-followups i {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--gray-400);
            display: block;
        }

        /* Animation for new follow-up items */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .followup-item {
            animation: fadeInUp 0.3s ease-out;
        }
    </style>
</head>
<body>
    @include('TeamLead.Components.sidebar')
    <div class="main-content">
        @include('TeamLead.Components.header', ['title' => $title, 'subtitle' => 'View leads filtered by status'])

        @php
            function formatLeadType($type) {
                $mapping = [
                    'personal_loan' => 'PL',
                    'business_loan' => 'BL',
                    'home_loan' => 'HL',
                    'loan_against_property' => 'LAP',
                    'creditcard_loan' => 'CC',
                ];
                return $mapping[strtolower($type ?? '')] ?? $type ?? 'N/A';
            }
        @endphp

    <div class="dashboard-container">
            <div class="leads-table-section">
                <!-- Title + Back button in same row -->
                <div class="title-button-container">
                    <h3 class="leads-table-title">
                        <i class="fas fa-table"></i>
                        {{ $title }}
                    </h3>
                    <a href="{{ route('team_lead.dashboard') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>

                <!-- Search + Total Count (aligned right) -->
                {{-- <div class="flex justify-end items-center mb-6 gap-4">
                    <div class="relative">
                        <input
                            type="text"
                            id="executiveSearch"
                            placeholder="Search by executive..."
                            class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onkeyup="searchExecutives()"
                        >
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                    <div class="total-leads-counter">
        Total Leads: <span id="totalLeadsCount">{{ count($leads) }}</span>
    </div>
                </div> --}}
            </div>
        </div>
   <div class="flex justify-between items-center mb-6">
    <div class="flex items-center">
        <div class="relative mr-4">
                   <input
    type="text"
    id="executiveSearch"
    placeholder="Search by executive or client name..."
    class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
    onkeyup="searchExecutives()"
>
            <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
        </div>
        <a href="{{ route('team_lead.dashboard.leads.byStatus', ['status' => $status, 'lead_type' => $leadType, 'from_date' => $fromDate, 'to_date' => $toDate, 'month' => $month]) }}"
           class="btn-secondary1 ml-2">
            <i class="fas fa-times"></i> Clear Filters
        </a>
    </div>
    <div class="flex items-center gap-2">
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">
            Total Leads: <span id="totalLeadsCount">{{ count($leads) }}</span>
        </div>
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">
            Total Amount: <span id="totalAmountDisplay">₹0</span>
        </div>
    </div>
</div>



                               <!-- Filter Form -->
                               <div class="table-container">
<form method="GET" action="{{ route('team_lead.dashboard.leads.byStatus',['status' => $status]) }}" id="filterForm">
    <input type="hidden" name="status" value="{{ $status }}">
    <input type="hidden" name="lead_type" value="{{ $leadType }}">
    <input type="hidden" name="from_date" value="{{ $fromDate }}">
    <input type="hidden" name="to_date" value="{{ $toDate }}">
    <input type="hidden" name="month" value="{{ $month }}">

                    <table class="leads-table w-full">
                        <thead>
                             <tr class="header-row">
                                <th scope="col">Executive</th>
                                <th scope="col">Name</th>
                                <th scope="col">Loan AC No</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Company</th>
                                <th scope="col">Loan Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Lead Type</th>
                                <th scope="col">Bank</th>
                                <th scope="col">Created At</th>
                            </tr>
                            <tr class="filters-row">
                            <th>
    <div class="filter-item">
        <select id="executive-filter" name="executive" onchange="document.getElementById('filterForm').submit()">
            <option value="">ALL EXECUTIVES</option>
            @foreach($executives as $exec)
                <option value="{{ $exec['id'] }}" {{ $executiveFilter == $exec['id'] ? 'selected' : '' }}>
                    {{ strtoupper($exec['name']) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th style="width: 180px; min-width: 180px;">
    <div class="filter-item">
        <select id="name-filter" name="name" onchange="document.getElementById('filterForm').submit()" style="width: 100%;">
            <option value="">ALL NAMES</option>
            @foreach($names as $name)
                <option value="{{ $name }}" 
                        title="{{ strtoupper($name) }}"
                        {{ $nameFilter == $name ? 'selected' : '' }}>
                    {{ strtoupper($name) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th>
    <div class="filter-item">
        <select id="loan-account-filter" name="loan_account" onchange="document.getElementById('filterForm').submit()">
            <option value="">ALL ACCOUNTS</option>
            @foreach($loanAccounts as $account)
                <option value="{{ $account }}" {{ $loanAccountFilter == $account ? 'selected' : '' }}>
                    {{ strtoupper($account) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th>
    <div class="filter-item">
        <select id="phone-filter" name="phone" onchange="document.getElementById('filterForm').submit()">
            <option value="">ALL PHONES</option>
            @foreach($phones as $phone)
                <option value="{{ $phone }}" {{ $phoneFilter == $phone ? 'selected' : '' }}>
                    {{ strtoupper($phone) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th style="width: 180px; min-width: 180px;">
    <div class="filter-item">
        <select id="company-filter" name="company" onchange="document.getElementById('filterForm').submit()" style="width: 100%;">
            <option value="">ALL COMPANIES</option>
            @foreach($companies as $company)
                <option value="{{ $company }}" 
                        title="{{ strtoupper($company) }}" 
                        {{ $companyFilter == $company ? 'selected' : '' }}>
                    {{-- Limit text to 20 chars so dropdown doesn't get too wide --}}
                    {{ Str::limit(strtoupper($company), 20) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th>
    <div class="filter-item">
        <select id="loan-amount-filter" name="loan_amount_range" onchange="document.getElementById('filterForm').submit()">
            <option value="">ALL AMOUNTS</option>
            <option value="1-1000" {{ request('loan_amount_range') == '1-1000' ? 'selected' : '' }}>1 – 1,000</option>
            <option value="10000-100000" {{ request('loan_amount_range') == '10000-100000' ? 'selected' : '' }}>10,000 – 1,00,000</option>
            <option value="100000-1000000" {{ request('loan_amount_range') == '100000-1000000' ? 'selected' : '' }}>1,00,000 – 10,00,000</option>
            <option value="1000000+" {{ request('loan_amount_range') == '1000000+' ? 'selected' : '' }}>ABOVE 10,00,000</option>
        </select>
    </div>
</th>
<th>
    <div class="filter-item">
        <select id="status-filter" name="status_filter" onchange="document.getElementById('filterForm').submit()">
            <option value="">ALL STATUSES</option>
            @foreach($statuses as $statusValue)
                <option value="{{ $statusValue }}" {{ $statusFilter == $statusValue ? 'selected' : '' }}>
                    {{ strtoupper(str_replace('_', ' ', $statusValue)) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th>
    <div class="filter-item">
        <select id="lead-type-filter" name="lead_type_filter" onchange="document.getElementById('filterForm').submit()">
            <option value="">ALL TYPES</option>
            @foreach($formattedLeadTypes as $type)
                <option value="{{ $type['value'] }}" {{ $leadTypeFilter == $type['value'] ? 'selected' : '' }}>
                    {{ strtoupper($type['display']) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
<th style="width: 180px; min-width: 180px;">
    <div class="filter-item">
        <select id="bank-filter" name="bank" onchange="document.getElementById('filterForm').submit()" style="width: 100%;">
            <option value="">ALL BANKS</option>
            @foreach($banks as $bank)
                <option value="{{ $bank }}" 
                        title="{{ strtoupper($bank) }}"
                        {{ $bankFilter == $bank ? 'selected' : '' }}>
                    {{ Str::limit(strtoupper($bank), 20) }}
                </option>
            @endforeach
        </select>
    </div>
</th>
                                <th>
                                    <div class="filter-item">

                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody">
                            @forelse ($leads as $lead)
                                <tr class="lead-row" data-lead-id="{{ $lead->id }}"
                                    data-name="{{ strtolower($lead->name) }}"
                                    data-email="{{ strtolower($lead->email ?? '') }}"
                                    data-status="{{ $lead->status }}"
                                    data-state="{{ $lead->state ?? '' }}"
                                    data-district="{{ $lead->district ?? '' }}"
                                    data-city="{{ $lead->city ?? '' }}"
                                    data-lead-type="{{ $lead->lead_type ?? '' }}"
                                    data-amount="{{ $lead->lead_amount }}"
                                    data-date="{{ $lead->created_at }}"
                                    onclick="viewLeadDetails({{ $lead->id }})">
                                   <td class="uppercase" title="{{ $lead->employee ? strtoupper($lead->employee->name) : 'N/A' }}">
    <div style="
        width: 180px;
        white-space: normal;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.2em;
        height: 2.4em;
    ">
        {{ $lead->employee ? strtoupper($lead->employee->name) : 'N/A' }}
    </div>
</td>
                                    <td class="uppercase"><strong>{{ strtoupper($lead->name) }}</strong></td>
                                    <td class="uppercase">{{ $lead->loan_account_number ? strtoupper($lead->loan_account_number) : 'N/A' }}</td>
                                    <td class="uppercase">{{ $lead->phone ? strtoupper($lead->phone) : 'N/A' }}</td>
                                  <td class="uppercase" title="{{ $lead->company_name ? strtoupper($lead->company_name) : 'N/A' }}">
    <div style="
        width: 180px;               /* Matches header width */
        white-space: normal;        /* Allows text to wrap */
        display: -webkit-box;       /* Required for clamping */
        -webkit-line-clamp: 2;      /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;           /* Hide overflow */
        line-height: 1.2em;
        height: 2.4em;              /* Fixed height for consistency */
    ">
        {{ $lead->company_name ? strtoupper($lead->company_name) : 'N/A' }}
    </div>
</td>
                                    <td class="uppercase"><strong>{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount) }}</strong></td>
                                    <td class="uppercase">
                                        <span class="lead-status status-{{ strtolower($lead->status) }}">
                                            {{ strtoupper($lead->status) }}
                                        </span>
                                    </td>
                                    <td class="uppercase">{{ formatLeadType($lead->lead_type) }}</td>
                                    <td class="uppercase" title="{{ $lead->bank_name ? strtoupper($lead->bank_name) : 'N/A' }}">
    <div style="
        width: 180px;
        white-space: normal;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.2em;
        height: 2.4em;
    ">
        {{ $lead->bank_name ? strtoupper($lead->bank_name) : 'N/A' }}
    </div>
</td>
                                    <td class="uppercase">{{ $lead->created_at ? $lead->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>NO LEADS FOUND FOR THIS STATUS.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </form>
                </div>



            <!-- Lead Detail Modal -->
            <div class="modal fade" id="leadDetailModal" tabindex="-1" aria-labelledby="leadDetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="leadDetailLabel">Lead Details</h2>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="lead-detail-grid">
                                <div class="lead-detail-left">
                                    <div class="lead-avatar-large" id="modalLeadInitials"></div>
                                    <div class="lead-basic-info">
                                        <h3><input type="text" id="modalLeadName" class="form-control editable-field"></h3>
                                        <div class="lead-contact">
                                            <div class="contact-item">
                                                <i class="fas fa-phone"></i>
                                                <input type="text" id="modalLeadPhone" class="form-control editable-field">
                                            </div>
                                            <div class="contact-item">
                                                <i class="fas fa-envelope"></i>
                                                <input type="email" id="modalLeadEmail" class="form-control editable-field">
                                            </div>
                                                 <div class="contact-item">
    <i class="fas fa-university"></i>
    
    <div style="flex: 1; display: flex; align-items: center; gap: 8px; overflow: hidden;">
        
        <input type="text" id="modalLeadBank" class="editable-field" disabled 
               style="background: transparent; border: none; width: 100%;">
        
        <select id="modalLeadBankDropdown" class="editable-field" 
                style="display: none; width: auto; max-width: 120px; background: transparent; border: 1px solid #ccc; border-radius: 4px; padding: 4px;" 
                onchange="handleBankChange(this)">
            <option value="">Select Bank</option>
            @foreach($banksName as $bank)
                <option value="{{ $bank }}">{{ strtoupper($bank) }}</option>
            @endforeach
            <option value="Other">Other</option>
        </select>
        
        <input type="text" 
               id="modalLeadBankCustom" 
               class="editable-field" 
               placeholder="BANK NAME" 
               style="display: none; flex: 1; min-width: 0; background: white; border: 1px solid #ccc; border-radius: 4px; padding: 4px 8px;">
    </div>
</div>

                                               <div class="followup-section">
        <h4>
            <i class="fas fa-history"></i>
            Follow-Up History
        </h4>
        <div id="followupList">
            <div class="no-followups">
                <i class="fas fa-clock"></i>
                <p>Loading follow-ups...</p>
            </div>
        </div>
    </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lead-detail-right">
                                    <div class="detail-section">
                                        <h4>Lead Information</h4>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label>DOB</label>
                                                <input type="date" id="modaldob" class="form-control editable-field">
                                            </div>
                                            <div class="detail-item">
                                                <label>State</label>
                                                <input type="text" id="modalLeadState" class="editable-field" disabled>
                                                <select id="modalLeadStateDropdown" class="editable-field" style="display: none;" onchange="loadDistricts(this.value)">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            <div class="detail-item">
                                                <label>District</label>
                                                <input type="text" id="modalLeadDistrict" class="editable-field" disabled>
                                                <select id="modalLeadDistrictDropdown" class="editable-field" style="display: none;" onchange="loadCities(this.value)">
                                                    <option value="">Select District</option>
                                                </select>
                                            </div>
                                            <div class="detail-item">
                                                <label>City</label>
                                                <input type="text" id="modalLeadCity" class="editable-field" disabled>
                                                <select id="modalLeadCityDropdown" class="editable-field" style="display: none;">
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                            <div class="detail-item">
                                                <label>Lead Amount</label>
                                                <input type="number" id="modalLeadAmount" class="form-control editable-field">
                                            </div>
                                            <div class="detail-item">
                                                <label>Amount in Words</label>
                                                <span id="modalLeadAmountInWords" class="uneditable-field"></span>
                                            </div>
                                            <div class="detail-item">
                                                <label>Salary</label>
                                                <input type="number" id="modalsalary" class="form-control editable-field">
                                            </div>
                                            <div class="detail-item">
                                                <label>Loan Account Number</label>
                                                <input type="text" id="modalLoanAccountNumber" class="form-control editable-field">
                                            </div>
                                            <div class="detail-item">
                                                <label>Status</label>
                                                <span id="modalLeadStatus" class="lead-status uneditable-field"></span>
                                            </div>
                                            <div class="detail-item">
                                                <label>Expected Month</label>
                                                <select id="modalLeadExpectedMonth" class="form-control editable-field">
                                                    <option value="">Select Month</option>
                                                    <option value="January">January</option>
                                                    <option value="February">February</option>
                                                    <option value="March">March</option>
                                                    <option value="April">April</option>
                                                    <option value="May">May</option>
                                                    <option value="June">June</option>
                                                    <option value="July">July</option>
                                                    <option value="August">August</August>
                                                    <option value="September">September</option>
                                                    <option value="October">October</option>
                                                    <option value="November">November</option>
                                                    <option value="December">December</option>
                                                </select>
                                            </div>
                                            <div class="detail-item">
                                                <label>Lead Type</label>
                                                <span id="modalLeadTypeDisplay" class="lead-type-display"></span>
                                                <select id="modalLeadType" class="editable-field" style="display: none;" disabled>
                                                    <option value="">Select Lead Type</option>
                                                    <option value="personal_loan">Personal Loan</option>
                                                    <option value="business_loan">Business Loan</option>
                                                    <option value="home_loan">Home Loan</option>
                                                </select>
                                            </div>
                                            <div class="detail-item">
                                                <label>Turnover Amount</label>
                                                <input type="number" id="modalLeadTurnoverAmount" class="form-control editable-field">
                                            </div>
                                            <div class="detail-item">
                                                <label>Company</label>
                                                <input type="text" id="modalcompany" class="form-control editable-field">
                                            </div>
                                            <div class="detail-item">
                                                <label>Voice Recording</label>
                                                <span id="modalLeadVoiceRecording" class="uneditable-field"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="detail-section">
                                        <h4>Employee Information</h4>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <label>Created By</label>
                                                <span id="modalLeadEmployeeName" class="uneditable-field"></span>
                                            </div>
                                            <div class="detail-item">
                                                <label>Team Lead</label>
                                                <span id="modalLeadTeamLeadName" class="uneditable-field"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="detail-section">
                                        <h4>Documents</h4>
                                        <button class="btn-primary" id="addDocumentButton" onclick="openAddDocumentModal()">
                                            <i class="fas fa-plus"></i> Add Document
                                        </button>
                                        <div id="documentList" class="document-list" style="margin-top: 16px;">
                                            <p>Loading documents...</p>
                                        </div>
                                    </div>
                                    <div class="detail-section" id="rejectionReasonSection" style="display: none;">
                                        <h4>Rejection Reason</h4>
                                        <div class="remarks-box">
                                            <p id="modalLeadReason" class="uneditable-field"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" id="leadActions">
                            <button class="btn-primary" id="editBtn" onclick="enableEditMode()">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-secondary d-none" id="cancelEditBtn" onclick="cancelEdit()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button class="btn-primary d-none" id="saveChangesBtn" onclick="saveChanges()">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            @if(auth()->user()->hasDesignation('team_lead'))
                                <button class="btn-primary" id="authorizeButton" onclick="showAuthorizeConfirm(currentLeadId)">
                                    <i class="fas fa-check-circle"></i> Authorize
                                </button>
                                <button class="btn-primary" id="markpersonalleadButton" onclick="showPersonalLeadConfirm(currentLeadId)">
                                    <i class="fas fa-check-circle"></i> Personal Lead
                                </button>
                                <button class="btn-primary" id="futureLeadButton" onclick="showFutureLeadConfirm(currentLeadId)">
                                    <i class="fas fa-clock"></i> Mark as Future Lead
                                </button>
                                <button class="btn-primary" id="forwardOperationsButton" onclick="forwardToOperations(currentLeadId)">
                                    <i class="fas fa-share"></i> Forward to Operations
                                </button>
                                <button class="btn-danger" id="rejectButton" onclick="showRejectConfirm(currentLeadId)">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                                                    <button class="btn-danger" id="deleteButton" onclick="showDeleteModal(currentLeadId)">
    <i class="fas fa-trash"></i> Delete Lead
</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authorize Confirmation Modal -->
            <div class="modal fade" id="authorizeConfirmModal" tabindex="-1" aria-labelledby="authorizeConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Authorize</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="confirm-message">
                                <div class="confirm-icon" style="background: #dbeafe; color: #1e40af;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <p>Are you sure you want to authorize this lead? This action will disable the "Mark as Future Lead" button.</p>
                                <div class="mb-3">
                                    <label for="authorizeRemarks" class="form-label">Remarks (optional)</label>
                                    <textarea id="authorizeRemarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                            <button type="button" class="btn-primary" onclick="confirmAuthorize()">
                                <i class="fas fa-check-circle"></i>
                                Authorize
                            </button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Personal Lead Confirmation Modal -->
            <div class="modal fade" id="personalleadConfirmModal" tabindex="-1" aria-labelledby="personalleadConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Personal Lead</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="confirm-message">
                                <div class="confirm-icon" style="background: #dbeafe; color: #1e40af;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <p>Are you sure you want to chnage status Personal Lead this lead?</p>
                                <div class="mb-3">
                                    <label for="personalleadRemarks" class="form-label">Remarks (optional)</label>
                                    <textarea id="personalleadRemarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                            <button type="button" class="btn-primary" onclick="confirmPersonalLead()">
                                <i class="fas fa-check-circle"></i>
                                Personal Lead
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Future Lead Confirmation Modal -->
            <div class="modal fade" id="futureLeadConfirmModal" tabindex="-1" aria-labelledby="futureLeadConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Mark as Future Lead</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="confirm-message">
                                <div class="confirm-icon" style="background: #fef9c3; color: #713f12;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <p>Are you sure you want to mark this as a future lead? This action will disable the Authorize, Forward to Operations, and Reject buttons.</p>
                                <div class="mb-3">
                                    <label for="futureLeadRemarks" class="form-label">Remarks (optional)</label>
                                    <textarea id="futureLeadRemarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                            <button type="button" class="btn-primary" onclick="confirmFutureLead()">
                                <i class="fas fa-clock"></i>
                                Mark as Future Lead
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Confirmation Modal -->
            <div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-labelledby="rejectConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Reject</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="confirm-message">
                                <div class="confirm-icon" style="background: #fee2e2; color: #dc2626;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <p>Are you sure you want to reject this lead? This action will disable all other buttons.</p>
                                <div class="mb-3">
                                    <label for="rejectRemarks" class="form-label">Rejection Reason (required)</label>
                                    <textarea id="rejectRemarks" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                            <button type="button" class="btn-danger" onclick="confirmReject()">
                                <i class="fas fa-times-circle"></i>
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forward to Operations Modal -->
            <div class="modal fade" id="forwardOperationsModal" tabindex="-1" aria-labelledby="forwardOperationsLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Forward Lead to Operations</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="forwardOperationsForm">
                                <input type="hidden" id="forward-lead-id">
                                <div class="mb-3">
                                    <label for="operation-remarks" class="form-label">Remarks</label>
                                    <textarea id="operation-remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn-primary" onclick="submitForwardToOperations()">Forward</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add/Update Document Modal -->
            <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDocumentModalLabel">Add New Document</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="lead_id" id="documentLeadId">
                                <input type="hidden" name="document_id" id="documentId">
                                <div class="form-group full-width mb-3">
                                    <label for="documentName">Document Name <span class="required" style="color: var(--error-500);">*</span></label>
                                    <input type="text" id="documentName" name="name" class="form-control" placeholder="e.g., ID Proof" required>
                                </div>
                                <div class="form-group full-width mb-3">
                                    <label for="documentType">Document Type</label>
                                    <select id="documentType" name="type" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="id_proof">ID Proof</option>
                                        <option value="address_proof">Address Proof</option>
                                        <option value="financial">Financial Document</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group full-width mb-3">
                                    <label for="documentDescription">Description</label>
                                    <textarea id="documentDescription" name="description" class="form-control" placeholder="Enter description"></textarea>
                                </div>
                                <div class="form-group full-width mb-3">
                                    <label for="documentFile">Upload File <span class="required" style="color: var(--error-500);">*</span></label>
                                    <input type="file" id="documentFile" name="document_file" class="form-control" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="addDocumentForm" class="btn-primary">
                                <i class="fas fa-upload"></i> Save & Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Document Confirmation Modal -->
            <div class="modal fade" id="deleteDocumentConfirmModal" tabindex="-1" aria-labelledby="deleteDocumentConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteDocumentConfirmLabel">Confirm Delete Document</h5>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="confirm-message">
                                <div class="confirm-icon" style="background: #fee2e2; color: #dc2626;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <p>Are you sure you want to delete this document? This action cannot be undone.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn-danger" onclick="confirmDeleteDocument()">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
                   <!-- Add this modal overlay for delete confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Delete Lead</h2>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <div class="confirm-icon reject">
                    <i class="fas fa-trash"></i>
                </div>
                <h4>Delete This Lead?</h4>
                <p>This action cannot be undone.</p>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-check"></i> Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>



    <script>
        // Number to words conversion function
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

        // Initial leads data from server
        let leadsData = @json($leadsData);
        let currentLeadId = null;
        let currentDocumentId = null;

        function filterByExecutive(executiveId) {
            const url = new URL(window.location.href);
            if (executiveId) {
                url.searchParams.set('executive', executiveId);
            } else {
                url.searchParams.delete('executive');
            }
            window.location.href = url.toString();
        }

        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
         calculateTotalAmount();

            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function() {
                    // This will naturally refresh the page with new filtered data
                    // The total amount will be calculated again on page load
                });
            }
            const leadAmountInput = document.getElementById('modalLeadAmount');
              const leadModal = document.getElementById('leadDetailModal');
    if (leadModal) {
        leadModal.addEventListener('hidden.bs.modal', function () {
            location.reload();
        });
    }
            if (leadAmountInput) {
                leadAmountInput.addEventListener('input', function() {
                    if (!this.disabled) {
                        const amount = parseInt(this.value);
                        document.getElementById('modalLeadAmountInWords').textContent =
                            isNaN(amount) || amount <= 0 ? 'N/A' : numberToWords(amount);
                    }
                });
            }
        });

        async function loadStates() {
            try {
                console.log('Loading states...');
                const response = await fetch('/team-lead/states', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to load states');
                }

                const data = await response.json();
                const dropdown = document.getElementById('modalLeadStateDropdown');
                if (!dropdown) throw new Error('State dropdown element not found');

                dropdown.innerHTML = '<option value="">Select State</option>';
                if (data.data && data.data.length > 0) {
                    data.data.forEach(state => {
                        const option = new Option(state.state_title, state.state_id);
                        dropdown.add(option);
                    });

                    const currentState = document.getElementById('modalLeadState').value;
                    if (currentState) {
                        const selectedOption = [...dropdown.options].find(opt => opt.text === currentState);
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
                const response = await fetch(`/team-lead/districts/${stateId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to load districts');
                }

                const data = await response.json();
                const dropdown = document.getElementById('modalLeadDistrictDropdown');
                if (!dropdown) throw new Error('District dropdown element not found');

                dropdown.innerHTML = '<option value="">Select District</option>';
                if (data.data && data.data.length > 0) {
                    data.data.forEach(district => {
                        const option = new Option(district.district_title, district.districtid);
                        dropdown.add(option);
                    });

                    const currentDistrict = document.getElementById('modalLeadDistrict').value;
                    if (currentDistrict) {
                        const selectedOption = [...dropdown.options].find(opt => opt.text === currentDistrict);
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
                const response = await fetch(`/team-lead/cities/${districtId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to load cities');
                }

                const data = await response.json();
                const dropdown = document.getElementById('modalLeadCityDropdown');
                if (!dropdown) throw new Error('City dropdown element not found');

                dropdown.innerHTML = '<option value="">Select City</option>';
                if (data.data && data.data.length > 0) {
                    data.data.forEach(city => {
                        const option = new Option(city.name, city.id);
                        dropdown.add(option);
                    });

                    const currentCity = document.getElementById('modalLeadCity').value;
                    if (currentCity) {
                        const selectedOption = [...dropdown.options].find(opt => opt.text === currentCity);
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

        function viewLeadDetails(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) return;

            console.log('Lead Data:', lead);
            currentLeadId = id;

            const formattedAmount = new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 0
            }).format(lead.amount);

            const formattedTurnover = lead.turnover_amount ? new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 0
            }).format(lead.turnover_amount) : '-';

            const initials = lead.name.split(' ').map(n => n[0]).join('');
            document.getElementById('modalLeadInitials').textContent = initials;
            document.getElementById('modalLeadName').value = lead.name ?? '';
            document.getElementById('modaldob').value = lead.dob ?? '';
            document.getElementById('modalLeadCity').value = lead.city || '';
            document.getElementById('modalLeadDistrict').value = lead.district || '';
            document.getElementById('modalLeadState').value = lead.state || '';
            document.getElementById('modalsalary').value = lead.salary || '';
            document.getElementById('modalLoanAccountNumber').value = lead.loan_account_number || '';
            document.getElementById('modalcompany').value = lead.company || '';
            document.getElementById('modalLeadPhone').value = lead.phone || '';
            document.getElementById('modalLeadEmail').value = lead.email || '';
              document.getElementById('modalLeadBank').value = lead.bank_name ?? '';
            document.getElementById('modalLeadAmount').value = lead.amount;
            document.getElementById('modalLeadAmountInWords').textContent = lead.amount ? numberToWords(parseInt(lead.amount)) : 'N/A';
            document.getElementById('modalLeadStatus').textContent = lead.status.replace('_', ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            document.getElementById('modalLeadStatus').className = `lead-status status-${lead.status}`;
            document.getElementById('modalLeadExpectedMonth').value = lead.expected_month || '-';
            const leadTypeDisplay = lead.lead_type ? lead.lead_type.replace(/_/g, ' ').toUpperCase() : 'N/A';
            document.getElementById('modalLeadTypeDisplay').textContent = leadTypeDisplay;
            document.getElementById('modalLeadType').value = lead.lead_type || '';
            document.getElementById('modalLeadTurnoverAmount').value = formattedTurnover;
            document.getElementById('modalLeadEmployeeName').textContent = lead.employee_name || '-';
            document.getElementById('modalLeadTeamLeadName').textContent = lead.team_lead_name || '-';
            document.getElementById('modalLeadReason').textContent = lead.reason || '-';
            document.getElementById('rejectionReasonSection').style.display = lead.reason && lead.status === 'rejected' ? 'block' : 'none';

            const voiceElement = document.getElementById("modalLeadVoiceRecording");
                    if (lead.voice_recording) {
    voiceElement.innerHTML = `
        <audio controls style="width:100%;">
            <source src="${lead.voice_recording}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    `;
} else {
    voiceElement.textContent = 'N/A';
}

            const authorizeButton = document.getElementById('authorizeButton');
            const futureLeadButton = document.getElementById('futureLeadButton');
            const forwardOperationsButton = document.getElementById('forwardOperationsButton');
            const rejectButton = document.getElementById('rejectButton');

            if (lead.status === 'authorized') {
                authorizeButton.disabled = true;
                futureLeadButton.disabled = true;
                forwardOperationsButton.disabled = false;
                rejectButton.disabled = false;
            } else if (lead.status === 'future_lead') {
                authorizeButton.disabled = true;
                futureLeadButton.disabled = true;
                forwardOperationsButton.disabled = true;
                rejectButton.disabled = true;
            } else if (lead.status === 'rejected') {
                authorizeButton.disabled = true;
                futureLeadButton.disabled = true;
                forwardOperationsButton.disabled = true;
                rejectButton.disabled = true;
            } else if (lead.status === 'personal_lead') {
                authorizeButton.disabled = false;
                futureLeadButton.disabled = false;
                forwardOperationsButton.disabled = false;
                rejectButton.disabled = false;
            } else {
                authorizeButton.disabled = true;
                futureLeadButton.disabled = true;
                forwardOperationsButton.disabled = true;
                rejectButton.disabled = true;
            }

             // Disable all editable fields initially
    document.querySelectorAll('.editable-field').forEach(field => {
        field.setAttribute('disabled', true);

        // Special handling for display fields
        document.getElementById('modalLeadTypeDisplay').style.display = 'inline-block';
        document.getElementById('modalLeadType').style.display = 'none';
        document.getElementById('modalLeadState').style.display = 'block';
        document.getElementById('modalLeadStateDropdown').style.display = 'none';
        document.getElementById('modalLeadDistrict').style.display = 'block';
        document.getElementById('modalLeadDistrictDropdown').style.display = 'none';
        document.getElementById('modalLeadCity').style.display = 'block';
        document.getElementById('modalLeadCityDropdown').style.display = 'none';
    });

    // Disable Expected Month field for certain statuses
    const expectedMonthField = document.getElementById('modalLeadExpectedMonth');
    const restrictedStatuses = ['disbursed', 'rejected', 'future_lead'];

    if (restrictedStatuses.includes(lead.status.toLowerCase())) {
        expectedMonthField.setAttribute('disabled', 'disabled');
        expectedMonthField.style.backgroundColor = '#f3f4f6'; // Visual indication it's disabled
        expectedMonthField.style.cursor = 'not-allowed';
    }

    // Reset edit mode UI
    document.getElementById('editBtn').classList.remove('d-none');
    document.getElementById('saveChangesBtn').classList.add('d-none');
    document.getElementById('cancelEditBtn').classList.add('d-none');

    // Load documents for the lead
    loadDocumentsForLead(id);



    const modal = new bootstrap.Modal(document.getElementById('leadDetailModal'));
    modal.show();

      // Update follow-up section
              const followupList = document.getElementById('followupList');
    followupList.innerHTML = '';

    if (lead.followUps && lead.followUps.length > 0) {
        lead.followUps.forEach(fu => {
            const formattedDate = new Date(fu.timestamp).toLocaleString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            followupList.innerHTML += `
                <div class="followup-item">
                    <p class="followup-message">${fu.message || 'No message provided'}</p>
                    ${fu.recording_path ? `
                        <audio controls class="followup-audio">
                            <source src="${fu.recording_path}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    ` : ''}
                    <div class="followup-meta">
                        <span><strong>${fu.user?.name ?? 'Unknown User'}</strong></span>
                        <span class="followup-date">${formattedDate}</span>
                    </div>
                </div>
            `;
        });
    } else {
        followupList.innerHTML = `
            <div class="no-followups">
                <i class="fas fa-inbox"></i>
                <p>No follow-ups found for this lead.</p>
            </div>
        `;
    }



}

        // Function to load documents for a lead
      async function loadDocumentsForLead(leadId) {
    const documentList = document.getElementById('documentList');
    documentList.innerHTML = '<p>Loading documents...</p>';

    try {
        const response = await fetch(`/team-lead/lead/${leadId}/document`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({})
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to load documents');
        }

        const data = await response.json();
        const documents = data.documents || [];

        if (documents.length === 0) {
            documentList.innerHTML = '<p>No documents available.</p>';
            return;
        }

        documentList.innerHTML = '';
        documents.forEach(doc => {
            const docElement = document.createElement('div');
            docElement.className = 'document-item';
            docElement.innerHTML = `
                <div class="document-info">
                    <p><strong>${doc.document_name}</strong></p>
                    <small>Type: ${doc.type || 'N/A'}</small><br>
                    <small>Uploaded: ${new Date(doc.uploaded_at).toLocaleDateString()}</small>
                </div>
                <div class="document-actions">
                    ${doc.filepath ? `
                        <button class="btn-document btn-document-view" onclick="viewDocument(${leadId}, ${doc.document_id}, '${doc.filepath}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn-document btn-document-delete" onclick="showDeleteDocumentConfirm(${leadId}, ${doc.document_id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    ` : `
                        <button class="btn-document btn-primary" onclick="openUpdateDocumentModal(${leadId}, ${doc.document_id}, '${doc.document_name}', '${doc.type}', '${doc.description}')">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    `}
                </div>
            `;
            documentList.appendChild(docElement);
        });


    } catch (error) {
        console.error('Error loading documents:', error);
        documentList.innerHTML = '<p>Error loading documents.</p>';
        showNotification('Failed to load documents: ' + error.message, 'error');
    }
}


        // Function to view a document
        function viewDocument(leadId, documentId, filepath) {
            window.open(`/storage/${filepath}`, '_blank');
        }

        // Function to show delete document confirmation
       function showDeleteDocumentConfirm(leadId, documentId) {
    currentLeadId = leadId;
    currentDocumentId = documentId;
    console.log("Delete called for lead:", leadId, "document:", documentId); // debug log
    const modal = new bootstrap.Modal(document.getElementById('deleteDocumentConfirmModal'));
    modal.show();
}


        // Function to confirm document deletion
      async function confirmDeleteDocument() {
    try {
        const response = await fetch(`/team-lead/lead/${currentLeadId}/documents/${currentDocumentId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to delete document');
        }

        const data = await response.json();
        showNotification(data.message, 'success');
        bootstrap.Modal.getInstance(document.getElementById('deleteDocumentConfirmModal')).hide();
        loadDocumentsForLead(currentLeadId); // Reload documents to reflect the change
    } catch (error) {
        console.error('Error deleting document:', error);
        showNotification(error.message || 'Failed to delete document', 'error');
    }
}




// Function to open update document modal
        function openUpdateDocumentModal(leadId, documentId, name, type, description) {
            document.getElementById('documentLeadId').value = leadId;
            document.getElementById('documentId').value = documentId;
            document.getElementById('documentName').value = name || '';
            document.getElementById('documentType').value = type || '';
            document.getElementById('documentDescription').value = description || '';
            document.getElementById('documentFile').removeAttribute('required'); // Make file optional for updates
            document.getElementById('addDocumentModalLabel').textContent = 'Update Document';
            const modal = new bootstrap.Modal(document.getElementById('addDocumentModal'));
            modal.show();
        }

        // Function to open add document modal
        function openAddDocumentModal() {
            document.getElementById('addDocumentForm').reset();
            document.getElementById('documentLeadId').value = currentLeadId;
            document.getElementById('documentId').value = '';
            document.getElementById('documentFile').setAttribute('required', 'required');
            document.getElementById('addDocumentModalLabel').textContent = 'Add New Document';
            const modal = new bootstrap.Modal(document.getElementById('addDocumentModal'));
            modal.show();
        }

        // Function to close modal
        function closeModal(id) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(id));
            modal.hide();

        }

        // Handle document form submission
        document.getElementById('addDocumentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const leadId = document.getElementById('documentLeadId').value;
            const documentId = document.getElementById('documentId').value;
            const url = documentId ? `/team-lead/lead/${leadId}/documents/${documentId}/upload` : `/team-lead/lead/${leadId}/documents`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                return res.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showNotification(documentId ? 'Document updated successfully' : 'Document added successfully', 'success');
                    closeModal('addDocumentModal');
                    loadDocumentsForLead(leadId);
                    document.getElementById('addDocumentForm').reset();
                } else {
                    throw new Error(data.message || 'Failed to process document');
                }
            })
            .catch(error => {
                console.error("Error processing document:", error);
                showNotification(`Failed to process document: ${error.message}`, 'error');
            });
        });



        // Function to show notification
        function showNotification(message, type = 'info') {
            let container = document.getElementById('notificationContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notificationContainer';
                container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                `;
                document.body.appendChild(container);
            }

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                background: ${type === 'success' ? '#d1fae5' : type === 'error' ? '#fee2e2' : '#dbeafe'};
                color: ${type === 'success' ? '#065f46' : type === 'error' ? '#991b1b' : '#1e40af'};
                border-left: 4px solid ${type === 'success' ? '#059669' : type === 'error' ? '#dc2626' : '#3b82f6'};
                padding: 16px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 300px;
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
            `;

            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            notification.innerHTML = `
                <i class="fas fa-${icon}" style="font-size: 20px;"></i>
                <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 2px;">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <div style="font-size: 14px;">${message}</div>
                </div>
                <button style="background: none; border: none; cursor: pointer; color: inherit; font-size: 16px;">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(notification);

            notification.querySelector('button').addEventListener('click', () => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        container.removeChild(notification);
                    }
                }, 300);
            });

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            container.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        }

        function showAuthorizeConfirm(id) {
            currentLeadId = id;
            document.getElementById('authorizeRemarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('authorizeConfirmModal'));
            modal.show();
        }

        function confirmAuthorize() {
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;
            setAuthorized(currentLeadId);
        }

        function setAuthorized(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) return;

            const remarks = document.getElementById('authorizeRemarks').value;
            fetch(`/team-lead/leads/${id}/authorize`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    remarks: remarks || undefined
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    lead.status = 'authorized';
                    if (remarks) lead.reason = remarks;
                    bootstrap.Modal.getInstance(document.getElementById('authorizeConfirmModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error authorizing lead');
                console.error(error);
            });
        }

             function showPersonalLeadConfirm(id) {
            currentLeadId = id;
            document.getElementById('personalleadRemarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('personalleadConfirmModal'));
            modal.show();
        }

        function confirmPersonalLead() {
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;
            setPersonalLead(currentLeadId);
        }

        function setPersonalLead(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) return;

            const remarks = document.getElementById('personalleadRemarks').value;
            fetch(`/team-lead/leads/${id}/markpersonallead`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    remarks: remarks || undefined
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    lead.status = 'personal_lead';
                    if (remarks) lead.reason = remarks;
                    bootstrap.Modal.getInstance(document.getElementById('personalleadConfirmModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error authorizing lead');
                console.error(error);
            });
        }

        

        function showFutureLeadConfirm(id) {
            currentLeadId = id;
            document.getElementById('futureLeadRemarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('futureLeadConfirmModal'));
            modal.show();
        }

        function confirmFutureLead() {
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;
            setFutureLead(currentLeadId);
        }

        function setFutureLead(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) return;

            const remarks = document.getElementById('futureLeadRemarks').value;
            fetch(`/team-lead/leads/${id}/future`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    remarks: remarks || undefined
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    lead.status = 'future_lead';
                    if (remarks) lead.reason = remarks;
                    bootstrap.Modal.getInstance(document.getElementById('futureLeadConfirmModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                    location.reload();
                } else {
                    alert(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                alert(`Error updating lead: ${error.message}`);
                console.error(error);
            });
        }

        function showRejectConfirm(id) {
            currentLeadId = id;
            document.getElementById('rejectRemarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('rejectConfirmModal'));
            modal.show();
        }

        function confirmReject() {
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;

            const remarks = document.getElementById('rejectRemarks').value.trim();
            if (!remarks) {
                alert('Rejection reason is required.');
                return;
            }
            rejectLead(currentLeadId);
        }

        function rejectLead(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) {
                alert('Lead not found');
                return;
            }

            const remarks = document.getElementById('rejectRemarks').value.trim();
            fetch(`/team-lead/leads/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ remarks })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    lead.status = 'rejected';
                    lead.reason = remarks;
                    bootstrap.Modal.getInstance(document.getElementById('rejectConfirmModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to reject lead');
                }
            })
            .catch(error => {
                alert(`Error rejecting lead: ${error.message}`);
                console.error(error);
            });
        }

        let selectedLeadId = null;
        function forwardToOperations(leadId) {
            selectedLeadId = leadId;
            document.getElementById('operation-remarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('forwardOperationsModal'));
            modal.show();
        }

        function submitForwardToOperations() {
            const remarks = document.getElementById('operation-remarks').value;
            fetch(`/team-lead/leads/${selectedLeadId}/forward-to-operations-by-teamlead`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ remarks })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Lead forwarded to Operation team.');
                    bootstrap.Modal.getInstance(document.getElementById('forwardOperationsModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                    location.reload();
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(error => {
                alert(`Error forwarding lead: ${error.message}`);
                console.error(error);
            });
        }

        let originalLeadData = {};

     function enableEditMode() {
    const lead = leadsData.find(lead => lead.id === currentLeadId);
    if (!lead) return;

    // --- ADD THESE 3 LINES ---
    const bankDropdown = document.getElementById('modalLeadBankDropdown');
    const bankCustomInput = document.getElementById('modalLeadBankCustom');
    const currentBankName = document.getElementById('modalLeadBank').value;
    // -------------------------

    document.querySelectorAll('.editable-field').forEach(field => {
        originalLeadData[field.id] = field.value;
        field.removeAttribute('disabled');

        // Special handling for lead type display
        document.getElementById('modalLeadTypeDisplay').style.display = 'none';
        document.getElementById('modalLeadType').style.display = 'block';

        // Special handling for location fields
        document.getElementById('modalLeadState').style.display = 'none';
        document.getElementById('modalLeadStateDropdown').style.display = 'block';
        document.getElementById('modalLeadDistrict').style.display = 'none';
        document.getElementById('modalLeadDistrictDropdown').style.display = 'block';
        document.getElementById('modalLeadCity').style.display = 'none';
        document.getElementById('modalLeadCityDropdown').style.display = 'block';

        // Hide display, show dropdown
        document.getElementById('modalLeadBank').style.display = 'none';
        bankDropdown.style.display = 'block';
        bankDropdown.disabled = false;

        // Check if the current bank exists in the dropdown options
        let bankExists = false;
        for (let i = 0; i < bankDropdown.options.length; i++) {
            if (bankDropdown.options[i].value === currentBankName) {
                bankDropdown.selectedIndex = i;
                bankExists = true;
                break;
            }
        }

        // If bank exists in dropdown, select it and hide "Other" input
        if (bankExists) {
            bankCustomInput.style.display = 'none';
        } else if (currentBankName && currentBankName !== 'N/A') {
            // If bank doesn't exist in dropdown (it was custom previously), select "Other" and fill input
            bankDropdown.value = 'Other';
            bankCustomInput.style.display = 'block';
            bankCustomInput.value = currentBankName;
            bankCustomInput.disabled = false;
        } else {
            // No bank set
            bankDropdown.value = "";
            bankCustomInput.style.display = 'none';
        }

        if (document.getElementById('modalLeadStateDropdown').options.length <= 1) {
            loadStates();
        }
    });

    // Disable Expected Month field for certain statuses
    const expectedMonthField = document.getElementById('modalLeadExpectedMonth');
    const restrictedStatuses = ['disbursed', 'rejected', 'future_lead'];

    if (restrictedStatuses.includes(lead.status.toLowerCase())) {
        expectedMonthField.setAttribute('disabled', 'disabled');
        expectedMonthField.style.backgroundColor = '#f3f4f6'; // Visual indication it's disabled
        expectedMonthField.style.cursor = 'not-allowed';
    }

    document.getElementById('editBtn').classList.add('d-none');
    document.getElementById('saveChangesBtn').classList.remove('d-none');
    document.getElementById('cancelEditBtn').classList.remove('d-none');
}

        function cancelEdit() {
            document.querySelectorAll('.editable-field').forEach(field => {
                field.value = originalLeadData[field.id] ?? '';
                field.setAttribute('disabled', true);
                document.getElementById('modalLeadTypeDisplay').style.display = 'inline-block';
                document.getElementById('modalLeadType').style.display = 'none';
                document.getElementById('modalLeadState').style.display = 'block';
                document.getElementById('modalLeadStateDropdown').style.display = 'none';
                document.getElementById('modalLeadDistrict').style.display = 'block';
                document.getElementById('modalLeadDistrictDropdown').style.display = 'none';
                document.getElementById('modalLeadCity').style.display = 'block';
                document.getElementById('modalLeadCityDropdown').style.display = 'none';
            });

            document.getElementById('editBtn').classList.remove('d-none');
            document.getElementById('saveChangesBtn').classList.add('d-none');
            document.getElementById('cancelEditBtn').classList.add('d-none');

            
        }

       function saveChanges() {
    // 1. Determine Bank Name Logic
    const bankDropdown = document.getElementById('modalLeadBankDropdown');
    let finalBankName = '';

    if (bankDropdown.value === 'Other') {
        finalBankName = document.getElementById('modalLeadBankCustom').value.trim();
        if (finalBankName === '') {
            showNotification('Please enter a Bank Name', 'error');
            return;
        }
    } else {
        finalBankName = bankDropdown.value;
    }

    const leadId = currentLeadId;
    // ... (Your data object creation remains the same)
    const updatedData = {
        name: document.getElementById('modalLeadName').value,
        phone: document.getElementById('modalLeadPhone').value,
        email: document.getElementById('modalLeadEmail').value,
        dob: document.getElementById('modaldob').value,
        state: document.getElementById('modalLeadStateDropdown').options[document.getElementById('modalLeadStateDropdown').selectedIndex].text,
        district: document.getElementById('modalLeadDistrictDropdown').options[document.getElementById('modalLeadDistrictDropdown').selectedIndex].text,
        city: document.getElementById('modalLeadCityDropdown').options[document.getElementById('modalLeadCityDropdown').selectedIndex].text,
        company_name: document.getElementById('modalcompany').value,
        lead_amount: document.getElementById('modalLeadAmount').value,
        loan_account_number: document.getElementById('modalLoanAccountNumber').value,
        expected_month: document.getElementById('modalLeadExpectedMonth').value,
        lead_type: document.getElementById('modalLeadType').value,
        turnover_amount: document.getElementById('modalLeadTurnoverAmount').value,
        salary: document.getElementById('modalsalary').value,
        bank_name: finalBankName,
        company: document.getElementById('modalcompany').value
    };

    fetch(`/team-lead/lead/${currentLeadId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(updatedData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Lead updated successfully!', 'success');

            // --- UI RESET LOGIC STARTS HERE ---

            // 1. Update the Read-Only Text Inputs with new values
            document.getElementById('modalLeadBank').value = finalBankName;
            document.getElementById('modalLeadState').value = updatedData.state;
            document.getElementById('modalLeadDistrict').value = updatedData.district;
            document.getElementById('modalLeadCity').value = updatedData.city;
            
            // Update Lead Type Text
            const typeSelect = document.getElementById('modalLeadType');
            const typeText = typeSelect.options[typeSelect.selectedIndex].text;
            document.getElementById('modalLeadTypeDisplay').textContent = typeText;

            // 2. Hide Dropdowns / Custom Inputs
            document.getElementById('modalLeadBankDropdown').style.display = 'none';
            document.getElementById('modalLeadBankCustom').style.display = 'none';
            document.getElementById('modalLeadStateDropdown').style.display = 'none';
            document.getElementById('modalLeadDistrictDropdown').style.display = 'none';
            document.getElementById('modalLeadCityDropdown').style.display = 'none';
            document.getElementById('modalLeadType').style.display = 'none';

            // 3. Show Read-Only Text Inputs
            document.getElementById('modalLeadBank').style.display = 'block';
            document.getElementById('modalLeadState').style.display = 'block';
            document.getElementById('modalLeadDistrict').style.display = 'block';
            document.getElementById('modalLeadCity').style.display = 'block';
            document.getElementById('modalLeadTypeDisplay').style.display = 'inline-block';

            // 4. Disable all inputs again
            document.querySelectorAll('.editable-field').forEach(field => {
                field.setAttribute('disabled', true);
                // Update the "original data" reference so Cancel works correctly next time
                originalLeadData[field.id] = field.value;
            });

            // 5. Toggle Buttons
            document.getElementById('editBtn').classList.remove('d-none');
            document.getElementById('saveChangesBtn').classList.add('d-none');
            document.getElementById('cancelEditBtn').classList.add('d-none');

            // --- UI RESET LOGIC ENDS HERE ---

        } else {
            alert('Error updating lead: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error updating lead: ' + error.message);
        console.error(error);
    });
}

 function searchExecutives() {
    const input = document.getElementById('executiveSearch');
    const filter = input.value.toUpperCase();
    const table = document.querySelector('.leads-table');
    const tr = table.getElementsByTagName('tr');
    let count = 0;

    // Start from index 1 to skip header row
    for (let i = 1; i < tr.length; i++) {
        const executiveTd = tr[i].getElementsByTagName('td')[0]; // First column is executive name
        const clientTd = tr[i].getElementsByTagName('td')[1];    // Second column is client name

        let shouldDisplay = false;

        if (executiveTd) {
            const executiveText = executiveTd.textContent || executiveTd.innerText;
            if (executiveText.toUpperCase().indexOf(filter) > -1) {
                shouldDisplay = true;
            }
        }

        if (!shouldDisplay && clientTd) {
            const clientText = clientTd.textContent || clientTd.innerText;
            if (clientText.toUpperCase().indexOf(filter) > -1) {
                shouldDisplay = true;
            }
        }

        if (shouldDisplay) {
            tr[i].style.display = "";
            count++;
        } else {
            tr[i].style.display = "none";
        }
    }

    // Update the count display
    document.getElementById('totalLeadsCount').textContent = count;
    calculateTotalAmount();
}

function parseIndianAmount(amountText) {
    amountText = amountText.trim().toUpperCase();
    let value = parseFloat(amountText.replace(/[^\d.]/g, ''));

    if (isNaN(value)) return 0;

    if (amountText.includes('K')) {
        value *= 1000;
    } else if (amountText.includes('L')) {
        value *= 100000; // 1 Lakh = 100,000
    } else if (amountText.includes('CR')) {
        value *= 10000000; // 1 Crore = 10,000,000
    }

    return value;
}

function calculateTotalAmount() {
    const table = document.querySelector('.leads-table');
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    let totalAmount = 0;

    rows.forEach(row => {
        const amountCell = row.cells[5]; // Loan amount column
        if (amountCell) {
            const amountText = amountCell.textContent.trim();
            totalAmount += parseIndianAmount(amountText);
        }
    });

    document.getElementById('totalAmountDisplay').textContent = formatIndianCurrency(totalAmount);
}



        // Function to format currency in Indian format
      function formatIndianCurrency(amount) {
    if (isNaN(amount)) return '₹0';

    amount = parseFloat(amount);

    if (amount >= 1000 && amount < 100000) {
        return '₹' + (amount / 1000).toFixed(2) + ' K';
    } else if (amount >= 10000000) {
        return '₹' + (amount / 10000000).toFixed(2) + ' Cr';
    } else if (amount >= 100000) {
        return '₹' + (amount / 100000).toFixed(2) + ' L';
    } else {
        return '₹' + amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}


function showDeleteModal(leadId) {
    window.currentLeadId = leadId;

    let modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}


function confirmDelete() {
    const leadId = window.currentLeadId;
    showLoading(true);

    fetch(`/team-lead/leads/${leadId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => {
                throw new Error(err.message || `HTTP ${res.status}`);
            });
        }
        return res.json();
    })
    .then(data => {
        showNotification(data.message || 'Lead deleted successfully.', 'success');
        closeDocModal('deleteModal');
        closeModal('leadDetailModal');
        setTimeout(() => location.reload(), 1000);
    })
    .catch(error => {
        console.error('Error deleting lead:', error);
        showNotification(`Failed to delete lead: ${error.message}`, 'error');
    })
    .finally(() => showLoading(false));
}

   function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                if (show) {
                    overlay.classList.add('active');
                } else {
                    overlay.classList.remove('active');
                }
            }
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

        function handleBankChange(selectElement) {
    const customInput = document.getElementById('modalLeadBankCustom');
    
    if (selectElement.value === 'Other') {
        customInput.style.display = 'block'; // Shows next to the dropdown
        customInput.required = true;
        customInput.value = ''; 
        customInput.focus();
    } else {
        customInput.style.display = 'none'; // Hides, giving space back to dropdown
        customInput.required = false;
        customInput.value = ''; 
    }
}
    </script>
</body>
</html>
