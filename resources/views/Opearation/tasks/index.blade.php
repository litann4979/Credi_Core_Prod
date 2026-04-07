<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Lead Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            animation: fadeInDown 0.6s ease-out;
        }

        .header-content {
            flex: 1;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            font-size: 16px;
            color: #6b7280;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn-primary {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        /* Task Stats */
        .task-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
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
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 600;
        }

        /* Main Content Layout */
        .content-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        /* Task Form */
        .task-form-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 24px;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #1f2937;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control.textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .priority-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .priority-option {
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-option.low {
            border-color: #10b981;
            color: #10b981;
        }

        .priority-option.medium {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .priority-option.high {
            border-color: #ef4444;
            color: #ef4444;
        }

        .priority-option.urgent {
            border-color: #8b5cf6;
            color: #8b5cf6;
        }

        .priority-option.active {
            background: currentColor;
            color: white;
        }

        .employee-selection {
            margin-bottom: 20px;
        }

        .selection-toggle {
            display: flex;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 16px;
        }

        .toggle-btn {
            flex: 1;
            padding: 8px 16px;
            border: none;
            background: transparent;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .toggle-btn.active {
            background: white;
            color: #3b82f6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .employee-list {
            display: none;
            max-height: 200px;
            overflow-y: auto;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: #f9fafb;
        }

        .employee-list.active {
            display: block;
        }

        .employee-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .employee-item:hover {
            background: #f3f4f6;
        }

        .employee-item:last-child {
            border-bottom: none;
        }

        .employee-checkbox {
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
        }

        .employee-checkbox.checked {
            background: #3b82f6;
            border-color: #3b82f6;
        }

        .employee-checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 10px;
            font-weight: bold;
        }

        .employee-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }

        .employee-info {
            flex: 1;
        }

        .employee-name {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .employee-role {
            font-size: 12px;
            color: #6b7280;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 16px;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            background: #f9fafb;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-label:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            color: #3b82f6;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-secondary {
            flex: 1;
            padding: 12px 20px;
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-primary.full {
            flex: 2;
        }

        /* Tasks List */
        .tasks-section {
            background: white;
            border-radius: 20px;
            padding: 32px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
        }

        .view-controls {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-box {
            position: relative;
        }

        .search-input {
            width: 250px;
            padding: 8px 12px 8px 36px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: #f9fafb;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .filter-dropdown {
            position: relative;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
        }

        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .task-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .task-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        }

        .task-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            background: white;
        }

        .task-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .task-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .task-id {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .task-priority {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .priority.low {
            background: #dcfce7;
            color: #166534;
        }

        .priority.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .priority.high {
            background: #fee2e2;
            color: #991b1b;
        }

        .priority.urgent {
            background: #fdf2f8;
            color: #be185d;
        }

        .task-description {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .task-assignees {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .assignee-avatars {
            display: flex;
            margin-left: -8px;
        }

        .assignee-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 10px;
            border: 2px solid white;
            margin-left: -8px;
        }

        .more-assignees {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 10px;
            border: 2px solid white;
            margin-left: -8px;
        }

        .task-due-date {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .task-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .task-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status.in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .status.review {
            background: #fdf4ff;
            color: #a21caf;
        }

        .status.completed {
            background: #dcfce7;
            color: #166534;
        }

        .task-progress {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-bar {
            width: 60px;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
        }

        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 64px 24px;
            text-align: center;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #9ca3af;
            margin-bottom: 24px;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 16px;
            color: #6b7280;
        }

        /* Success Modal */
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
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-overlay.active .modal-container {
            transform: scale(1);
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 24px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .modal-message {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

     #viewTaskModal {
    display: none;
    position: fixed;
    z-index: 99999;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
}

    #viewTaskModal.active {
        display: flex;
    }

    #viewTaskModal .modal-content {
        background: white;
        padding: 24px;
        border-radius: 8px;
        width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 10000;
    }

        /* Animations */
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .content-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .task-form-card {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 16px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .task-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .priority-options {
                grid-template-columns: 1fr;
            }

            .tasks-grid {
                grid-template-columns: 1fr;
            }

            .search-input {
                width: 200px;
            }

            .view-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }

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
    color: #374151;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.pagination .page-link:hover {
    background: #eff6ff;
    color: #3b82f6;
    border-color: #bfdbfe;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border-color: #1d4ed8;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background: #f3f4f6;
    border-color: #e5e7eb;
    cursor: not-allowed;
    box-shadow: none;
}

.pagination .page-link:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Specific styling for Previous and Next buttons */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    padding: 0.5rem 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pagination .page-item:first-child .page-link::before {
    content: "←";
    margin-right: 0.25rem;
}

.pagination .page-item:last-child .page-link::after {
    content: "→";
    margin-left: 0.25rem;
}

.pagination .page-item.disabled:first-child .page-link::before,
.pagination .page-item.disabled:last-child .page-link::after {
    opacity: 0.5;
}

.multi-select-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.multi-select-list::-webkit-scrollbar {
    width: 6px;
}

.multi-select-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.multi-select-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.multi-select-list::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

.employee-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s ease;
}

.employee-item:hover {
    background: #f3f4f6;
}

.employee-item:last-child {
    border-bottom: none;
}


    </style>
</head>
<body>
    @include('Opearation.Components.sidebar')

    <div class="main-content">
        @include('Opearation.Components.header', ['title' => 'Task Management', 'subtitle' => 'Create and manage team tasks'])

        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Task Management</h1>
                    {{-- <p class="page-subtitle">Create, assign, and track team tasks efficiently</p> --}}
                </div>
                {{-- <div class="header-actions">
                    <button class="btn-primary" onclick="resetForm()">
                        <i class="fas fa-plus"></i>
                        New Task
                    </button>
                </div> --}}
            </div>

            <!-- Task Statistics -->
            <div class="task-stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="totalTasks"></div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="pendingTasks">0</div>
                    <div class="stat-label">Pending Tasks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="inProgressTasks">0</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="completedTasks">0</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Main Content Layout -->
            <div class="content-layout">
                <!-- Task Form -->
                <div class="task-form-card">
                    <div class="form-header">
                        <h2 class="form-title" id="formTitle">Create New Task</h2>
                        <p class="form-subtitle">Fill in the details to create a new task</p>
                    </div>

                    <form id="taskForm" onsubmit="submitTask(event)">
                        <input type="hidden" id="taskId" value="">

                        <div class="form-group">
                            <label class="form-label">Task Title *</label>
                            <input type="text" id="taskTitle" class="form-control" placeholder="Enter task title" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description *</label>
                            <textarea id="taskDescription" class="form-control textarea" placeholder="Describe the task in detail" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Assigned Date *</label>
                                <input type="date" id="assignedDate" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Due Date *</label>
                                <input type="date" id="dueDate" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Priority *</label>
                            <div class="priority-options">
                                <div class="priority-option low" onclick="selectPriority('low')">Low</div>
                                <div class="priority-option medium" onclick="selectPriority('medium')">Medium</div>
                                <div class="priority-option high" onclick="selectPriority('high')">High</div>
                                <div class="priority-option urgent" onclick="selectPriority('urgent')">Urgent</div>
                            </div>
                           <input type="hidden" id="taskPriority" name="priority" value="">

                        </div>

                        <div class="form-group">
                            <label class="form-label">Initial Progress (%)</label>
                            <input type="range" id="taskProgress" class="form-control" min="0" max="100" value="0" oninput="updateProgressDisplay()">
                            <div style="text-align: center; margin-top: 8px;">
                                <span id="progressDisplay" style="font-weight: 600; color: #3b82f6;">0%</span>
                            </div>
                        </div>

                        <div class="employee-selection">
    <label class="form-label">Assign To *</label>

    <div class="selection-toggle">
        <button type="button" class="toggle-btn active" id="individualBtn" onclick="toggleSelection('individual')">Individual Employees</button>
        <button type="button" class="toggle-btn" id="teamleadBtn" onclick="toggleSelection('individual_teamlead')">Individual Teamleads</button>
        <button type="button" class="toggle-btn" id="allBtn" onclick="toggleSelection('all')">All Members</button>
    </div>

  <!-- Inside the employee-selection div, modify these sections -->
<div class="selection-section" id="employeeListSection">
    <label class="form-label">Select Employees</label>
    <div class="search-box" style="margin-bottom: 12px;">
        <input type="text" class="search-input" placeholder="Search employees..."
               oninput="filterEmployees(this.value)">
        <i class="fas fa-search search-icon"></i>
    </div>
    <div id="employeeList" class="multi-select-list" style="max-height: 200px; overflow-y: auto;"></div>
</div>

<div class="selection-section" id="teamleadListSection" style="display: none;">
    <label class="form-label">Select Team Leads</label>
    <div class="search-box" style="margin-bottom: 12px;">
        <input type="text" class="search-input" placeholder="Search team leads..."
               oninput="filterTeamleads(this.value)">
        <i class="fas fa-search search-icon"></i>
    </div>
    <div id="teamleadList" class="multi-select-list" style="max-height: 200px; overflow-y: auto;"></div>
</div>

    <!-- All team message -->
    <div id="allTeamMessage" class="selection-section" style="display: none; padding: 16px; background: #eff6ff; border-radius: 8px; color: #1e40af; font-size: 14px; text-align: center;">
        <i class="fas fa-users"></i>
        Task will be assigned to all employees and team leads under your operation.
    </div>

    <!-- Hidden field for target_type -->
    <input type="hidden" id="targetType" name="target_type" value="individual">
</div>


                        <div class="form-group">
                            <label class="form-label">Attachments</label>
                            <div class="file-upload">
                                <input type="file" id="taskAttachments" class="file-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                                <label for="taskAttachments" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose files or drag here</span>
                                </label>
                            </div>
                            <div id="fileList" style="margin-top: 12px;"></div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn-primary full">
                                <i class="fas fa-save"></i>
                                <span id="submitBtnText">Create Task</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tasks List -->
                <div class="tasks-section">
                    <div class="section-header">
                        <h2 class="section-title">All Tasks</h2>
                        <div class="view-controls">
                            <div class="search-box">
                                <input type="text" class="search-input" placeholder="Search tasks..." oninput="filterTasks()">
                                <i class="fas fa-search search-icon"></i>
                            </div>
                            <div class="filter-dropdown">
                                <select class="filter-btn" onchange="filterTasks()">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="review">Under Review</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tasks-grid" id="tasksGrid">
                        <!-- Tasks will be populated here -->
                    </div>

                    <!-- Empty State -->
                    <div class="empty-state" id="emptyState" style="display: none;">
                        <div class="empty-state-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>No tasks found</h3>
                        <p>Create your first task to get started</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-container">
            <div class="modal-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="modal-title">Task Created Successfully!</h2>
            <p class="modal-message">The task has been created and assigned to the selected team members. They will receive notifications about their new assignment.</p>
            <div class="modal-actions">
                <button class="btn-primary" onclick="closeModal()">
                    <i class="fas fa-check"></i>
                    Got it
                </button>
            </div>
        </div>
    </div>


    <div id="viewTaskModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="width: 500px; background: white; padding: 24px; border-radius: 8px;">
        <h2 id="viewTaskTitle" style="margin-bottom: 16px;">Task Details</h2>
        <p><strong>Description:</strong> <span id="viewTaskDescription"></span></p>
        <p><strong>Assigned Date:</strong> <span id="viewTaskAssignedDate"></span></p>
        <p><strong>Due Date:</strong> <span id="viewTaskDueDate"></span></p>
        <p><strong>Priority:</strong> <span id="viewTaskPriority"></span></p>
        <p><strong>Status:</strong> <span id="viewTaskStatus"></span></p>
        <p><strong>Progress:</strong> <span id="viewTaskProgress"></span>%</p>
        <p><strong>Assigned To:</strong></p>
        <ul id="viewTaskAssignees" style="margin-left: 20px;"></ul>
        <p><strong>Attachments:</strong></p>
        <ul id="viewTaskAttachments" style="margin-left: 20px;"></ul>

        <button onclick="closeViewModal()" style="margin-top: 20px;" class="btn btn-secondary">Close</button>
    </div>
</div>

    <script>


        // Sample employee data
       const employees = @json($employees); // passed from controller to Blade
       const teamleads = @json($teamleads);

        // Tasks data
      let tasks = []; // will be fetched dynamically via AJAX

let currentPage = 1;
const tasksPerPage = 6;


        // Global variables
        let selectedEmployees = [];
       //let selectedPriority= document.getElementById('taskPriority').value = priority;
       let selectedPriority='';
        let assignmentMode = 'individual';
        let editingTaskId = null;

        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
    fetchTasks(); // Load tasks from DB
    initializePage();
});

// Filter employees by search term
function filterEmployees(searchTerm) {
    const employeeList = document.getElementById('employeeList');
    const items = employeeList.querySelectorAll('.employee-item');

    searchTerm = searchTerm.toLowerCase();

    items.forEach(item => {
        const name = item.querySelector('.employee-name').textContent.toLowerCase();
        const role = item.querySelector('.employee-role').textContent.toLowerCase();
        const matches = name.includes(searchTerm) || role.includes(searchTerm);
        item.style.display = matches ? 'flex' : 'none';
    });
}

// Filter teamleads by search term
function filterTeamleads(searchTerm) {
    const teamleadList = document.getElementById('teamleadList');
    const items = teamleadList.querySelectorAll('.employee-item');

    searchTerm = searchTerm.toLowerCase();

    items.forEach(item => {
        const name = item.querySelector('.employee-name').textContent.toLowerCase();
        const role = item.querySelector('.employee-role').textContent.toLowerCase();
        const matches = name.includes(searchTerm) || role.includes(searchTerm);
        item.style.display = matches ? 'flex' : 'none';
    });
}

function fetchTasks() {
    fetch('/operations/tasks/all', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(res => {
            console.log('Response status:', res.status);
            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {

            tasks = data
                        .map(task => ({
                            ...task,
                            assignees: task.assignees || [],
                            created_at: task.created_at ? new Date(task.created_at) : new Date() // Ensure created_at is a Date
                        }))
                        .sort((a, b) => b.created_at - a.created_at); // Sort by created_at in descending order
            renderTasks();
            updateStats();
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showNotification('Failed to load tasks: ' + err.message, 'error');
        });
}


        // Initialize page
        function initializePage() {
            populateEmployeeList();
            populateTeamleadList();
            renderTasks();
            updateStats();
            setDefaultDates();
        }

        // Set default dates
        function setDefaultDates() {
            const today = new Date();
            const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

            document.getElementById('assignedDate').value = today.toISOString().split('T')[0];
            document.getElementById('dueDate').value = nextWeek.toISOString().split('T')[0];
        }

        // Populate employee list
      function populateEmployeeList() {
    const employeeList = document.getElementById('employeeList');
    employeeList.innerHTML = '';

    employees.forEach(employee => {
        const employeeItem = document.createElement('div');
        employeeItem.className = 'employee-item';
        employeeItem.onclick = () => toggleEmployee(employee.id);

        employeeItem.innerHTML = `
            <div class="employee-checkbox" id="checkbox-${employee.id}"></div>
            <div class="employee-avatar">${employee.avatar ? employee.avatar : ''}</div>
            <div class="employee-info">
                <div class="employee-name">${employee.name}</div>
                <div class="employee-role">${employee.role || 'Employee'}</div>
            </div>
        `;

        employeeList.appendChild(employeeItem);
    });
}
        // Toggle employee selection
        function toggleEmployee(employeeId) {
            const checkbox = document.getElementById(`checkbox-${employeeId}`);
            const index = selectedEmployees.indexOf(employeeId);

            if (index > -1) {
                selectedEmployees.splice(index, 1);
                checkbox.classList.remove('checked');
            } else {
                selectedEmployees.push(employeeId);
                checkbox.classList.add('checked');
            }
        }

        // Toggle assignment mode
       function toggleSelection(mode) {
    assignmentMode = mode;

    // Update button states
    document.getElementById('individualBtn').classList.toggle('active', mode === 'individual');
    document.getElementById('teamleadBtn').classList.toggle('active', mode === 'individual_teamlead');
    document.getElementById('allBtn').classList.toggle('active', mode === 'all');

    // Show/hide sections
    document.getElementById('employeeListSection').style.display = mode === 'individual' ? 'block' : 'none';
    document.getElementById('teamleadListSection').style.display = mode === 'individual_teamlead' ? 'block' : 'none';
    document.getElementById('allTeamMessage').style.display = mode === 'all' ? 'block' : 'none';

    // Clear selections when switching modes
    if (mode === 'individual') {
        // Clear teamlead selections
        selectedTeamleads = [];
        document.querySelectorAll('#teamleadList .employee-checkbox').forEach(checkbox => {
            checkbox.classList.remove('checked');
        });
    } else if (mode === 'individual_teamlead') {
        // Clear employee selections
        selectedEmployees = [];
        document.querySelectorAll('#employeeList .employee-checkbox').forEach(checkbox => {
            checkbox.classList.remove('checked');
        });
    } else if (mode === 'all') {
        // Clear both selections
        selectedEmployees = [];
        selectedTeamleads = [];
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.classList.remove('checked');
        });
    }
}

function populateTeamleadList() {
    const teamleadList = document.getElementById('teamleadList');
    teamleadList.innerHTML = '';

    teamleads.forEach(teamlead => {
        const item = document.createElement('div');
        item.className = 'employee-item';
        item.onclick = () => toggleTeamlead(teamlead.id);

        item.innerHTML = `
            <div class="employee-checkbox" id="checkbox-tl-${teamlead.id}"></div>
            <div class="employee-avatar">${teamlead.avatar || ''}</div>
            <div class="employee-info">
                <div class="employee-name">${teamlead.name}</div>
                <div class="employee-role">Team Lead</div>
            </div>
        `;

        teamleadList.appendChild(item);
    });
}

let selectedTeamleads = [];

function toggleTeamlead(id) {
    const checkbox = document.getElementById(`checkbox-tl-${id}`);
    const index = selectedTeamleads.indexOf(id);

    if (index > -1) {
        selectedTeamleads.splice(index, 1);
        checkbox.classList.remove('checked');
    } else {
        selectedTeamleads.push(id);
        checkbox.classList.add('checked');
    }
}



function selectPriority(priority) {
    // Remove 'active' class from all options
    document.querySelectorAll('.priority-option').forEach(option => {
        option.classList.remove('active');
    });

    // Add 'active' class to the selected option
    const selectedOption = document.querySelector(`.priority-option.${priority}`);
    if (selectedOption) {
        selectedOption.classList.add('active');
    }

    // Update both the hidden input and the global variable ✅
    selectedPriority = priority;
    document.getElementById('taskPriority').value = priority;
}




        // Update progress display
        function updateProgressDisplay() {
            const progress = document.getElementById('taskProgress').value;
            document.getElementById('progressDisplay').textContent = `${progress}%`;
        }

        // Handle file selection
        document.getElementById('taskAttachments').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';

            Array.from(e.target.files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.style.cssText = `
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 12px;
                    background: #f3f4f6;
                    border-radius: 6px;
                    margin-bottom: 8px;
                    font-size: 14px;
                `;

                fileItem.innerHTML = `
                    <i class="fas fa-file" style="color: #6b7280;"></i>
                    <span style="flex: 1; color: #1f2937;">${file.name}</span>
                    <span style="color: #6b7280; font-size: 12px;">${(file.size / 1024).toFixed(1)} KB</span>
                `;

                fileList.appendChild(fileItem);
            });
        });
        //submit task
function submitTask(event) {
    event.preventDefault();

    if (!validateForm()) return;

    const assignedDate = document.getElementById('assignedDate').value;
    const dueDate = document.getElementById('dueDate').value;

    const formData = new FormData();
    formData.append('title', document.getElementById('taskTitle').value);
    formData.append('description', document.getElementById('taskDescription').value);
    formData.append('assigned_date', assignedDate);
    formData.append('due_date', dueDate);
    formData.append('priority', selectedPriority);
    formData.append('progress', document.getElementById('taskProgress').value);
    formData.append('status', 'pending');
    formData.append('target_type', assignmentMode);

    // ✅ Add ID only if updating
    if (editingTaskId) {
        formData.append('id', editingTaskId);
    }

    // Attach employee IDs (if individual) and Attach teamlead (if individual_teamlead)
    if (assignmentMode === 'individual') {
    selectedEmployees.forEach(id => formData.append('employees[]', id));
} else if (assignmentMode === 'individual_teamlead') {
    selectedTeamleads.forEach(id => formData.append('teamleads[]', id));
}


    // Attach uploaded files
    const files = document.getElementById('taskAttachments').files;
    for (let i = 0; i < files.length; i++) {
        formData.append('attachments[]', files[i]);
    }

    // ✅ Use a single route for both create and update
    const url = '/operations/tasks/store';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showSuccessModal();
            fetchTasks();
            resetForm();
        } else {
            showNotification(data.message || 'Something went wrong', 'error');
        }
    })
    .catch(() => showNotification('Task creation failed', 'error'));
}


     //validate form
     function validateForm() {
    const title = document.getElementById('taskTitle').value.trim();
    const description = document.getElementById('taskDescription').value.trim();
    const assignedDate = document.getElementById('assignedDate').value;
    const dueDate = document.getElementById('dueDate').value;


    console.log({
        title,
        description,
        assignedDate,
        dueDate,
        selectedPriority,
    }); // 🧪 Logs all form values

    if (!title || !description || !assignedDate || !dueDate || !selectedPriority) {
        showNotification('Please fill in all required fields', 'error');
        return false;
    }

    if (assignmentMode === 'individual' && selectedEmployees.length === 0) {
        showNotification('Please select at least one employee', 'error');
        return false;
    }

    if (new Date(dueDate) <= new Date(assignedDate)) {
        showNotification('Due date must be after assigned date', 'error');
        return false;
    }

    return true;
}

        // Reset form
    function resetForm() {
    document.getElementById('taskForm').reset();
    document.getElementById('taskId').value = '';
    document.getElementById('formTitle').textContent = 'Create New Task';
    document.getElementById('submitBtnText').textContent = 'Create Task';

    selectedEmployees = [];
    selectedPriority = '';
    editingTaskId = null;
    selectedTeamleads = [];
   selectedTeamleads = [];


    // Reset UI elements
    document.querySelectorAll('.priority-option').forEach(option => {
        option.classList.remove('active');
    });

    document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
        checkbox.classList.remove('checked');
    });

    document.getElementById('progressDisplay').textContent = '0%';
    document.getElementById('fileList').innerHTML = '';

    setDefaultDates(); // This should reset dates
    toggleSelection('individual');
}
        // Render tasks
       // Render tasks
function renderTasks() {
    const tasksGrid = document.getElementById('tasksGrid');
    const emptyState = document.getElementById('emptyState');
    const totalTasks = tasks.length;

    if (totalTasks === 0) {
        tasksGrid.style.display = 'none';
        emptyState.style.display = 'flex';
        return;
    }

    tasksGrid.style.display = 'grid';
    emptyState.style.display = 'none';

    // Calculate pagination
    const totalPages = Math.ceil(totalTasks / tasksPerPage);
    const startIndex = (currentPage - 1) * tasksPerPage;
    const endIndex = startIndex + tasksPerPage;
    const paginatedTasks = tasks.slice(startIndex, endIndex);

    tasksGrid.innerHTML = '';

    paginatedTasks.forEach(task => {
        const taskCard = document.createElement('div');
        taskCard.className = 'task-card';

        const dueDate = new Date(task.due_date);
        const formattedDueDate = dueDate.toLocaleDateString('en-IN', {
            day: 'numeric',
            month: 'short'
        });

        const uniqueAssignees = Array.from(
            new Map(task.assignees.map(a => [a.id, a])).values()
        );
        const displayAssignees = uniqueAssignees.slice(0, 3);
        const remainingCount = Math.max(0, uniqueAssignees.length - 3);

        taskCard.innerHTML = `
            <div class="task-header">
                <div>
                    <div class="task-title">${task.title}</div>
                    <div class="task-id">#${task.id.toString().padStart(3, '0')}</div>
                </div>
                <div class="task-priority priority ${task.priority}">${task.priority}</div>
            </div>
            <div class="task-description">${task.description}</div>
            <div class="task-meta">
                <div class="task-assignees">
                    <span style="font-size: 12px; color: #6b7280; margin-right: 8px;">Assigned to:</span>
                    <div class="assignee-avatars">
                        ${displayAssignees.map(assignee => `
                            <div class="assignee-avatar" title="${assignee.name}">${assignee.avatar || ''}</div>
                        `).join('')}
                        ${remainingCount > 0 ? `<div class="more-assignees" title="${remainingCount} more">+${remainingCount}</div>` : ''}
                    </div>
                </div>
                <div class="task-due-date">
                    <i class="fas fa-calendar"></i>
                    ${formattedDueDate}
                </div>
            </div>
            <div class="task-footer">
                <div class="task-status status ${task.status}">${task.status.replace('-', ' ')}</div>
                <div class="task-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${task.progress}%"></div>
                    </div>
                    <div class="progress-text">${task.progress}%</div>
                </div>
            </div>
            <div class="task-actions" style="margin-top: 10px; display: flex; gap: 8px;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); editTask(${task.id})">Edit</button>
                <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); viewTask(${task.id})">View</button>
            </div>
        `;

        tasksGrid.appendChild(taskCard);
    });

    // Render pagination
    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    const paginationWrapper = document.createElement('div');
    paginationWrapper.className = 'pagination-wrapper';
    paginationWrapper.innerHTML = `
        <ul class="pagination">
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); event.preventDefault();">Previous</a>
            </li>
            ${Array.from({ length: totalPages }, (_, i) => `
                <li class="page-item ${currentPage === i + 1 ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i + 1}); event.preventDefault();">${i + 1}</a>
                </li>
            `).join('')}
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); event.preventDefault();">Next</a>
            </li>
        </ul>
    `;

    const existingPagination = document.querySelector('.pagination-wrapper');
    if (existingPagination) {
        existingPagination.replaceWith(paginationWrapper);
    } else {
        document.querySelector('.tasks-section').appendChild(paginationWrapper);
    }
}

function changePage(page) {
    const totalPages = Math.ceil(tasks.length / tasksPerPage);
    if (page < 1 || page > totalPages) return;

    currentPage = page;
    renderTasks();
}


function setDefaultDates() {
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

    document.getElementById('assignedDate').value = today.toISOString().split('T')[0];
    document.getElementById('dueDate').value = nextWeek.toISOString().split('T')[0];
}


        function formatDate(dateStr) {
   if (!dateStr) return 'N/A';

    // For ISO dates (from server)
    if (dateStr.includes('T')) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-IN', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }

    // For YYYY-MM-DD format (from form)
    const [year, month, day] = dateStr.split('-');
    const date = new Date(year, month - 1, day);
    return date.toLocaleDateString('en-IN', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}

function formatDateForInput(dateStr) {
    if (!dateStr) return '';

    // If already in YYYY-MM-DD format, return as-is
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
        return dateStr;
    }

    // For ISO dates (from server)
    if (dateStr.includes('T')) {
        const date = new Date(dateStr);
        // Use local date components to avoid timezone issues
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // For display format (e.g., "1 Aug 2023")
    const months = {
        'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
        'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
        'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
    };

    const parts = dateStr.split(' ');
    if (parts.length === 3) {
        const day = parts[0].padStart(2, '0');
        const month = months[parts[1]] || '01';
        const year = parts[2];
        return `${year}-${month}-${day}`;
    }

    return '';
}


function editTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (!task) {
        console.error('Task not found for ID:', taskId);
        showNotification('Task not found', 'error');
        return;
    }

    console.log('Editing task:', task); // Debug log

    editingTaskId = taskId;

    // Populate form fields
    document.getElementById('taskTitle').value = task.title || '';
    document.getElementById('taskDescription').value = task.description || '';
    document.getElementById('taskProgress').value = task.progress || 0;
    updateProgressDisplay();

    // Set formatted dates
   document.getElementById('assignedDate').value = formatDateForInput(task.assigned_date);
    document.getElementById('dueDate').value = formatDateForInput(task.due_date);

    // Set priority
    selectPriority(task.priority);

    // Set target_type and assignees
    assignmentMode = task.target_type || 'individual';
    document.getElementById('targetType').value = assignmentMode;

    // Reset selections
    selectedEmployees = [];
    selectedTeamleads = [];

    // Update toggle buttons and sections
    toggleSelection(assignmentMode);

    if (assignmentMode === 'individual') {
        // Populate selected employees
        selectedEmployees = task.assignees.map(a => a.id);
        employees.forEach(employee => {
            const checkbox = document.getElementById(`checkbox-${employee.id}`);
            if (checkbox) {
                checkbox.classList.toggle('checked', selectedEmployees.includes(employee.id));
            }
        });
    } else if (assignmentMode === 'individual_teamlead') {
        // Populate selected team leads
        selectedTeamleads = task.assignees.map(a => a.id);
        teamleads.forEach(teamlead => {
            const checkbox = document.getElementById(`checkbox-tl-${teamlead.id}`);
            if (checkbox) {
                checkbox.classList.toggle('checked', selectedTeamleads.includes(teamlead.id));
            }
        });
    } else if (assignmentMode === 'all') {
        // No individual selections needed for 'all'
        selectedEmployees = [];
        selectedTeamleads = [];
    }

    // Update UI
    document.getElementById('formTitle').textContent = 'Edit Task';
    document.getElementById('submitBtnText').textContent = 'Update Task';
    document.getElementById('taskId').value = taskId; // Set hidden input

    // Clear file input (cannot pre-populate files for security reasons)
    document.getElementById('taskAttachments').value = '';
    document.getElementById('fileList').innerHTML = '';

    // Scroll to form
    document.querySelector('.task-form-card').scrollIntoView({ behavior: 'smooth' });
}
     //view task
     function viewTask(taskId) {

    const task = tasks.find(t => t.id === taskId);
    if (!task) {
        console.error("Task not found for ID:", taskId);
        showNotification('Task not found', 'error');
        return;
    }

    console.log("Task found:", task); // Debug log

    // Populate modal fields
    document.getElementById('viewTaskTitle').textContent = task.title || 'No Title';
    document.getElementById('viewTaskDescription').textContent = task.description || 'No Description';
    document.getElementById('viewTaskAssignedDate').textContent = formatDate(task.assigned_date);
    document.getElementById('viewTaskDueDate').textContent = formatDate(task.due_date);

    document.getElementById('viewTaskPriority').textContent = task.priority || 'N/A';
    document.getElementById('viewTaskStatus').textContent = task.status || 'N/A';
    document.getElementById('viewTaskProgress').textContent = task.progress || '0';

    const assigneeList = document.getElementById('viewTaskAssignees');
    assigneeList.innerHTML = '';
    const uniqueAssignees = new Map();
(task.assignees || []).forEach(assignee => {
    if (!uniqueAssignees.has(assignee.id)) {
        uniqueAssignees.set(assignee.id, assignee.name);
        const li = document.createElement('li');
        li.textContent = assignee.name || 'Unknown';
        assigneeList.appendChild(li);
    }
});


    const attachmentList = document.getElementById('viewTaskAttachments');
    attachmentList.innerHTML = '';
    const attachments = Array.isArray(task.attachments)
    ? task.attachments
    : typeof task.attachments === 'string'
        ? [task.attachments]
        : [];

attachments.forEach(file => {
    const li = document.createElement('li');
    const link = document.createElement('a');
    link.href = `/storage/${file.replace(/^\/?storage\/?/, '')}`;
    link.target = '_blank';
    link.textContent = file.split('/').pop() || 'Unnamed File';
    li.appendChild(link);
    attachmentList.appendChild(li);
});


    // Show modal
    const modal = document.getElementById('viewTaskModal');
    modal.style.display = 'flex';
    modal.classList.add('active'); // Add active class for CSS consistency
    modal.scrollIntoView({ behavior: 'smooth' });
}



//close modal
function closeViewModal() {
    document.getElementById('viewTaskModal').style.display = 'none';
}


        // Filter tasks
function filterTasks() {
    const searchTerm = document.querySelector('.tasks-section .search-input').value.toLowerCase();
    const statusFilter = document.querySelector('.tasks-section .filter-btn').value;

    // Create a filtered copy of tasks without modifying the original
    const filteredTasks = tasks.filter(task => {
        const matchesSearch = task.title.toLowerCase().includes(searchTerm) ||
                            task.description.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || task.status === statusFilter;

        return matchesSearch && matchesStatus;
    });

    // Temporarily replace tasks with filtered results for rendering
    const originalTasks = [...tasks];
    tasks = filteredTasks;
    renderTasks();
    tasks = originalTasks; // Restore original tasks
}

        // Update statistics
        function updateStats() {
            const totalTasks = tasks.length;
            const pendingTasks = tasks.filter(task => task.status === 'pending').length;
            const inProgressTasks = tasks.filter(task => task.status === 'in_progress').length;
            const completedTasks = tasks.filter(task => task.status === 'completed').length;

            animateCounter('totalTasks', totalTasks);
            animateCounter('pendingTasks', pendingTasks);
            animateCounter('inProgressTasks', inProgressTasks);
            animateCounter('completedTasks', completedTasks);
        }

        // Animate counter
        function animateCounter(elementId, targetValue) {
            const element = document.getElementById(elementId);
            const currentValue = parseInt(element.textContent) || 0;
            const increment = targetValue > currentValue ? 1 : -1;
            const duration = 500;
            const steps = Math.abs(targetValue - currentValue);
            const stepDuration = steps > 0 ? duration / steps : 0;

            let current = currentValue;
            const timer = setInterval(() => {
                current += increment;

                if ((increment > 0 && current >= targetValue) || (increment < 0 && current <= targetValue)) {
                    element.textContent = targetValue;
                    clearInterval(timer);
                } else {
                    element.textContent = current;
                }
            }, stepDuration);
        }

        // Show success modal
        function showSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.classList.add('active');
        }

        // Close modal
        function closeModal() {
            const modal = document.getElementById('successModal');
            modal.classList.remove('active');
        }

        // Show notification
        function showNotification(message, type = 'info') {
            // Check if notification container exists, if not create it
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

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                background: ${type === 'success' ? '#dcfce7' : type === 'error' ? '#fee2e2' : '#dbeafe'};
                color: ${type === 'success' ? '#166534' : type === 'error' ? '#991b1b' : '#1e40af'};
                border-left: 4px solid ${type === 'success' ? '#16a34a' : type === 'error' ? '#dc2626' : '#3b82f6'};
                padding: 16px;
                border-radius: 8px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 300px;
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
            `;

            // Icon based on type
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

            // Add to container
            container.appendChild(notification);

            // Add click event to close button
            notification.querySelector('button').addEventListener('click', () => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        container.removeChild(notification);
                    }
                }, 300);
            });

            // Auto remove after 5 seconds
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

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        });
    </script>
</body>
</html>
