<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lead Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('TeamLead.Components.css')
    <style>
        :root {
            /* Modern Color Palette */
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
            --success-500: #10b981;
            --success-600: #059669;

            --warning-50: #fffbeb;
            --warning-500: #f59e0b;
            --warning-600: #d97706;

            --error-50: #fef2f2;
            --error-500: #ef4444;
            --error-600: #dc2626;

            --purple-50: #faf5ff;
            --purple-500: #8b5cf6;
            --purple-600: #7c3aed;

            --orange-50: #fff7ed;
            --orange-500: #f97316;
            --orange-600: #ea580c;

            --teal-50: #f0fdfa;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;

            /* Shadows */
            --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header Section */
        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
        }

        .stat-card.orange::before { background: linear-gradient(90deg, var(--orange-500), var(--orange-600)); }
        .stat-card.green::before { background: linear-gradient(90deg, var(--success-500), var(--success-600)); }
        .stat-card.purple::before { background: linear-gradient(90deg, var(--purple-500), var(--purple-600)); }
        .stat-card.teal::before { background: linear-gradient(90deg, var(--teal-500), var(--teal-600)); }
        .stat-card.yellow::before { background: linear-gradient(90deg, var(--warning-500), var(--warning-600)); }
        .stat-card.dark::before { background: linear-gradient(90deg, var(--gray-700), var(--gray-800)); }
        .stat-card.grey::before { background: linear-gradient(90deg, var(--gray-500), var(--gray-600)); }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            background: var(--primary-500);
        }

        .stat-card.orange .stat-icon { background: var(--orange-500); }
        .stat-card.green .stat-icon { background: var(--success-500); }
        .stat-card.purple .stat-icon { background: var(--purple-500); }
        .stat-card.teal .stat-icon { background: var(--teal-500); }
        .stat-card.yellow .stat-icon { background: var(--warning-500); }
        .stat-card.dark .stat-icon { background: var(--gray-700); }
        .stat-card.grey .stat-icon { background: var(--gray-500); }

        .export-btn {
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.2s;
        }

        .export-btn:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        /* Table Styles */
        .table-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .table-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .table-content {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        .data-table th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            font-size: 0.875rem;
            color: var(--gray-900);
        }

        .data-table tr:hover {
            background: var(--gray-50);
        }

        /* Filter Form */
        .filter-section {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .filter-form {
            display: flex;
            align-items: end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 150px;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .filter-select {
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            background: white;
            color: var(--gray-900);
            transition: all 0.2s;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px var(--primary-50);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-500);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-600);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success-500);
            color: white;
        }

        .btn-success:hover {
            background: var(--success-600);
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary-500);
            border: 1px solid var(--primary-500);
        }

        .btn-outline-primary:hover {
            background: var(--primary-500);
            color: white;
        }

        .btn-outline-primary.active {
            background: var(--primary-500);
            color: white;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-authorized {
            background: var(--primary-50);
            color: var(--primary-700);
        }

        .status-disbursed {
            background: var(--success-50);
            color: var(--success-700);
        }

        .status-rejected {
            background: var(--error-50);
            color: var(--error-700);
        }

        .status-approved {
            background: var(--teal-50);
            color: var(--teal-700);
        }

        .status-default {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        /* Task Management */
        .task-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }

        .task-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .task-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .task-content {
            padding: 1.5rem;
        }

        .task-filters {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .task-filter {
            padding: 0.5rem 1rem;
            border: 1px solid var(--gray-300);
            background: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.2s;
        }

        .task-filter.active {
            background: var(--primary-500);
            color: white;
            border-color: var(--primary-500);
        }

        .task-filter:hover {
            border-color: var(--primary-500);
            color: var(--primary-500);
        }

        .task-item {
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .task-item:hover {
            border-color: var(--primary-300);
            box-shadow: var(--shadow-sm);
        }

        /* Team Members */
        .team-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .team-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .team-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .team-content {
            padding: 1.5rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .member-card {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .member-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-300);
        }

        .member-avatar {
            position: relative;
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1rem;
        }

        .member-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .member-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 2px solid white;
            background: var(--success-500);
        }

        .member-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .member-role {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
        }

        .member-stats {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-600);
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: var(--radius-xl);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .modal-close {
            width: 2rem;
            height: 2rem;
            border: none;
            background: var(--gray-100);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .modal-body {
            padding: 1.5rem;
        }

        /* Employee Profile */
        .employee-profile {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
        }

        .profile-avatar {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .profile-info p {
            color: var(--gray-600);
            margin-bottom: 0.125rem;
        }

        /* Tabs */
        .employee-tabs {
            display: flex;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            background: none;
            color: var(--gray-500);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
        }

        .tab-btn.active {
            color: var(--primary-600);
            border-bottom-color: var(--primary-600);
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        /* Performance Metrics */
        .performance-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .metric-card {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            text-align: center;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-600);
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .team-grid {
                grid-template-columns: 1fr;
            }

            .employee-profile {
                flex-direction: column;
                text-align: center;
            }

            .employee-tabs {
                flex-wrap: wrap;
            }

            .tab-btn {
                flex: 1;
                min-width: 120px;
            }
        }

        /* Highlight for high-value leads */
        .highlight-lead {
            background-color: var(--warning-50);
            font-weight: 600;
        }

        /* Custom range display */
        #custom-range {
            display: flex;
            gap: 1rem;
            align-items: end;
            margin-top: 0.5rem;
        }

        #custom-range label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        #custom-range input {
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
        }

        /* Badge styles for task status */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .bg-success {
            background: var(--success-50);
            color: var(--success-700);
        }

        .bg-info {
            background: var(--primary-50);
            color: var(--primary-700);
        }

        .bg-warning {
            background: var(--warning-50);
            color: var(--warning-700);
        }

        /* Text utilities */
        .text-center {
            text-align: center;
        }

        .text-muted {
            color: var(--gray-600);
        }

        .fw-bold {
            font-weight: 700;
        }

        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }

        .card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }
    </style>
</head>
<body>
    @include('TeamLead.Components.sidebar')

    <div class="main-content">
        @include('TeamLead.Components.header', ['title' => 'Team Lead Dashboard', 'subtitle' => 'Manage your team and track performance'])

        <div class="dashboard-container">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <h1 class="dashboard-title">Dashboard Overview</h1>
                <p class="dashboard-subtitle">Monitor your team's performance and track key metrics</p>
            </div>

            <!-- Quick Stats Cards -->
            <div class="stats-grid">
                <!-- Total Leads -->
                <div class="stat-card blue">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('total')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['total_leads'] }}</div>
                    <div class="stat-label">Total Leads</div>
                </div>

                <!-- Personal Leads -->
                <div class="stat-card orange">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('personal')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['personal_leads'] ?? 0 }}</div>
                    <div class="stat-label">Personal Leads</div>
                </div>

                <!-- Authorized Leads -->
                <div class="stat-card green">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('authorized')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['authorized_leads'] }}</div>
                    <div class="stat-label">Authorized Leads</div>
                </div>

                <!-- Login Leads -->
                <div class="stat-card purple">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('login')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['login_leads'] }}</div>
                    <div class="stat-label">Login Leads</div>
                </div>

                <!-- Approved Leads -->
                <div class="stat-card teal">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('approved')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['approved_leads'] }}</div>
                    <div class="stat-label">Approved Leads</div>
                </div>

                <!-- Disbursed Leads -->
                <div class="stat-card yellow">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('disbursed')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['disbursed_leads'] }}</div>
                    <div class="stat-label">Disbursed Leads</div>
                </div>

                <!-- Rejected Leads -->
                <div class="stat-card dark">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <button class="export-btn" onclick="exportLeads('rejected')">
                            <i class="fas fa-file-excel"></i>
                            Export
                        </button>
                    </div>
                    <div class="stat-value">{{ $stats['rejected_leads'] }}</div>
                    <div class="stat-label">Rejected Leads</div>
                </div>

                <!-- Active Employees -->
                <div class="stat-card grey">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['active_employees'] }}</div>
                    <div class="stat-label">Active Employees</div>
                </div>
            </div>

            <!-- Today's Attendance -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-clock mr-2"></i>Today's Attendance Details</h3>
                </div>
                <div class="table-content">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Hours</th>
                                    <th>Check-In Location</th>
                                    <th>Check-Out Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td><strong>EMP{{ str_pad($attendance->employee_id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                                        <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                                        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                                        <td>
                                            @if($attendance->check_in && $attendance->check_out)
                                                @php
                                                    $in = \Carbon\Carbon::parse($attendance->check_in);
                                                    $out = \Carbon\Carbon::parse($attendance->check_out);
                                                    $duration = $in->diff($out);
                                                @endphp
                                                <strong>{{ $duration->h }}h {{ $duration->i }}m</strong>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $attendance->check_in_location ?? '-' }}</td>
                                        <td>{{ $attendance->check_out_location ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No attendance found for today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Leads Management Section -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-chart-line mr-2"></i>Leads Management</h3>
                </div>
                <div class="table-content">
                    <div style="padding: 1.5rem;">
                        <!-- Filter Form -->
                        <div class="filter-section">
                            <form method="GET" action="{{ route('team_lead.reports.index') }}" class="filter-form">
                                <div class="filter-group">
                                    <label for="filter">Filter by Date:</label>
                                    <select name="filter" id="filter" class="filter-select" onchange="toggleCustomRange(this.value)">
                                        <option value="7" {{ request('filter') == 7 ? 'selected' : '' }}>Last 7 Days</option>
                                        <option value="15" {{ request('filter') == 15 ? 'selected' : '' }}>Last 15 Days</option>
                                        <option value="30" {{ request('filter') == 30 ? 'selected' : '' }}>Last 30 Days</option>
                                        <option value="custom" {{ request('filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                    </select>
                                </div>

                                <div id="custom-range" style="display: {{ request('filter') == 'custom' ? 'flex' : 'none' }};">
                                    <div class="filter-group">
                                        <label>From:</label>
                                        <input type="date" name="from" value="{{ request('from') }}" class="filter-select">
                                    </div>
                                    <div class="filter-group">
                                        <label>To:</label>
                                        <input type="date" name="to" value="{{ request('to') }}" class="filter-select">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                    Apply Filter
                                </button>
                            </form>
                        </div>

                        @if($leads->count())
                            <div class="mb-3">
                                <a href="{{ route('team_lead.teamlead.export', ['type' => request('filter', 'total')]) }}" class="btn btn-success">
                                    <i class="fas fa-file-csv"></i>
                                    Export
                                </a>
                            </div>

                            <div class="table-wrapper">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Lead ID</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Assigned To</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leads as $lead)
                                            <tr class="{{ $lead->lead_amount > 100000 ? 'highlight-lead' : '' }}">
                                                <td><strong>#{{ $lead->id }}</strong></td>
                                                <td>{{ $lead->name }}</td>
                                                <td><strong>₹{{ number_format($lead->lead_amount, 2) }}</strong></td>
                                                <td>
                                                    <span class="status-badge
                                                        {{
                                                            $lead->status === 'authorized' ? 'status-authorized' :
                                                            ($lead->status === 'disbursed' ? 'status-disbursed' :
                                                            ($lead->status === 'rejected' ? 'status-rejected' :
                                                            ($lead->status === 'approved' ? 'status-approved' : 'status-default')))
                                                        }}">
                                                        {{ ucfirst($lead->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $lead->created_at->format('d M Y') }}</td>
                                                <td>{{ $lead->employee->name ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center" style="padding: 2rem;">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                                <p style="color: var(--gray-600); font-size: 1.125rem;">No leads found for selected filter.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Task Management Section -->
            <div class="task-card">
                <div class="task-header">
                    <h3><i class="fas fa-tasks mr-2"></i>Task Management</h3>
                </div>
                <div class="task-content">
                    <!-- Filter Buttons -->
                    <div class="task-filters">
                        <button class="task-filter active" data-filter="all">All Team</button>
                        <button class="task-filter" data-filter="individual">Individual</button>
                    </div>

                    <!-- Task List -->
                    <div id="task-list">
                        @foreach ($tasks as $task)
                            <div class="task-item" data-type="{{ $task->target_type }}">
                                <h6 class="fw-bold mb-2">{{ $task->title }}</h6>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-user-tag mr-1"></i>
                                    Assigned to:
                                    @if($task->target_type === 'all')
                                        <strong>All Team</strong>
                                    @else
                                        @php
                                            $userIds = $task->notifications->pluck('user_id')->unique()->implode(', ');
                                        @endphp
                                        {{ $userIds ?: 'N/A' }}
                                    @endif
                                    &nbsp; | &nbsp;
                                    <i class="fas fa-calendar mr-1"></i>
                                    Due: <strong>{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</strong>
                                </p>
                                <p class="mb-2">{{ $task->description }}</p>
                                <span class="badge
                                    @if($task->status == 'completed') bg-success
                                    @elseif($task->status == 'in_progress') bg-info
                                    @else bg-warning @endif
                                ">
                                    {{ strtoupper($task->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Team Members Overview -->
            <div class="team-card">
                <div class="team-header">
                    <h3><i class="fas fa-users mr-2"></i>Team Members Overview</h3>
                    {{-- <button class="btn btn-secondary" onclick="viewAllMembers()">
                        <i class="fas fa-users"></i>
                        View All
                    </button> --}}
                </div>
                <div class="team-content">
                    <div class="team-grid">
                        @foreach($employees as $employee)
                            <div class="member-card" onclick="viewMemberDetails({{ $employee->id }})">
                                <div class="member-avatar">
                                    <img src="{{ $employee->profile_photo ? asset($employee->profile_photo) : asset('images/placeholder.svg') }}" alt="{{ $employee->name }}">
                                    <div class="member-status"></div>
                                </div>
                                <div class="member-info">
                                    <div class="member-name">{{ $employee->name }}</div>
                                    <div class="member-role">{{ $employee->designation ?? 'N/A' }}</div>
                                    <div class="member-stats">
                                        <div class="stat-item">
                                            <div class="stat-label">Performance</div>
                                            <div class="stat-value">{{ $employee->performance_rate ?? '0' }}%</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-label">Attendance</div>
                                            <div class="stat-value">{{ $employee->attendance_rate ?? '0' }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details Modal -->
    <div class="modal-overlay" id="employeeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Employee Details</h2>
                <button class="modal-close" onclick="closeEmployeeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="employee-details">
                    <div class="employee-profile">
                        <div class="profile-avatar">
                            <img id="employeeAvatar" src="/placeholder.svg" alt="">
                        </div>
                        <div class="profile-info">
                            <h3 id="employeeName">--</h3>
                            <p id="employeeRole">--</p>
                            <p id="employeeId">--</p>
                        </div>
                    </div>

                    <div class="employee-tabs">
                        <button class="tab-btn active" onclick="showTab('performance')">Performance</button>
                        <button class="tab-btn" onclick="showTab('attendance')">Attendance</button>
                        <button class="tab-btn" onclick="showTab('tasks')">Tasks</button>
                        <button class="tab-btn" onclick="showTab('overview')">Overview</button>
                    </div>

                    <div class="tab-content">
                        <div id="performance" class="tab-pane active">
                            <div class="performance-metrics">
                                <div class="metric-card">
                                    <div class="metric-value" id="overallPerformance">--%</div>
                                    <div class="metric-label">Overall Performance</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="leadsCompleted">--</div>
                                    <div class="metric-label">Leads Completed</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="revenueGenerated">--</div>
                                    <div class="metric-label">Revenue Generated</div>
                                </div>
                            </div>
                        </div>

                        <div id="attendance" class="tab-pane">
                            <div class="performance-metrics">
                                <div class="metric-card">
                                    <div class="metric-value" id="attendanceRate">--%</div>
                                    <div class="metric-label">Attendance Rate</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="presentDays">--</div>
                                    <div class="metric-label">Present Days</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="halfDays">--</div>
                                    <div class="metric-label">Half Days</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="absentDays">--</div>
                                    <div class="metric-label">Absent Days</div>
                                </div>
                            </div>
                        </div>

                        <div id="tasks" class="tab-pane">
                            <div class="performance-metrics">
                                <div class="metric-card">
                                    <div class="metric-value" id="totalTasks">--</div>
                                    <div class="metric-label">Total Tasks</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="completedTasks">--</div>
                                    <div class="metric-label">Completed</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-value" id="inProgressTasks">--</div>
                                    <div class="metric-label">In Progress</div>
                                </div>
                            </div>
                        </div>

                        <div id="overview" class="tab-pane">
                            <div style="display: grid; gap: 1rem;">
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-200);">
                                    <label style="font-weight: 600; color: var(--gray-600);">Email:</label>
                                    <span id="employeeEmail">--</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-200);">
                                    <label style="font-weight: 600; color: var(--gray-600);">Phone:</label>
                                    <span id="employeePhone">--</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-200);">
                                    <label style="font-weight: 600; color: var(--gray-600);">Department:</label>
                                    <span id="employeeDepartment">--</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-200);">
                                    <label style="font-weight: 600; color: var(--gray-600);">Join Date:</label>
                                    <span id="employeeJoinDate">--</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-200);">
                                    <label style="font-weight: 600; color: var(--gray-600);">Manager:</label>
                                    <span id="employeeManager">--</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                                    <label style="font-weight: 600; color: var(--gray-600);">Location:</label>
                                    <span id="employeeLocation">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('TeamLead.Components.script')

    <script>
        function toggleCustomRange(value) {
            const customRange = document.getElementById('custom-range');
            customRange.style.display = (value === 'custom') ? 'flex' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Task filter functionality
            document.querySelectorAll('.task-filter').forEach(button => {
                button.addEventListener('click', function () {
                    // Remove active class from all buttons
                    document.querySelectorAll('.task-filter').forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.add('btn-outline-primary');
                        btn.classList.remove('btn-primary');
                    });

                    // Add active class to clicked button
                    this.classList.add('active');
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');

                    const filterType = this.getAttribute('data-filter');

                    // Show/hide tasks based on filter
                    document.querySelectorAll('.task-item').forEach(task => {
                        const taskType = task.getAttribute('data-type');
                        if (filterType === 'all') {
                            task.style.display = 'block';
                        } else {
                            task.style.display = (filterType === taskType) ? 'block' : 'none';
                        }
                    });
                });
            });
        });

        function viewMemberDetails(userId) {
            fetch(`/team-lead/employee/details/${userId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate modal with employee data
                    document.getElementById('employeeName').textContent = data.name;
                    document.getElementById('employeeRole').textContent = data.designation;
                    document.getElementById('employeeId').textContent = 'EMP' + data.id;

                    const avatar = document.getElementById('employeeAvatar');
                    if (avatar) {
                        avatar.src = data.profile_photo || '/images/placeholder.svg';
                    }

                    // Performance data
                    document.getElementById('overallPerformance').textContent = data.performance_rate + '%';
                    document.getElementById('leadsCompleted').textContent = data.leads_completed;
                    document.getElementById('revenueGenerated').textContent = data.revenue_generated;

                    // Attendance data
                    document.getElementById('attendanceRate').textContent = data.attendance_rate + '%';
                    document.getElementById('presentDays').textContent = data.present_days;
                    document.getElementById('absentDays').textContent = data.absent_days;
                    document.getElementById('halfDays').textContent = data.half_days;

                    // Task data
                    document.getElementById('totalTasks').textContent = data.total_tasks;
                    document.getElementById('completedTasks').textContent = data.completed_tasks;
                    document.getElementById('inProgressTasks').textContent = data.in_progress_tasks;

                    // Overview data
                    document.getElementById('employeeEmail').textContent = data.email;
                    document.getElementById('employeePhone').textContent = data.phone;
                    document.getElementById('employeeDepartment').textContent = data.department;
                    document.getElementById('employeeJoinDate').textContent = data.join_date;
                    document.getElementById('employeeManager').textContent = data.manager;
                    document.getElementById('employeeLocation').textContent = data.location;

                    // Show modal
                    document.getElementById('employeeModal').classList.add('show');
                })
                .catch(error => {
                    console.error('Error fetching employee details:', error);
                });
        }

        function closeEmployeeModal() {
            const modal = document.getElementById('employeeModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        function showTab(tabName) {
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab pane
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab button
            event.target.classList.add('active');
        }

        function exportLeads(type) {
            window.location.href = `/team-lead/export-report/${type}`;
        }

        function viewAllMembers() {
            // Implement view all members functionality
            console.log('View all members clicked');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('employeeModal');
            if (event.target === modal) {
                closeEmployeeModal();
            }
        });
    </script>
</body>
</html>
