<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="lead-report-url" content="{{ route('operations.report') }}">
    <title>Operations Reports - Lead Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.jpg') }}">
     <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
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
            --success-600: #016948;
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
            --orange-50: #fff7ed;
            --orange-100: #ffedd5;
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --indigo-50: #eef2ff;
            --indigo-100: #e0e7ff;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
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
            /* Add Blue variables (mapped to Tailwind's blue palette) */
    --blue-50: #eff6ff;  /* Matches bg-blue-50 */
    --blue-100: #dbeafe; /* Matches bg-blue-100 */
    --blue-500: #3b82f6; /* Matches bg-blue-500 */
    --blue-600: #2563eb; /* Matches bg-blue-600 */
    --blue-800: #1d4ed8; /* Matches text-blue-800 */
    --blue-900: #1e3a8a; /* Matches text-blue-900 */

    /* Add Cyan variables (mapped to Tailwind's cyan palette) */
    --cyan-50: #ecfeff;  /* Matches bg-cyan-50 */
    --cyan-100: #cffafe; /* Matches bg-cyan-100 */
    --cyan-500: #06b6d4; /* Matches bg-cyan-500 */
    --cyan-600: #0891b2; /* Matches bg-cyan-600 */
    --cyan-800: #0e7490; /* Matches text-cyan-800 */
    --cyan-900: #155e75; /* Matches text-cyan-900 */
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--primary-50) 100%);
            color: var(--gray-900);
            line-height: 1.6;
            min-height: 100vh;
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
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }
        .dashboard-header {
            margin-bottom: 2.5rem;
            text-align: center;
            animation: fadeInDown 0.8s ease-out;
        }
        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary-600), var(--indigo-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        .dashboard-subtitle {
            font-size: 1.125rem;
            color: var(--gray-600);
            font-weight: 500;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
      .stat-card {
    background: white;
    border-radius: var(--radius-2xl);
    padding: 0.5rem; /* Further decreased */
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-100);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    animation: slideUp 0.6s ease-out;
    animation-fill-mode: both;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    min-height: 80px; /* Further decreased */
    min-width: 60px; /* Further decreased */
}
.bg-emerald-50 { background-color: #ccf5d3}
.bg-emerald-100 { background-color: #24d06c; }
 .bg-violet-50 { background-color: #f5f3ff; }
 .bg-amber-50 { background-color: #fffbeb; }
 .bg-amber-100 { color: #92400e; }
 .bg-cyan-50 { background-color: #ecfeff; }
/* Optional: Adjust hover effect scale if size changes significantly */
.stat-card:hover {
    transform: translateY(-8px) scale(1.02); /* Adjust scale value if needed */
    box-shadow: var(--shadow-xl);
}
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }
        .stat-card:nth-child(7) { animation-delay: 0.7s; }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-500), var(--indigo-500));
        }
        .stat-card.total::before {
            background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
        }
        .stat-card.authorized::before {
            background: linear-gradient(90deg, var(--success-500), var(--success-600));
        }
        .stat-card.login::before {
            background: linear-gradient(90deg, var(--purple-500), var(--purple-600));
        }
        .stat-card.approved::before {
            background: linear-gradient(90deg, var(--teal-500), var(--teal-600));
        }
        .stat-card.disbursed::before {
            background: linear-gradient(90deg, var(--warning-500), var(--warning-600));
        }
        .stat-card.rejected::before {
            background: linear-gradient(90deg, var(--error-500), var(--error-600));
        }
        .stat-card.employees::before {
            background: linear-gradient(90deg, var(--gray-500), var(--gray-600));
        }
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-xl);
        }
        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .stat-icon {
            width: 1.7rem;
            height: 1.7rem;
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: white;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            box-shadow: var(--shadow-md);
        }
        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
        }
        .stat-card.authorized .stat-icon {
            background: linear-gradient(135deg, var(--warning-500), var(--warning-600));
        }
        .stat-card.login .stat-icon {
            background: linear-gradient(135deg, var(--purple-500), var(--purple-600));
        }
        .stat-card.approved .stat-icon {
            background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
        }
        .stat-card.disbursed .stat-icon {
            background: linear-gradient(135deg, var(--success-500), var(--success-600));
        }
        .stat-card.rejected .stat-icon {
            background: linear-gradient(135deg, var(--error-500), var(--error-600));
        }
        .stat-card.employees .stat-icon {
            background: linear-gradient(135deg, var(--gray-500), var(--gray-600));
        }
        .stat-content {
            flex: 1;
        }
        .stat-main {
            margin-bottom: 1rem;
        }
        .stat-count {
            font-size: 1.7rem;
            font-weight: 900;
            color: var(--gray-900);
            margin-bottom: 0.15rem;
            line-height: 1;
        }
        .stat-label {
            font-size: 1.5rem;
            color: var(--gray-600);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .stat-value {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
        }
        .stat-value-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-800);
        }
        .stat-value-label {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }
        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
        }
        .stat-trend.up {
            background: var(--success-100);
            color: var(--success-700);
        }
        .stat-trend.down {
            background: var(--error-100);
            color: var(--error-700);
        }
        .filters-section {
            background: white;
            border-radius: var(--radius-2xl);
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            animation: fadeInDown 0.8s ease-out;
        }
        .filters-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .filters-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filters-toggle {
            background: var(--primary-50);
            border: 1px solid var(--primary-200);
            color: var(--primary-700);
            padding: 0.5rem 1rem;
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        .filters-toggle:hover {
            background: var(--primary-100);
            color: var(--primary-800);
        }
        .filters-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }
        .filter-input,
        .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            color: var(--gray-900);
            background: white;
            transition: all 0.2s ease;
        }
        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px var(--primary-50);
        }
        .filter-actions {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }
        .btn-filter {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-md);
        }
        .btn-filter:hover {
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .btn-reset {
            padding: 0.75rem 1.5rem;
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-reset:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }
        .chart-card {
            background: white;
            border-radius: var(--radius-2xl);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            animation: slideInLeft 0.8s ease-out;
            transition: all 0.3s ease;
        }
        .chart-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .chart-container {
            height: 300px;
            position: relative;
        }
        .team-performance-section {
            margin-bottom: 2.5rem;
            animation: slideInRight 0.8s ease-out;
        }
        .team-performance-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .team-performance-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .team-performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .team-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-100);
            transition: all 0.3s ease;
        }
        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .team-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .team-avatar {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            box-shadow: var(--shadow-md);
        }
        .team-info {
            flex: 1;
        }
        .team-name {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        .team-role {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
        }
        .team-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .team-stat {
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            padding: 1rem;
            text-align: center;
            border: 1px solid var(--gray-200);
        }
        .team-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        .team-stat-label {
            font-size: 0.75rem;
            color: var(--gray-600);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .team-progress {
            margin-bottom: 1rem;
        }
        .progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .progress-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }
        .progress-value {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--primary-600);
        }
        .progress-bar {
            height: 0.5rem;
            background: var(--gray-200);
            border-radius: 9999px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
            border-radius: 9999px;
            transition: width 0.8s ease;
        }
        .leads-table-section {
            background: white;
            border-radius: var(--radius-2xl);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            margin-bottom: 2rem;
            animation: slideUp 0.8s ease-out;
        }
        .leads-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }
        .leads-table-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .leads-table-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .leads-search {
            position: relative;
        }
        .leads-search input {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            color: var(--gray-900);
            background: white;
            width: 300px;
            transition: all 0.2s ease;
        }
        .leads-search input:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px var(--primary-50);
        }
        .leads-search i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            text-transform: uppercase;
        }
        .leads-table {
            width: 100%;
            border-collapse: collapse;
        }
         .leads-table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            border-bottom: 1px solid var(--gray-200);
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .leads-table td {
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            color: var(--gray-900);
            border-bottom: 1px solid var(--gray-200);
            white-space: nowrap;
        }

        .leads-table tr:hover td {
            background: var(--gray-50);
        }
        .lead-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-new {
            background: var(--primary-100);
            color: var(--primary-700);
        }
        .status-contacted {
            background: var(--warning-100);
            color: var(--warning-700);
        }
        .status-qualified {
            background: var(--success-100);
            color: var(--success-700);
        }
        .status-converted {
            background: var(--teal-100);
            color: var(--teal-700);
        }
        .status-closed {
            background: var(--error-100);
            color: var(--error-700);
        }
        .status-authorized {
            background: var(--primary-100);
            color: var(--primary-700);
        }
        .status-disbursed {
            background: var(--success-100);
            color: var(--success-700);
        }
        .status-rejected {
            background: var(--error-100);
            color: var(--error-700);
        }
        .status-approved {
            background: var(--teal-100);
            color: var(--teal-700);
        }
        .status-login {
            background: var(--purple-100);
            color: var(--purple-700);
        }
        /* Pagination Styles */
        .pagination-wrapper {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pagination {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
        }
        .pagination .page-item {
            display: flex;
            align-items: center;
        }
        .pagination .page-link {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: var(--shadow-xs);
        }
        .pagination .page-link:hover {
            background: var(--primary-50);
            color: var(--primary-700);
            border-color: var(--primary-200);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            border-color: var(--primary-600);
            box-shadow: var(--shadow-md);
        }
        .pagination .page-item.disabled .page-link {
            color: var(--gray-400);
            background: var(--gray-100);
            border-color: var(--gray-200);
            cursor: not-allowed;
            box-shadow: none;
        }
        .pagination .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-50);
            border-color: var(--primary-500);
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .dashboard-container {
                padding: 1rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .filters-content {
                grid-template-columns: 1fr;
            }
            .team-performance-grid {
                grid-template-columns: 1fr;
            }
            .leads-table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .leads-table-actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }
            .leads-search input {
                width: 100%;
            }
            .filter-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }
            .pagination .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }
        .text-center {
            text-align: center;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--gray-500);
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gray-400);
        }
        .stat-card {
    background: var(--gray-50);
    border: 1px solid var(--gray-100);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 0;
    min-height: 200px;
    transition: all 0.2s ease-in-out;
    position: relative;
    animation: slideUp 0.6s ease-out;
    animation-fill-mode: both;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
}

.stat-card.total {
    background: var(--primary-50);
    border: 1px solid var(--primary-100);
}

.stat-card.total::before {
    background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
}

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: var(--primary-600);
}

.stat-card.pending {
    background: var(--purple-50);
    border: 1px solid var(--purple-100);
}

.stat-card.pending::before {
    background: linear-gradient(90deg, var(--purple-500), var(--purple-600));
}

.stat-card.pending .stat-icon {
    background: linear-gradient(135deg, var(--purple-500), var(--purple-600));
    color: var(--purple-600);
}

.stat-card.personal_lead {
    background: var(--blue-50);
    border: 1px solid var(--blue-100);
}

.stat-card.personal_lead::before {
    background: linear-gradient(90deg, var(--blue-500), var(--blue-600));
}

.stat-card.personal_lead .stat-icon {
    background: linear-gradient(135deg, var(--blue-500), var(--blue-600));
    color: var(--blue-600);
}

.stat-card.authorized {
    background: var(--cyan-50);
    border: 1px solid var(--cyan-100);
}

.stat-card.authorized::before {
    background: linear-gradient(90deg, var(--cyan-500), var(--cyan-600));
}

.stat-card.authorized .stat-icon {
    background: linear-gradient(135deg, var(--cyan-500), var(--cyan-600));
    color: var(--cyan-600);
}

.stat-card.login {
    background: var(--warning-50);
    border: 1px solid var(--warning-100);
}

.stat-card.login::before {
    background: linear-gradient(90deg, var(--warning-500), var(--warning-600));
}

.stat-card.login .stat-icon {
    background: linear-gradient(135deg, var(--warning-500), var(--warning-600));
    color: var(--warning-600);
}

.stat-card.approved {
    background: var(--teal-50);
    border: 1px solid var(--teal-100);
}

.stat-card.approved::before {
    background: linear-gradient(90deg, var(--teal-500), var(--teal-600));
}

.stat-card.approved .stat-icon {
    background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
    color: var(--teal-600);
}

.stat-card.disbursed {
    background: var(--success-100);
    border: 1px solid var(--success-100);
}

.stat-card.disbursed::before {
    background: linear-gradient(90deg, var(--success-500), var(--success-600));
}

.stat-card.disbursed .stat-icon {
    background: linear-gradient(135deg, var(--success-500), var(--success-600));
    color: var(--success-600);
}

.stat-card.rejected {
    background: var(--error-50);
    border: 1px solid var(--error-100);
}

.stat-card.rejected::before {
    background: linear-gradient(90deg, var(--error-500), var(--error-600));
}

.stat-card.rejected .stat-icon {
    background: linear-gradient(135deg, var(--error-500), var(--error-600));
    color: var(--error-600);
}

.stat-card .stat-icon {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 1.5rem;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
.stat-card:nth-child(5) { animation-delay: 0.5s; }
.stat-card:nth-child(6) { animation-delay: 0.6s; }
.stat-card:nth-child(7) { animation-delay: 0.7s; }
.stat-card:nth-child(8) { animation-delay: 0.8s; }



.greeting-container {
    display: flex;
    align-items: center;
    background: linear-gradient(90deg, #e0e7ff 0%, #f3f4f6 100%);
    padding: 1.25rem 2rem;
    border-radius: 1.5rem;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2); /* stronger subtle blue shadow */
    cursor: default; /* Ensures not clickable */
}







        .greeting-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            color: var(--primary-600);
        }

        .greeting-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .user-name {
            color: var(--primary-600);
            font-weight: 700;
        }


    </style>
</head>
<body>
    @include('TeamLead.Components.sidebar')

    <div class="main-content">
        @include('TeamLead.Components.header', ['title' => 'Operations Reports', 'subtitle' => 'Analyze system-wide lead performance and conversion metrics'])

        <div class="dashboard-container">
       <!-- Greeting Message -->
            <div class="greeting-container">
                <span class="greeting-icon" id="greeting-icon">
                    <i class="fas fa-sun"></i>
                </span>
                <span class="greeting-text" id="greeting-text">
                    Good morning, <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>!
                </span>
            </div>


 <!-- Combined Leads Filter and Overview -->
<div class="dashboard-card p-1 mb-0">

    <!-- Leads Filter Form -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
    <form method="GET" action="{{ route('team_lead.dashboard') }}" class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-4" id="tableFilterForm">
        <!-- From Date Field with Calendar Icon -->
        <div class="space-y-2 relative">
            <label for="table_from_date" class="block text-sm font-semibold text-gray-700">From Date</label>
            <div class="relative">
                <input type="text"
                       name="table_from_date"
                       id="table_from_date"
                       value="{{ $tableFromDate ?? '' }}"
                       placeholder="Select date"
                       class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500 pr-10"
                       {{ $tableMonth ? 'disabled' : '' }}>
                <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-calendar-alt"></i>
                </span>
            </div>
        </div>

        <!-- To Date Field with Calendar Icon -->
        <div class="space-y-2 relative">
            <label for="table_to_date" class="block text-sm font-semibold text-gray-700">To Date</label>
            <div class="relative">
                <input type="text"
                       name="table_to_date"
                       id="table_to_date"
                       value="{{ $tableToDate ?? '' }}"
                       placeholder="Select date"
                       class="form-input w-full flatpickr bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500 pr-10"
                       {{ $tableMonth ? 'disabled' : '' }}>
                <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-calendar-alt"></i>
                </span>
            </div>
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

        <div class="flex items-center space-x-3">
            <button type="submit" class="btn-filter btn-primary flex items-center justify-center">
                <i class="fas fa-filter mr-1"></i>Apply
            </button>
            <button type="button" onclick="resetLeadTableFilters()" class="btn-reset btn-secondary flex items-center justify-center">
                <i class="fas fa-undo mr-1"></i>Reset
            </button>
        </div>
    </form>
</div>
<!-- Dashboard: This Month Leads Overview -->
<div class="card-header p-1 -m-8 mb-6">
    {{-- <h2 class="text-lg font-semibold text-gray-800">This Month Leads Overview</h2> --}}
</div>
<div class="dashboard-card p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    {{-- Total Leads Card --}}
    <a href="{{ route('team_lead.dashboard.leads.byStatus', ['status' => 'total', 'lead_type' => $tableLeadType, 'from_date' => $tableFromDate, 'to_date' => $tableToDate, 'month' => $tableMonth]) }}"
       class="bg-blue-50 p-6 rounded-xl shadow-sm flex flex-col items-center justify-center text-center">
        <i class="fas fa-users text-2xl text-blue-700 mb-1"></i>
        <div class="text-2xl font-semibold text-blue-800">Total Leads</div>
        <div class="text-2xl font-bold text-gray-900">{{ $totalLeads }}</div>
        <div class="text-2xl font-semibold text-gray-700">{{ \App\Helpers\FormatHelper::formatToIndianCurrency($totalValuation) }}</div>
    </a>

    {{-- Status Cards --}}
    @foreach ([
        'personal_lead' => ['label' => 'Personal lead', 'icon' => 'fas fa-user-plus', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
        'authorized' => ['label' => 'Authorized', 'icon' => 'fas fa-check-circle', 'bg' => 'bg-cyan-50', 'text' => 'text-cyan-800'],
        'login' => ['label' => 'Login', 'icon' => 'fas fa-sign-in-alt', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        'approved' => ['label' => 'Approved', 'icon' => 'fas fa-thumbs-up', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
        'rejected' => ['label' => 'Rejected', 'icon' => 'fas fa-times-circle', 'bg' => 'bg-red-100', 'text' => 'text-red-800'],
        'disbursed' => ['label' => 'Disbursed', 'icon' => 'fas fa-money-bill-wave', 'bg' => 'bg-green-50', 'text' => 'text-green-800'],
        'future_lead' => ['label' => 'Future lead', 'icon' => 'fas fa-clock', 'bg' => 'bg-lime-50', 'text' => 'text-lime-800'],
    ] as $status => $meta)
        @php
            $count = $leadsByStatus[$status]['count'] ?? 0;
            $valuation = $leadsByStatus[$status]['total_valuation'] ?? 0;
        @endphp
        <a href="{{ route('team_lead.dashboard.leads.byStatus', ['status' => $status, 'lead_type' => $tableLeadType, 'from_date' => $tableFromDate, 'to_date' => $tableToDate, 'month' => $tableMonth]) }}"
           class="{{ $meta['bg'] }} p-6 rounded-xl shadow-sm flex flex-col items-center justify-center text-center">
            <i class="{{ $meta['icon'] }} text-3xl {{ $meta['text'] }} mb-1"></i>
            <div class="text-2xl font-semibold {{ $meta['text'] }}">{{ $meta['label'] }}</div>
            <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
            <div class="text-2xl font-semibold mt-1">
                @if($status !== 'future_lead')
                    {{ \App\Helpers\FormatHelper::formatToIndianCurrency($valuation) }}

                @endif
            </div>
        </a>
    @endforeach

</div>
</div>




<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for date pickers
    const fromDatePicker = flatpickr('#table_from_date', {
        dateFormat: 'Y-m-d',
        defaultDate: document.getElementById('table_from_date').value || null,
        enableTime: false,
        onChange: function(selectedDates, dateStr, instance) {
            toggleFilters();
        }
    });

    const toDatePicker = flatpickr('#table_to_date', {
        dateFormat: 'Y-m-d',
        defaultDate: document.getElementById('table_to_date').value || null,
        enableTime: false,
        onChange: function(selectedDates, dateStr, instance) {
            toggleFilters();
        }
    });

    // Toggle mutual exclusivity for Leads Filter
    const fromDateInput = document.getElementById('table_from_date');
    const toDateInput = document.getElementById('table_to_date');
    const monthSelect = document.getElementById('table_month');

    function toggleFilters() {
        if (monthSelect.value !== '') {
            fromDateInput.disabled = true;
            toDateInput.disabled = true;
            fromDateInput.value = '';
            toDateInput.value = '';
            fromDatePicker.clear();
            toDatePicker.clear();
        } else {
            fromDateInput.disabled = false;
            toDateInput.disabled = false;
        }

        if (fromDateInput.value || toDateInput.value) {
            monthSelect.disabled = true;
            monthSelect.value = '';
        } else {
            monthSelect.disabled = false;
        }
    }

    // Bind toggleFilters to input changes
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
        const fromDatePicker = flatpickr('#table_from_date');
        const toDatePicker = flatpickr('#table_to_date');
        fromDatePicker.clear();
        toDatePicker.clear();
        document.getElementById('table_from_date').value = '';
        document.getElementById('table_to_date').value = '';
        document.getElementById('table_month').value = '';
        document.getElementById('table_lead_type').value = '';
        document.getElementById('table_from_date').disabled = false;
        document.getElementById('table_to_date').disabled = false;
        document.getElementById('table_month').disabled = false;
        form.submit();
    }
}

// Reset Today's Leads Filter (if still needed, though not used in the provided HTML)
function resetTodaysLeadsFilters() {
    const form = document.getElementById('todaysLeadsFilterForm');
    if (form) {
        form.reset();
        document.getElementById('todays_lead_type').value = '';
        form.submit();
    }
}

 // Function to update greeting based on time of day
        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            const userName = "{{ Auth::user()->name ?? 'User' }}";

            let greeting, icon;

            if (hour >= 5 && hour < 12) {
                greeting = `Good morning, <span class="user-name">${userName}</span>!`;
                icon = 'fas fa-sun';
            } else if (hour >= 12 && hour < 17) {
                greeting = `Good afternoon, <span class="user-name">${userName}</span>!`;
                icon = 'fas fa-sun';
            } else if (hour >= 17 && hour < 21) {
                greeting = `Good evening, <span class="user-name">${userName}</span>!`;
                icon = 'fas fa-moon';
            } else {
                greeting = `Good night, <span class="user-name">${userName}</span>!`;
                icon = 'fas fa-moon';
            }

            document.getElementById('greeting-text').innerHTML = greeting;
            document.getElementById('greeting-icon').innerHTML = `<i class="${icon}"></i>`;
        }

        // Update greeting on page load
        document.addEventListener('DOMContentLoaded', updateGreeting);

        // Update greeting every minute to handle edge cases
        setInterval(updateGreeting, 60000);

</script>
</body>
</html>
