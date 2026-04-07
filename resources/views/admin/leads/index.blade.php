<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Leads Management - Lead Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
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
            max-width: 1800px;
            margin: 0 auto;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            animation: fadeInDown 0.6s ease-out;
        }
        .header-content {
            flex: 1;
        }
        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
        }
        .page-subtitle {
            font-size: 15px;
            color: #6b7280;
            font-weight: 500;
        }
        .filter-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .filter-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        .filter-toggle.active {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .advanced-filters {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: none;
            animation: slideDown 0.4s ease-out;
        }
        .advanced-filters.active {
            display: block;
        }
        .filters-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }
        .filters-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filters-title i {
            color: #667eea;
        }
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-group label i {
            color: #667eea;
            font-size: 12px;
        }
        .filter-select,
        .filter-input {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1f2937;
            background: #f9fafb;
            transition: all 0.3s ease;
        }
        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-1px);
        }
        .search-input-wrapper {
            position: relative;
        }
        .search-input-wrapper i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }
        .filter-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 2px solid #f1f5f9;
        }
        .filter-stats {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }
        .filter-buttons {
            display: flex;
            gap: 12px;
        }
        .btn-reset {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-reset:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .btn-apply {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .leads-content-grid {
            display: block;
        }
        .leads-table-container {
            background: white;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: slideInRight 0.6s ease-out;
            width: 100%;
            margin: 0 auto;
        }
        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            width: 100%;
             background: linear-gradient(135deg, #c4d6e9 100%,#f8f9fa 0% );
            color: black;
        }
        .table-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }
        .table-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .results-info {
            font-size: 16px;
            color: #6b7280;
            font-weight: 500;
        }
        .btn-refresh {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-refresh:hover {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border-color: #cbd5e1;
            color: #3b82f6;
            transform: translateY(-2px);
        }
        .table-wrapper {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            width: 100%;
        }
        .leads-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }
        .leads-table th {
           color: black;
                background: linear-gradient(135deg, #f8f9fa 0%, #c4d6e9 100%);
            text-align: left;
            font-weight: 600;

            border-bottom: 2px solid #e5e7eb;
            font-size: 16px;
            padding: 20px 24px;
            white-space: nowrap;
        }
        .leads-table td {
            padding: 18px 24px;
            font-size: 15px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .leads-table tr:last-child td {
            border-bottom: none;
        }
        .leads-table tbody tr {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .leads-table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transform: translateY(-1px);
        }
        .status {
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status.personal_lead {
            background: #fef3c7;
            color: #92400e;
        }
        .status.authorized {
            background: #dbeafe;
            color: #1e40af;
        }
        .status.approved {
            background: #dcfce7;
            color: #166534;
        }
        .status.rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .status.disbursed {
            background: #d1fae5;
            color: #065f46;
        }
        .status.future_lead {
            background: #fef9c3;
            color: #713f12;
        }
        .status.login {
            background: #e0e7ff;
            color: #3730a3;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-container {
            background: white;
            border-radius: 24px;
            max-width: 1000px;
            width: 100%;
            max-height: 100vh;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
            position: relative;
        }
        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }
        .modal-container.modal-sm {
            max-width: 500px;
        }
        .modal-header {
            padding: 24px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .modal-close {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10000;
            padding: 0;
        }
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        .modal-close i {
            font-size: 18px;
        }
        .modal-content {
            padding: 32px;
            overflow-y: auto;
            max-height: calc(90vh - 160px);
        }
        .modal-footer {
            padding: 24px 32px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
        }
        .lead-detail-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 32px;
        }
        .lead-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .lead-basic-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .lead-basic-info p {
            color: #6b7280;
            margin-bottom: 24px;
            font-size: 16px;
        }
        .lead-contact {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 15px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .contact-item i {
            width: 20px;
            color: #667eea;
            font-size: 16px;
        }
        .detail-section {
            margin-bottom: 32px;
        }
        .detail-section h4 {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            position: relative;
            padding-left: 20px;
        }
        .detail-section h4::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .detail-item label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-item span {
            font-size: 15px;
            color: #1f2937;
            font-weight: 600;
        }
        .detail-item input,
        .detail-item select {
            font-size: 15px;
            color: #1f2937;
            font-weight: 600;
            background: transparent;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 4px 0;
        }
        .detail-item input:focus,
        .detail-item select:focus {
            outline: none;
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            border: 2px solid #667eea;
        }
        .remarks-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        .remarks-box p {
            color: #4b5563;
            line-height: 1.6;
            margin: 0;
            font-size: 15px;
        }
        .confirm-message {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 20px;
        }
        .confirm-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        .confirm-icon.approve {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .confirm-icon.reject {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .confirm-icon.disburse {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }
        .confirm-icon.authorize {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        .confirm-icon.future_lead {
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
            color: white;
        }
        .confirm-message p {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.6;
        }
        .confirm-message h3 {
            color: #1f2937;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .edit-controls {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .document-list {
            display: grid;
            gap: 16px;
        }
        .document-item {
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: space-between;
        }
        .document-item label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            display: block;
        }
        .document-item a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10000;
        }
        .document-item a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .document-item p {
            margin: 8px 0 0 0;
            padding: 0;
            font-size: 14px;
            color: #4b5563;
        }
        .btn-primary,
        .btn-secondary,
        .btn-danger,
        .btn-success,
        .btn-authorize,
        .btn-future {
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        .btn-primary:disabled {
            background: #93c5fd;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: #f8fafc;
            color: #4b5563;
            border: 2px solid #e2e8f0;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }
        .btn-secondary:disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }
        .btn-danger:disabled {
            background: #f87171;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        .btn-success:disabled {
            background: #6ee7b7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-authorize {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        .btn-authorize:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        .btn-authorize:disabled {
            background: #93c5fd;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-future {
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
            color: white;
        }
        .btn-future:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(250, 204, 21, 0.4);
        }
        .btn-future:disabled {
            background: #fde68a;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }


        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px;
            border-radius: 8px;
            color: white;
            z-index: 10000;
            transition: opacity 0.3s ease;
        }
        .notification.success { background: #10b981; }
        .notification.error { background: #ef4444; }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9998;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @media (max-width: 1200px) {
            .leads-content-grid {
                display: block;
            }
            .leads-table-container {
                order: 1;
                margin-bottom: 24px;
            }
            .lead-detail-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .dashboard-container {
                padding: 16px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .filter-section {
                width: 100%;
            }
            .filter-toggle {
                width: 100%;
                justify-content: space-between;
            }
            .filters-grid {
                grid-template-columns: 1fr;
            }
            .lead-detail-grid {
                grid-template-columns: 1fr;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
            .modal-content {
                padding: 20px;
            }
            .modal-header {
                padding: 20px;
            }
            .modal-footer {
                padding: 20px;
            }
        }
        .leads-table td,
.leads-table th {
    text-transform: uppercase;
}
.lead-detail-grid,
.lead-detail-grid input,
.lead-detail-grid span,
.lead-detail-grid select,
.lead-detail-grid p,
.lead-detail-grid a {
    text-transform: uppercase;
}
.lead-type-display {
    font-size: 15px;
    color: #1f2937;
    font-weight: 600;
    text-transform: uppercase;
    padding: 4px 0;
    display: inline-block;
}

 input.editable-field#modalName,
    #modalLeadName,
    .lead-avatar-large#modalLeadInitials {
        text-transform: none !important;
    }

    #forwardOperationsModal {
    z-index: 10001 !important; /* Higher than lead detail modal's 9999 */
}
    </style>
</head>
<body>
    @include('admin.Components.sidebar')
    <div class="main-content">
        @include('admin.Components.header', ['title' => 'Admin Leads Management', 'subtitle' => 'Manage and process leads'])
        <div class="dashboard-container">
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Admin Leads Management</h1>
                    <p class="page-subtitle">Filter and manage leads efficiently</p>
                </div>
                <div class="filter-section">
                    <button class="filter-toggle active" onclick="toggleFilters()">
                        <i class="fas fa-filter"></i>
                        <span>Advanced Filters</span>
                        <i class="fas fa-chevron-down" id="filterChevron" style="transform: rotate(180deg);"></i>
                    </button>
                </div>
            </div>

            <div class="advanced-filters active" id="advancedFilters">
                <div class="filters-header">
                    <div class="filters-title">
                        <i class="fas fa-sliders-h"></i>
                        Filter Leads
                    </div>
                </div>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label><i class="fas fa-search"></i> Search by Name/Email</label>
                        <div class="search-input-wrapper">
                            <input type="text" id="searchFilter" class="filter-input" placeholder="Enter name or email...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-flag"></i> Status</label>
                        <select id="statusFilter" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="personal_lead">Personal Lead</option>
                            <option value="authorized">Authorized</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="disbursed">Disbursed</option>
                            <option value="future_lead">Future Lead</option>
                            <option value="login">Login</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-map-marker-alt"></i> State</label>
                        <select id="stateFilter" class="filter-select">
                            <option value="">All States</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-city"></i> District</label>
                        <select id="districtFilter" class="filter-select">
                            <option value="">All Districts</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-building"></i> City</label>
                        <select id="cityFilter" class="filter-select">
                            <option value="">All Cities</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-tags"></i> Lead Type</label>
                        <select id="leadTypeFilter" class="filter-select">
                            <option value="">All Types</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-rupee-sign"></i> Min Amount</label>
                        <input type="number" id="minAmountFilter" class="filter-input" placeholder="0">
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-rupee-sign"></i> Max Amount</label>
                        <input type="number" id="maxAmountFilter" class="filter-input" placeholder="1000000">
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-calendar"></i> Date From</label>
                        <input type="date" id="dateFromFilter" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-calendar"></i> Date To</label>
                        <input type="date" id="dateToFilter" class="filter-input">
                    </div>
                </div>
                <div class="filter-actions">
                    <div class="filter-stats">
                        <span id="filteredCount">{{ $forwardedLeads->count() }}</span> of {{ $forwardedLeads->count() }} leads shown
                    </div>
                    <div class="filter-buttons">
                        <button class="btn-reset" onclick="resetFilters()">
                            <i class="fas fa-undo"></i>
                            Reset
                        </button>
                        {{-- <button class="btn-apply" onclick="applyFilters()">
                            <i class="fas fa-check"></i>
                            Apply Filters
                        </button> --}}
                    </div>
                </div>
            </div>

            <div class="leads-content-grid">
                <div class="leads-table-container">
                    <div class="table-header">
                        <h2>All Leads</h2>
                        <div class="table-actions">
                            <div class="results-info">
                                Showing <span id="resultsCount">{{ $forwardedLeads->count() }}</span> leads
                            </div>
                            <button class="btn-refresh" onclick="refreshLeads()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table id="leadsTable" class="leads-table">
                            <thead>
                                <tr>
                                    <th>EXECUTIVE</th>
                                    <th>NAME</th>
                                    <th>LOAN AC NO.</th>
                                    <th>MOB</th>
                                    <th>COMPANY</th>

                                    <th>LOAN</th>
                                    <th>STATUS</th>
                                    <th>LEAD TYPE</th>
                                    <th>BANK NAME</th>
                                    <th>EXPECTED MONTH</th>

                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
                                @foreach($forwardedLeads as $lead)
                                    <tr class="lead-row" data-lead-id="{{ $lead['id'] }}"
    data-name="{{ strtolower($lead['name']) }}"
    data-email="{{ strtolower($lead['email']) }}"
    data-status="{{ $lead['status'] }}"
    data-state="{{ $lead['state'] ?? '' }}"
    data-district="{{ $lead['district'] ?? '' }}"
    data-city="{{ $lead['city'] ?? '' }}"
    data-lead-type="{{ $lead['lead_type'] ?? '' }}"
    data-amount="{{ $lead['amount'] }}"
    data-expected-month="{{ $lead['expected_month'] ?? '' }}"
    data-date="{{ $lead['created_at'] }}">
    <td>{{ $lead['employee_name'] ?? '-' }}</td>
    <td>{{ $lead['name'] }}</td>

    <td>{{ $lead['loan_account_number'] ?? '-' }}</td>
    <td>{{ $lead['phone'] }}</td>
    <td>{{ $lead['company'] ?? '-' }}</td>
    <td>{{ $lead['amount'] }}</td>

    <td><span class="status {{ $lead['status'] }}">{{ str_replace('_', ' ', ucfirst($lead['status'])) }}</span></td>
    <td>{{ $lead['lead_type'] ?? '-' }}</td>
    <td>{{ $lead['bank_name'] ?? '-' }}</td>
    <td>{{ $lead['expected_month'] ?? '-' }}</td>

</tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="empty-state" id="emptyState" style="display: {{ $forwardedLeads->isEmpty() ? 'flex' : 'none' }};">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>No Leads Found</h3>
                        <p>No leads available to display</p>
                    </div>
                    <!-- Pagination -->
                    {{-- <div class="pagination mt-3">
                        {{ $forwardedLeads->links() }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Detail Modal -->
    <div class="modal-overlay" id="leadDetailModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Lead Details</h2>
                <button class="modal-close" onclick="closeModal('leadDetailModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="lead-detail-grid">
                    <div class="lead-detail-left">
                        <div class="lead-avatar-large" id="modalLeadInitials"></div>
                        <div class="lead-basic-info">
                            <h3 id="modalLeadName"></h3>

                            <div class="lead-contact">
                                <div class="contact-item"><i class="fas fa-phone"></i><input type="text" id="modalLeadPhone" class="editable-field"  disabled/></div>
                                <div class="contact-item"><i class="fas fa-envelope"></i><input type="email" id="modalLeadEmail" class="editable-field"  disabled/></div>

                                <div class="contact-item"><i class="fas fa-university"></i><input type="text" id="modalLeadBank" class="editable-field" disabled></div>
                            </div>
                        </div>
                    </div>
                    <div class="lead-detail-right">
                        <div class="detail-section">
                            <h4>Lead Information</h4>
                            <div class="detail-grid">
                                <div class="detail-item"><label>Name</label><input type="text" id="modalName" class="editable-field" disabled></div>
                                <div class="detail-item"><label>Date of Birth</label><input type="date" id="modalLeadDob" class="editable-field" disabled></div>
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

                                <div class="detail-item"><label>Lead Amount</label><input type="number" id="modalLeadAmount" class="editable-field" disabled></div>
                                <div class="detail-item"><label>Amount in Words</label><span id="modalLeadAmountInWords"></span></div>
                                  <div class="detail-item"><label>COMPANY</label><input type="text" id="modalLeadCompany" class="editable-field" disabled></div>
                                <div class="detail-item"><label>Status</label><span id="modalLeadStatus" class="status"></span></div>
                                <div class="detail-item">
                                    <label>Expected Month</label>
                                    <select id="modalLeadExpectedMonth" class="editable-field" disabled>
                                        <option value="">Select Month</option>
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
                                <div class="detail-item">
    <label>LEAD TYPE</label>
    <span id="modalLeadTypeDisplay" class="lead-type-display"></span>
    <select id="modalLeadType" class="editable-field" style="display: none;" disabled>
        <option value="">SELECT LEAD TYPE</option>
        <option value="personal_loan">PERSONAL LOAN</option>
        <option value="business_loan">BUSINESS LOAN</option>
        <option value="home_loan">HOME LOAN</option>
    </select>
</div>
                                  <div class="detail-item"><label>LOAN AC No</label><input type="text" id="modalLeadAccountNumber" class="editable-field" disabled></div>
                                <div class="detail-item"><label>Turnover Amount</label><input type="number" id="modalLeadTurnoverAmount" class="editable-field" disabled></div>
                                <div class="detail-item"><label>Salary</label><input type="number" id="modalLeadSalary" class="editable-field" disabled></div>
                                {{-- <div class="detail-item"><label>Bank Name</label><input type="text" id="modalLeadBank" class="editable-field" disabled></div> --}}
                                <div class="detail-item"><label>Voice Recording</label><span id="modalLeadVoiceRecording"></span></div>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>Employee Information</h4>
                            <div class="detail-grid">
                                <div class="detail-item"><label>Created By</label><span id="modalLeadEmployeeName"></span></div>
                                <div class="detail-item"><label>Team Lead</label><span id="modalLeadTeamLeadName"></span></div>
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
                                <p id="modalLeadReason"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary" id="editLeadButton" onclick="enableLeadEdit()">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-success" id="saveLeadButton" style="display:none;" onclick="saveLeadChanges()">
                    <i class="fas fa-save"></i> Save
                </button>
                @if(auth()->user()->hasDesignation('admin'))
                   <button class="btn-primary" id="loginButton" onclick="showLoginModal(currentLeadId)">
                         <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    <button class="btn-authorize" id="authorizeButton" onclick="showAuthorizeModal(currentLeadId)">
                        <i class="fas fa-check-double"></i> Authorize
                    </button>
                    <button class="btn-success" id="approveButton" onclick="showApproveModal(currentLeadId)">
                        <i class="fas fa-check-circle"></i> Approve
                    </button>
                    <button class="btn-danger" id="rejectButton" onclick="showRejectModal(currentLeadId)">
                        <i class="fas fa-times-circle"></i> Reject
                    </button>
                    <button class="btn-success" id="disburseButton" onclick="showDisburseModal(currentLeadId)">
                        <i class="fas fa-rupee-sign"></i> Disburse
                    </button>
                    <button class="btn-future" id="futureLeadButton" onclick="showFutureLeadModal(currentLeadId)">
                        <i class="fas fa-clock"></i> Mark as Future Lead
                    </button>
                    <button class="btn-primary" id="forwardOperationsButton" onclick="forwardToOperations(currentLeadId)">
    <i class="fas fa-share"></i> Forward to Operations
</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Authorize Confirmation Modal -->
    <div class="modal-overlay" id="authorizeModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Authorize Lead</h2>
                <button class="modal-close" onclick="closeModal('authorizeModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="confirm-message">
                    <div class="confirm-icon authorize">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h3>Authorize This Lead?</h3>
                    <p>This action will change the lead status to "Authorized".</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('authorizeModal')">Cancel</button>
                <button class="btn-authorize" onclick="confirmAuthorize()">
                    <i class="fas fa-check"></i> Yes, Authorize
                </button>
            </div>
        </div>
    </div>
    <!-- Login Confirmation Modal -->
<div class="modal-overlay" id="loginModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Login Lead</h2>
            <button class="modal-close" onclick="closeModal('loginModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="confirm-message">
                <div class="confirm-icon login">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h3>Login This Lead?</h3>
                <p id="loginModalMessage"></p>
                <div class="form-group full-width" style="margin-top: 16px;">
                    <input type="text" id="loanAccountNumber" class="form-control" placeholder="Enter loan account number...">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('loginModal')">Cancel</button>
            <button class="btn-primary" onclick="confirmLogin()">
                <i class="fas fa-check"></i> Yes, Login
            </button>
        </div>
    </div>
</div>

   <!-- Approve Confirmation Modal -->
<div class="modal-overlay" id="approveModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Approve Lead</h2>
            <button class="modal-close" onclick="closeModal('approveModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="confirm-message">
                <div class="confirm-icon approve">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Approve This Lead?</h3>
                <p id="approveModalMessage"></p>
                <div class="form-group full-width" style="margin-top: 16px;">
                    <input type="text" id="loanAccountNumber1" class="form-control" placeholder="Enter loan account number...">
                </div>
                <p>Once approved, no further documents can be uploaded to this lead. This action will change the lead status to "Approved".</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('approveModal')">Cancel</button>
            <button class="btn-success" onclick="confirmApprove()">
                <i class="fas fa-check"></i> Yes, Approve
            </button>
        </div>
    </div>
</div>

<!-- Disburse Confirmation Modal -->
<div class="modal-overlay" id="disburseModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Disburse Lead</h2>
            <button class="modal-close" onclick="closeModal('disburseModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="confirm-message">
                <div class="confirm-icon disburse">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <h3>Disburse This Lead?</h3>
                <p id="disburseModalMessage"></p>
                <div class="form-group full-width" style="margin-top: 16px;">
                    <input type="text" id="loanAccountNumber2" class="form-control" placeholder="Enter loan account number...">
                </div>
                <p>This action will mark the lead as "Disbursed" and complete the lead process. This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('disburseModal')">Cancel</button>
            <button class="btn-success" onclick="confirmDisburse()">
                <i class="fas fa-check"></i> Yes, Disburse
            </button>
        </div>
    </div>
</div>

    <!-- Reject Confirmation Modal -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Reject Lead</h2>
                <button class="modal-close" onclick="closeModal('rejectModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="confirm-message">
                    <div class="confirm-icon reject">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3>Reject This Lead?</h3>
                    <p>Please provide a reason for rejecting this lead:</p>
                    <div class="form-group full-width" style="margin-top: 16px;">
                        <textarea id="rejectionReason" class="form-control" placeholder="Enter rejection reason..." rows="4" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn-danger" onclick="confirmReject()">
                    <i class="fas fa-times"></i> Yes, Reject
                </button>
            </div>
        </div>
    </div>



   <!-- Future Lead Confirmation Modal -->
<div class="modal-overlay" id="futureLeadModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Mark as Future Lead</h2>
            <button class="modal-close" onclick="closeModal('futureLeadModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="confirm-message">
                <div class="confirm-icon future_lead">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Mark as Future Lead?</h3>
                <p>This action will mark the lead as "Future Lead" and complete the lead process. This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('futureLeadModal')">Cancel</button>
            <button class="btn-future" onclick="confirmFutureLead()">
                <i class="fas fa-check"></i> Yes, Mark as Future Lead
            </button>
        </div>
    </div>
</div>


   <!-- Forward to Operations Modal -->
<div class="modal-overlay" id="forwardOperationsModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Forward to Operations</h2>
            <button class="modal-close" onclick="closeModal('forwardOperationsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="confirm-message">
                <div class="confirm-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-share"></i>
                </div>
                <h3>Forward This Lead?</h3>
                <p>This action will forward the lead to the operations team for processing.</p>
                <div class="form-group full-width" style="margin-top: 16px;">
                    <label>Remarks (optional)</label>
                    <textarea id="operation-remarks" class="form-control" rows="3" placeholder="Enter any remarks for the operations team..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('forwardOperationsModal')">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn-primary" onclick="submitForwardToOperations()">
                <i class="fas fa-check"></i> Confirm Forward
            </button>
        </div>
    </div>
</div>

    <!-- Add Document Modal -->
    <div class="modal-overlay" id="addDocumentModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Add New Document</h2>
                <button class="modal-close" onclick="closeModal('addDocumentModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="lead_id" id="documentLeadId">
                    <div class="form-group full-width">
                        <label for="documentName">Document Name <span class="required">*</span></label>
                        <input type="text" id="documentName" name="name" class="form-control" placeholder="e.g., ID Proof" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="documentType">Document Type</label>
                        <select id="documentType" name="type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="id_proof">ID Proof</option>
                            <option value="address_proof">Address Proof</option>
                            <option value="financial">Financial Document</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="documentDescription">Description</label>
                        <textarea id="documentDescription" name="description" class="form-control" placeholder="Enter description"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="documentFile">Upload File <span class="required">*</span></label>
                        <input type="file" id="documentFile" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal('addDocumentModal')">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-upload"></i> Save & Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <script>

              // Forward to operations
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

    fetch(`/admin/leads/${currentLeadId}/forward-to-operations`, {
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
        closeModal('forwardOperationsModal');
        closeModal('leadDetailModal');
        refreshLeads();
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

    // Handle Indian number system (Crore, Lakh, Thousand)
    while (number > 0) {
        if (thousandIndex === 0) {
            // Handle thousands (first 3 digits)
            if (number % 1000 > 0) {
                result = convertLessThanThousand(number % 1000) + (result ? ' ' + thousands[thousandIndex] + ' ' + result : '');
            }
            number = Math.floor(number / 1000);
        } else {
            // Handle lakhs, crores, etc. (2 digits at a time)
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
let allLeads = [];
let filteredLeads = [];

document.addEventListener('DOMContentLoaded', function () {
    allLeads = Array.from(document.querySelectorAll('.lead-row')).map(row => ({
        element: row,
        id: row.dataset.leadId,
        name: row.dataset.name,
        email: row.dataset.email,
        status: row.dataset.status,
        state: row.dataset.state,
        district: row.dataset.district,
        city: row.dataset.city,
        leadType: row.dataset.leadType,
        amount: parseFloat(row.dataset.amount) || 0,
        expectedMonth: row.dataset.expectedMonth,
        date: row.dataset.date
    }));

    filteredLeads = [...allLeads];
    populateFilterOptions();

    document.querySelectorAll('.lead-row').forEach(row => {
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
        const maxSize = 9 * 1024 * 1024; // 5MB
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (file && file.size > maxSize) {
            showNotification('File size exceeds 2MB limit.', 'error');
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
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
            }
            return res.json();
        })
        .then(data => {
            showNotification(data.message || 'Document added successfully.', 'success');
            closeModal('addDocumentModal');
            viewLeadDetails(leadId);
        })
        .catch(error => {
            console.error("Error adding document:", error);
            showNotification(`Failed to add document: ${error.message}`, 'error');
        })
        .finally(() => showLoading(false));
    });

    document.getElementById('searchFilter').addEventListener('input', debounce(applyFilters, 300));
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('stateFilter').addEventListener('change', applyFilters);
    document.getElementById('districtFilter').addEventListener('change', applyFilters);
    document.getElementById('cityFilter').addEventListener('change', applyFilters);
    document.getElementById('leadTypeFilter').addEventListener('change', applyFilters);
    document.getElementById('minAmountFilter').addEventListener('input', debounce(applyFilters, 300));
    document.getElementById('maxAmountFilter').addEventListener('input', debounce(applyFilters, 300));
    document.getElementById('dateFromFilter').addEventListener('change', applyFilters);
    document.getElementById('dateToFilter').addEventListener('change', applyFilters);

    fetchFilterOptions();


      // Add event listener for modalLeadAmount to update modalLeadAmountInWords dynamically
        const leadAmountInput = document.getElementById('modalLeadAmount');
        leadAmountInput.addEventListener('input', function () {
            if (!this.disabled) { // Only update if the field is editable
                const amount = parseInt(this.value);
                document.getElementById('modalLeadAmountInWords').textContent = isNaN(amount) || amount <= 0 ? 'N/A' : numberToWords(amount);
            }
        });

        // Updated event listener for modalName to enforce uppercase input
        const nameInput = document.getElementById('modalName');
        nameInput.addEventListener('input', function () {
            if (!this.disabled) { // Only update if the field is editable
                // Force input to uppercase in the input field
                this.value = this.value.toUpperCase();
                const name = this.value.trim();
                // Convert to proper case for display (e.g., "MANISH KUMAR" -> "Manish Kumar")
                const properName = name ? name.toLowerCase().replace(/(^|\s)\w/g, char => char.toUpperCase()) : 'N/A';
                document.getElementById('modalLeadName').textContent = properName;
                document.getElementById('modalLeadInitials').textContent = properName && properName !== 'N/A' ? properName.charAt(0).toUpperCase() : '';
            }
        });
});

function fetchFilterOptions() {
    fetch('/admin/filters')
        .then(res => res.json())
        .then(data => {
            populateSelect('stateFilter', data.states || []);
            populateSelect('districtFilter', data.districts || []);
            populateSelect('cityFilter', data.cities || []);
            populateSelect('leadTypeFilter', data.leadTypes || []);
        })
        .catch(error => console.error('Error fetching filter options:', error));
}

function populateFilterOptions() {
    fetchFilterOptions();
}

function populateSelect(selectId, options) {
    const select = document.getElementById(selectId);
    const currentValue = select.value;
    const firstOption = select.options[0];
    select.innerHTML = '';
    select.appendChild(firstOption);

    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        select.appendChild(optionElement);
    });

    select.value = currentValue;
}

function toggleFilters() {
    const filters = document.getElementById('advancedFilters');
    const toggle = document.querySelector('.filter-toggle');
    const chevron = document.getElementById('filterChevron');

    if (!filters || !toggle || !chevron) {
        console.error('Filter elements not found:', { filters, toggle, chevron });
        return;
    }

    if (filters.classList.contains('active')) {
        filters.classList.remove('active');
        toggle.classList.remove('active');
        chevron.style.transform = 'rotate(0deg)';
        localStorage.setItem('filtersActive', 'false');
    } else {
        filters.classList.add('active');
        toggle.classList.add('active');
        chevron.style.transform = 'rotate(180deg)';
        localStorage.setItem('filtersActive', 'true');
    }
}

function applyFilters() {
    const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const stateFilter = document.getElementById('stateFilter').value;
    const districtFilter = document.getElementById('districtFilter').value;
    const cityFilter = document.getElementById('cityFilter').value;
    const leadTypeFilter = document.getElementById('leadTypeFilter').value;
    const minAmount = parseFloat(document.getElementById('minAmountFilter').value) || 0;
    const maxAmount = parseFloat(document.getElementById('maxAmountFilter').value) || Infinity;
    const dateFrom = document.getElementById('dateFromFilter').value;
    const dateTo = document.getElementById('dateToFilter').value;

    filteredLeads = allLeads.filter(lead => {
        if (searchTerm && !lead.name.includes(searchTerm) && !lead.email.includes(searchTerm)) return false;
        if (statusFilter && lead.status !== statusFilter) return false;
        if (stateFilter && lead.state !== stateFilter) return false;
        if (districtFilter && lead.district !== districtFilter) return false;
        if (cityFilter && lead.city !== cityFilter) return false;
        if (leadTypeFilter && lead.leadType !== leadTypeFilter) return false;
        if (lead.amount < minAmount || lead.amount > maxAmount) return false;
        if (dateFrom || dateTo) {
            const leadDate = new Date(lead.date);
            if (dateFrom && leadDate < new Date(dateFrom)) return false;
            if (dateTo && leadDate > new Date(dateTo + 'T23:59:59')) return false;
        }
        return true;
    });

    allLeads.forEach(lead => {
        const isVisible = filteredLeads.includes(lead);
        lead.element.style.display = isVisible ? '' : 'none';
    });

    document.getElementById('resultsCount').textContent = filteredLeads.length;
    document.getElementById('filteredCount').textContent = filteredLeads.length;

    const emptyState = document.getElementById('emptyState');
    if (filteredLeads.length === 0) {
        emptyState.style.display = 'flex';
    } else {
        emptyState.style.display = 'none';
    }
}

function resetFilters() {
    document.getElementById('searchFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('stateFilter').value = '';
    document.getElementById('districtFilter').value = '';
    document.getElementById('cityFilter').value = '';
    document.getElementById('leadTypeFilter').value = '';
    document.getElementById('minAmountFilter').value = '';
    document.getElementById('maxAmountFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    applyFilters();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function strReplaceUnderscore(str) {
    return str ? str.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : '';
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = show ? 'flex' : 'none';
    }
}

function setButtonStates(lead) {
    const isDisbursedOrFuture = ['disbursed', 'future_lead'].includes(lead.status);

    document.getElementById('loginButton').disabled = isDisbursedOrFuture;
    document.getElementById('authorizeButton').disabled = isDisbursedOrFuture;
    document.getElementById('approveButton').disabled = isDisbursedOrFuture;
    document.getElementById('rejectButton').disabled = isDisbursedOrFuture;
    document.getElementById('disburseButton').disabled = isDisbursedOrFuture;
    document.getElementById('futureLeadButton').disabled = isDisbursedOrFuture;
    document.getElementById('forwardOperationsButton').disabled = isDisbursedOrFuture;
    document.getElementById('addDocumentButton').disabled = isDisbursedOrFuture;
}

window.viewLeadDetails = function (leadId) {
    window.currentLeadId = leadId;
    document.getElementById('documentLeadId').value = leadId;
    const modal = document.getElementById("leadDetailModal");
    const docList = document.getElementById("documentList");

    if (!modal || !docList) {
        console.error("Modal or document list element not found.");
        showNotification("Failed to open lead details.", 'error');
        return;
    }

    modal.classList.add("active");
    docList.innerHTML = "<p>Loading documents...</p>";
    showLoading(true);

    fetch(`/admin/leads/${leadId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(text => { throw new Error(`HTTP ${res.status}: ${text}`); });
        }
        const contentType = res.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return res.json();
        } else {
            return res.text().then(text => { throw new Error('Response is not JSON: ' + text.substring(0, 50)); });
        }
    })
    .then(data => {
        const lead = data.lead;
        document.getElementById("modalLeadName").textContent = lead.name ?? 'N/A';
        document.getElementById('modalName').value = lead.name ? lead.name.toUpperCase() : 'N/A';
        document.getElementById("modalLeadInitials").textContent = lead.name ? lead.name.charAt(0).toUpperCase() : '';
        document.getElementById("modalLeadCompany").value = lead.company_name ?? 'N/A';
        document.getElementById("modalLeadPhone").value = lead.phone ?? 'N/A';
        document.getElementById("modalLeadEmail").value = lead.email ?? '';
        document.getElementById("modalLeadDob").value = lead.dob ?? '';
        document.getElementById("modalLeadState").value = lead.state ?? '';
        document.getElementById("modalLeadDistrict").value = lead.district ?? '';
        document.getElementById("modalLeadCity").value = lead.city ?? '';


        document.getElementById("modalLeadAmount").value = lead.lead_amount ? `${lead.lead_amount}` : '';
          document.getElementById("modalLeadAmountInWords").textContent = lead.lead_amount ? numberToWords(parseInt(lead.lead_amount)) : 'N/A';
        document.getElementById("modalLeadStatus").textContent = lead.status ? strReplaceUnderscore(lead.status) : 'N/A';
        document.getElementById("modalLeadStatus").className = `status ${lead.status}`;
        document.getElementById("modalLeadExpectedMonth").value = lead.expected_month ?? '';
        const leadTypeDisplay = lead.lead_type ? lead.lead_type.toUpperCase() : 'N/A';
document.getElementById('modalLeadTypeDisplay').textContent = leadTypeDisplay;
document.getElementById('modalLeadType').value = lead.lead_type || '';
        document.getElementById('modalLeadAccountNumber').value = lead.loan_account_number ?? '';
        document.getElementById("modalLeadTurnoverAmount").value = lead.turnover_amount ? `${lead.turnover_amount}` : '';
        document.getElementById("modalLeadSalary").value = lead.salary ? `${lead.salary}` : '';
        document.getElementById("modalLeadBank").value = lead.bank_name ? `${lead.bank_name}` : '';

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

        document.getElementById("modalLeadEmployeeName").textContent = lead.employee_name ?? 'N/A';
        document.getElementById("modalLeadTeamLeadName").textContent = lead.team_lead_name ?? 'N/A';

        const rejectionReasonSection = document.getElementById("rejectionReasonSection");
        const rejectionReason = document.getElementById("modalLeadReason");
        rejectionReason.textContent = lead.rejection_reason ?? 'N/A';
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
                            `<a href="${doc.filepath}" target="_blank"><i class="fas fa-eye"></i> View File</a>` :
                            `<form id="uploadForm_${doc.document_id}" method="POST" enctype="multipart/form-data" action="/admin/leads/${leadId}/documents/${doc.document_id}/upload">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                                <button type="submit" class="btn-primary"><i class="fas fa-upload"></i> Upload</button>
                            </form>`}
                    </div>
                    ${doc.filepath && !['disbursed'].includes(lead.status) ?
                        `<button class="btn-danger" onclick="deleteDocument(${leadId}, ${doc.document_id})"><i class="fas fa-trash"></i> Delete</button>` :
                        ''}
                `;
                docList.appendChild(docItem);
            });
        } else {
            docList.innerHTML = '<p>No documents uploaded.</p>';
        }

        document.querySelectorAll('form[id^="uploadForm_"]').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const documentId = this.id.split('_')[1];
                showLoading(true);

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(err => {
                            throw new Error(err.message || `HTTP ${res.status}: ${err.error || 'Unknown error'}`);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    showNotification('Document uploaded successfully.', 'success');
                    viewLeadDetails(window.currentLeadId); // Refresh to update the UI
                })
                .catch(error => {
                    console.error("Error uploading document:", error);
                    showNotification(`Failed to upload document: ${error.message}`, 'error');
                })
                .finally(() => showLoading(false));
            });
        });
    })
    .catch(error => {
        console.error("Error fetching lead details:", error);
        showNotification(`Failed to load lead details: ${error.message}`, 'error');
    })
    .finally(() => showLoading(false));
};




 function enableLeadEdit() {
    const currentStatus = document.getElementById('modalLeadStatus').textContent.toLowerCase().replace(' ', '_');
    const isReadOnlyStatus = ['disbursed', 'rejected', 'future_lead'].includes(currentStatus);

    const editButton = document.getElementById('editLeadButton');
    const saveButton = document.getElementById('saveLeadButton');
    const fields = document.querySelectorAll('.editable-field');

    fields.forEach(field => {
        // Skip enabling the expected_month field if status is read-only
        if (field.id === 'modalLeadExpectedMonth' && isReadOnlyStatus) {
            field.disabled = true;
        } else {
            field.disabled = false;
        }
    });

    editButton.style.display = 'none';
    saveButton.style.display = 'inline-flex';
    document.getElementById('modalLeadTypeDisplay').style.display = 'none';
    document.getElementById('modalLeadType').style.display = 'block';

    // Show dropdowns and hide text inputs
    document.getElementById('modalLeadState').style.display = 'none';
    document.getElementById('modalLeadStateDropdown').style.display = 'block';
    document.getElementById('modalLeadDistrict').style.display = 'none';
    document.getElementById('modalLeadDistrictDropdown').style.display = 'block';
    document.getElementById('modalLeadCity').style.display = 'none';
    document.getElementById('modalLeadCityDropdown').style.display = 'block';

    // Load states if not already loaded
    if (document.getElementById('modalLeadStateDropdown').options.length <= 1) {
        loadStates();
    }
}



function disableLeadEdit() {
    const fields = document.querySelectorAll('.editable-field');
    fields.forEach(field => field.disabled = true);
    document.getElementById('editLeadButton').style.display = 'inline-flex';
    document.getElementById('saveLeadButton').style.display = 'none';
    document.getElementById('modalLeadTypeDisplay').style.display = 'inline-block';
    document.getElementById('modalLeadType').style.display = 'none';


     // Hide dropdowns and show text inputs
    document.getElementById('modalLeadState').style.display = 'block';
    document.getElementById('modalLeadStateDropdown').style.display = 'none';
    document.getElementById('modalLeadDistrict').style.display = 'block';
    document.getElementById('modalLeadDistrictDropdown').style.display = 'none';
    document.getElementById('modalLeadCity').style.display = 'block';
    document.getElementById('modalLeadCityDropdown').style.display = 'none';
}


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



function saveLeadChanges() {
    const leadId = window.currentLeadId;
    const data = {
        name:document.getElementById('modalName').value,
        phone: document.getElementById('modalLeadPhone').value,
        email: document.getElementById('modalLeadEmail').value,
        dob: document.getElementById('modalLeadDob').value,
        state: document.getElementById('modalLeadStateDropdown').options[document.getElementById('modalLeadStateDropdown').selectedIndex].text,
        district: document.getElementById('modalLeadDistrictDropdown').options[document.getElementById('modalLeadDistrictDropdown').selectedIndex].text,
        city: document.getElementById('modalLeadCityDropdown').options[document.getElementById('modalLeadCityDropdown').selectedIndex].text,
        company_name: document.getElementById('modalLeadCompany').value,
        lead_amount: document.getElementById('modalLeadAmount').value,
        expected_month: document.getElementById('modalLeadExpectedMonth').value,
        lead_type: document.getElementById('modalLeadType').value,
        loan_account_number: document.getElementById('modalLeadAccountNumber').value,
        turnover_amount: document.getElementById('modalLeadTurnoverAmount').value,
        salary: document.getElementById('modalLeadSalary').value,
        bank_name: document.getElementById('modalLeadBank').value,
        _token: document.querySelector('meta[name="csrf-token"]').content
    };

    showLoading(true);

    fetch(`/admin/leads/${leadId}/update`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => {
                let errorMessage = 'An unexpected error occurred';
                if (err.message && typeof err.message === 'object') {
                    // Prioritize phone error if present, otherwise join all errors
                    if (err.message.phone) {
                        errorMessage = err.message.phone.join(', ');
                    } else {
                        errorMessage = Object.values(err.message).flat().join(', ');
                    }
                } else if (err.message) {
                    errorMessage = err.message; // For non-validation errors (e.g., 500)
                } else {
                    errorMessage = `HTTP error ${res.status}`;
                }
                throw new Error(err.error || errorMessage);
            });
        }
        return res.json();
    })
    .then(data => {
        showNotification(data.message || 'Lead updated successfully.', 'success');
           // Update read-only fields so they show new values
document.getElementById('modalLeadState').value =
    document.getElementById('modalLeadStateDropdown').options[
        document.getElementById('modalLeadStateDropdown').selectedIndex
    ].text;

document.getElementById('modalLeadDistrict').value =
    document.getElementById('modalLeadDistrictDropdown').options[
        document.getElementById('modalLeadDistrictDropdown').selectedIndex
    ].text;

document.getElementById('modalLeadCity').value =
    document.getElementById('modalLeadCityDropdown').options[
        document.getElementById('modalLeadCityDropdown').selectedIndex
    ].text;
        disableLeadEdit();
        const leadType = document.getElementById('modalLeadType').value;
        document.getElementById('modalLeadTypeDisplay').textContent = leadType ? leadType.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';

    })
    .catch(error => {
        console.error("Error updating lead:", error);
        const errorMessage = error.message || 'An unexpected error occurred';
        showNotification(`Failed to update lead: ${errorMessage}`, 'error');
    })
    .finally(() => showLoading(false));
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
        }


    }
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

        function confirmLogin() {
    const loanAccountNumber = document.getElementById('loanAccountNumber').value;
    const existingAccountNumber = document.getElementById('modalLeadAccountNumber').value;

    if (!existingAccountNumber && !loanAccountNumber) {
        showNotification('Please provide a loan account number.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'login', null, null, loanAccountNumber || existingAccountNumber);
}

 function showDisburseModal(leadId) {
    window.currentLeadId = leadId;
    const loanAccountNumber = document.getElementById('modalLeadAccountNumber').value;
    const message = loanAccountNumber ?
        "This lead already has a loan account number. You can update it below if needed:" :
        "Please provide the loan account number:";

    document.getElementById('disburseModalMessage').textContent = message;
    document.getElementById('loanAccountNumber2').value = loanAccountNumber || '';
    document.getElementById('loanAccountNumber2').required = !loanAccountNumber;
    document.getElementById('disburseModal').classList.add('active');
}

        function confirmDisburse() {
    const loanAccountNumber = document.getElementById('loanAccountNumber2').value;
    const existingAccountNumber = document.getElementById('modalLeadAccountNumber').value;

    if (!existingAccountNumber && !loanAccountNumber) {
        showNotification('Please provide a loan account number.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'disbursed', null, null, loanAccountNumber || existingAccountNumber);
}

        function showApproveModal(leadId) {
    window.currentLeadId = leadId;
    const loanAccountNumber = document.getElementById('modalLeadAccountNumber').value;
    const message = loanAccountNumber ?
        "This lead already has a loan account number. You can update it below if needed:" :
        "Please provide the loan account number:";

    document.getElementById('approveModalMessage').textContent = message;
    document.getElementById('loanAccountNumber1').value = loanAccountNumber || '';
    document.getElementById('loanAccountNumber1').required = !loanAccountNumber;
    document.getElementById('approveModal').classList.add('active');
}

        function confirmApprove() {
    const loanAccountNumber = document.getElementById('loanAccountNumber1').value;
    const existingAccountNumber = document.getElementById('modalLeadAccountNumber').value;

    if (!existingAccountNumber && !loanAccountNumber) {
        showNotification('Please provide a loan account number.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'approved', null, null, loanAccountNumber || existingAccountNumber);
}




function showRejectModal(leadId) {
    window.currentLeadId = leadId;
    document.getElementById('rejectModal').classList.add('active');
}

function confirmReject() {
    const reason = document.getElementById('rejectionReason').value;
    if (!reason) {
        showNotification('Please provide a rejection reason.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'rejected', reason);
}



function showFutureLeadModal(leadId) {
        window.currentLeadId = leadId;
        document.getElementById('futureLeadModal').classList.add('active');
    }

    function confirmFutureLead() {
        updateLeadStatus(window.currentLeadId, 'future_lead');
    }

    function updateLeadStatus(leadId, status, reason = null, expectedMonth = null, loanAccountNumber = null) {
        const data = {
            status: status,
            reason: reason,
            loan_account_number: loanAccountNumber, // Include loan_account_number
            _token: document.querySelector('meta[name="csrf-token"]').content
        };

        showLoading(true);
        fetch(`/admin/leads/${leadId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
            }
            return res.json();
        })
        .then(data => {
            showNotification(data.message || `Lead status updated to ${status}.`, 'success');
            let modalId;
            switch (status) {
                case 'authorized':
                    modalId = 'authorizeModal';
                    break;
                case 'approved':
                    modalId = 'approveModal';
                    break;
                case 'rejected':
                    modalId = 'rejectModal';
                    break;
                case 'disbursed':
                    modalId = 'disburseModal';
                    break;
                case 'future_lead':
                    modalId = 'futureLeadModal';
                    break;
                case 'login':
                    modalId = 'loginModal';
                    break;
            }
            if (modalId) closeModal(modalId);
            viewLeadDetails(leadId);
            refreshLeads();
        })
        .catch(error => {
            console.error(`Error updating lead status to ${status}:`, error);
            showNotification(`Failed to update lead status: ${error.message}`, 'error');
        })
        .finally(() => showLoading(false));
    }


function refreshLeads() {
    showLoading(true);
    fetch('/admin/leads', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(text => { throw new Error(`HTTP ${res.status}: ${text}`); });
        }
        return res.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.querySelector('#leadsTableBody');
        const newEmptyState = doc.querySelector('#emptyState');
        const newPagination = doc.querySelector('.pagination');
        if (newTableBody && newEmptyState) {
            document.getElementById('leadsTableBody').innerHTML = newTableBody.innerHTML;
            document.getElementById('emptyState').style.display = newEmptyState.style.display;
            if (newPagination && document.querySelector('.pagination')) {
                document.querySelector('.pagination').innerHTML = newPagination.innerHTML;
            }
            allLeads = Array.from(document.querySelectorAll('.lead-row')).map(row => ({
                element: row,
                id: row.dataset.leadId,
                name: row.dataset.name,
                email: row.dataset.email,
                status: row.dataset.status,
                state: row.dataset.state,
                district: row.dataset.district,
                city: row.dataset.city,
                leadType: row.dataset.leadType,
                amount: parseFloat(row.dataset.amount) || 0,
                expectedMonth: row.dataset.expectedMonth,
                date: row.dataset.date
            }));
            filteredLeads = [...allLeads];
            applyFilters();
            populateFilterOptions();
            document.querySelectorAll('.lead-row').forEach(row => {
                row.addEventListener('click', function (e) {
                    if (e.target.closest('button')) return;
                    const leadId = this.dataset.leadId;
                    if (leadId) viewLeadDetails(leadId);
                });
            });
        }
    })
    .catch(error => {
        console.error('Error refreshing leads:', error);
        showNotification(`Failed to refresh leads: ${error.message}`, 'error');
    })
    .finally(() => showLoading(false));
}
    </script>
</body>
</html>
