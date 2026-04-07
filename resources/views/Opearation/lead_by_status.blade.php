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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            text-align: center;
            vertical-align: middle;
            font-size: 0.875rem;
            font-weight: 600;
            color: black;
            background: linear-gradient(135deg, #f8f9fa 0%, #c4d6e9 100%);
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
            text-align: center;
            vertical-align: middle;
        }
        .leads-table tr:hover td {
            background: var(--gray-50);
        }
        .leads-table tbody tr {
            cursor: pointer;
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
        .status-personal_lead {
            background: var(--purple-100);
            color: var(--purple-700);
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
        .btn-back {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
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
            text-decoration: none;
        }
        .btn-back:hover {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
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
            border-radius: var(--radius-2xl);
            max-width: 1000px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-xl);
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
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
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
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
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
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
        }
        .lead-basic-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
        }
        .lead-basic-info p {
            color: var(--gray-500);
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
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
        }
        .contact-item i {
            width: 20px;
            color: var(--primary-500);
            font-size: 16px;
        }
        .detail-section {
            margin-bottom: 32px;
        }
        .detail-section h4 {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
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
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
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
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
        }
        .detail-item label {
            font-size: 12px;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-item span, .detail-item input, .detail-item select {
            font-size: 15px;
            color: var(--gray-900);
            font-weight: 600;
            text-transform: uppercase;
        }
        .detail-item input, .detail-item select {
            background: transparent;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-300);
            padding: 8px 12px;
        }
        .detail-item input:focus, .detail-item select:focus {
            outline: none;
            border-color: var(--primary-500);
            background: white;
            box-shadow: 0 0 0 3px var(--primary-50);
        }
        .detail-item input:disabled, .detail-item select:disabled {
            background: var(--gray-100);
            color: var(--gray-500);
        }
        .remarks-box {
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--gray-200);
        }
        .remarks-box p {
            color: var(--gray-600);
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
            background: linear-gradient(135deg, var(--success-500) 0%, var(--success-600) 100%);
            color: white;
        }
        .confirm-icon.reject {
            background: linear-gradient(135deg, var(--error-500) 0%, var(--error-600) 100%);
            color: white;
        }
        .confirm-icon.disburse {
            background: linear-gradient(135deg, var(--teal-500) 0%, var(--teal-600) 100%);
            color: white;
        }
        .confirm-icon.login {
            background: linear-gradient(135deg, var(--purple-500) 0%, var(--purple-600) 100%);
            color: white;
        }
        .confirm-message p {
            color: var(--gray-600);
            font-size: 16px;
            line-height: 1.6;
        }
        .confirm-message h3 {
            color: var(--gray-900);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .document-list {
            display: grid;
            gap: 16px;
        }
        .document-item {
            padding: 16px;
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
        }
        .document-item label {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 8px;
            display: block;
        }
        .document-item a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            color: white;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10000;
        }
        .document-item a:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .document-item p {
            margin: 8px 0 0 0;
            padding: 0;
            font-size: 14px;
            color: var(--gray-600);
        }
        .btn-primary, .btn-secondary, .btn-danger, .btn-success {
            padding: 12px 24px;
            border-radius: var(--radius-lg);
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
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .btn-primary:disabled {
            background: var(--primary-100);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }
        .btn-secondary:hover {
            background: var(--gray-200);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .btn-secondary:disabled {
            background: var(--gray-100);
            color: var(--gray-400);
            cursor: not-allowed;
            transform: none;
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
        .btn-danger {
            background: linear-gradient(135deg, var(--error-500) 0%, var(--error-600) 100%);
            color: white;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .btn-danger:disabled {
            background: var(--error-100);
            cursor: not-allowed;
            transform: none;
        }
        .btn-success {
            background: linear-gradient(135deg, var(--success-500) 0%, var(--success-600) 100%);
            color: white;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .btn-success:disabled {
            background: var(--success-100);
            cursor: not-allowed;
            transform: none;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group.full-width {
            grid-column: span 2;
        }
        .form-control {
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 14px;
            color: var(--gray-900);
            background: var(--gray-50);
            transition: all 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px var(--primary-50);
            background: white;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .dashboard-container {
                padding: 1rem;
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
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }
            .pagination .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
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
        .lead-type-display {
            font-size: 15px;
            color: var(--gray-900);
            font-weight: 600;
            text-transform: uppercase;
            padding: 4px 0;
            display: inline-block;
        }
        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-item label {
            font-weight: 500;
            white-space: nowrap;
        }
        .filter-item select {
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .uppercase-table {
            text-transform: uppercase;
        }
        .filter-item {
            text-transform: none;
        }
        .uppercase-table strong {
            text-transform: none;
            font-variant: normal;
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
.filter-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.filter-item label {
    font-size: 14px;
    font-weight: 700;
    color: #111; /* darker text */
    text-transform: uppercase;
}


.filter-item select {
    padding: 6px 8px;
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-300);
    font-size: 12px;
    background: white;
    color: var(--gray-800);
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-item select:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 2px var(--primary-50);
}

.filter-item select:hover {
    border-color: var(--primary-400);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filter-item {
        flex-direction: row;
        align-items: center;
    }

    .filter-item label {
        margin-bottom: 0;
        margin-right: 8px;
    }

    .filter-item select {
        flex: 1;
        min-width: 0;
    }
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
    @include('Opearation.Components.sidebar')
    <div class="main-content">
        @include('Opearation.Components.header', ['title' => $title, 'subtitle' => 'View leads filtered by status'])


        @php
    function formatLeadType($type) {
        $mapping = [
            'personal_loan' => 'PL',
            'business_loan' => 'BL',
            'home_loan' => 'HL',

            // Add more mappings as needed
        ];

        return $mapping[strtolower($type ?? '')] ?? $type ?? 'N/A';
    }
@endphp
        <div class="dashboard-container">
            <div class="leads-table-section">
                <div class="leads-table-header">
                    <h3 class="leads-table-title">
                        <i class="fas fa-table"></i>
                        {{ $title }}
                    </h3>

                           <div class="leads-table-actions mb-6">
                        <a href="{{ route('operations.dashboard') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
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
       <button
            onclick="clearAllFilters()"
            class="btn-secondary1 flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-gray-100 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 hover:transform hover:-translate-y-0.5 hover:shadow-sm"
            title="Clear all filters and search">
            <i class="fas fa-times"></i>
            Clear Filters
        </button>
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


                <div class="table-container">
         <table class="leads-table uppercase-table">
    <thead>
        <tr>
            <th>
                <div class="filter-item">
                    <label for="executive-filter">EXECUTIVE:</label>
                    <select id="executive-filter" name="executive" onchange="applyFilters()">
                        <option value="">ALL EXECUTIVES</option>
                        @foreach($executives as $exec)
                            <option value="{{ strtoupper($exec->name) }}" {{ request('executive') == $exec->id ? 'selected' : '' }}>
                                {{ strtoupper($exec->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div class="filter-item">
                    <label for="client-name-filter">CLIENT NAME:</label>
                    <select id="client-name-filter" name="client_name" onchange="applyFilters()">
                        <option value="">ALL NAMES</option>
                        @foreach($clientNames as $name)
                            <option value="{{ $name }}" {{ request('client_name') == $name ? 'selected' : '' }}>
                                {{ strtoupper($name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div class="filter-item">
                    <label for="loan-account-filter">LOAN AC NO.:</label>
                    <select id="loan-account-filter" name="loan_account" onchange="applyFilters()">
                        <option value="">ALL ACCOUNTS</option>
                        @foreach($loanAccounts as $account)
                            <option value="{{ $account }}" {{ request('loan_account') == $account ? 'selected' : '' }}>
                                {{ strtoupper($account) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div class="filter-item">
                    <label for="mobile-filter">MOB:</label>
                    <select id="mobile-filter" name="mobile" onchange="applyFilters()">
                        <option value="">ALL NUMBERS</option>
                        @foreach($mobiles as $mobile)
                            <option value="{{ $mobile }}" {{ request('mobile') == $mobile ? 'selected' : '' }}>
                                {{ strtoupper($mobile) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div class="filter-item">
                    <label for="company-filter">COMPANY:</label>
                    <select id="company-filter" name="company" onchange="applyFilters()">
                        <option value="">ALL COMPANIES</option>
                        @foreach($companies as $company)
                            <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>
                                {{ strtoupper($company) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
          <th>
    <div class="filter-item">
        <label for="loan-amount-filter">LOAN AMOUNT:</label>
        <select id="loan-amount-filter" name="loan_amount" onchange="applyFilters()">
            <option value="">ALL AMOUNTS</option>
            <option value="1-1000" {{ request('loan_amount') == '1-1000' ? 'selected' : '' }}>₹1 – ₹1,000</option>
            <option value="1000-10000" {{ request('loan_amount') == '1000-10000' ? 'selected' : '' }}>₹1000 – ₹1,000</option>
            <option value="10000-100000" {{ request('loan_amount') == '10000-100000' ? 'selected' : '' }}>₹10,000 – ₹1,00,000</option>
            <option value="100000-1000000" {{ request('loan_amount') == '100000-1000000' ? 'selected' : '' }}>₹1,00,000 – ₹10,00,000</option>
            <option value="1000000+" {{ request('loan_amount') == '1000000+' ? 'selected' : '' }}>Above ₹10,00,000</option>
        </select>
    </div>
</th>

            <th>
                <div class="filter-item">
                    <label for="status-filter">STATUS:</label>
                    <select id="status-filter" name="status_filter" onchange="applyFilters()">
                        <option value="">ALL STATUSES</option>
                        @foreach($statuses as $statusItem)
                            <option value="{{ $statusItem }}" {{ request('status_filter') == $statusItem ? 'selected' : '' }}>
                                {{ strtoupper($statusItem) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
            <th>
                <div class="filter-item">
                    <label for="lead-type-filter">LEAD TYPE:</label>
                    <select id="lead-type-filter" name="lead_type" onchange="applyFilters()">
                        <option value="">ALL TYPES</option>
                        @foreach($formattedLeadTypes as $type)
    <option value="{{ $type['value'] }}" {{ request('lead_type') == $type['value'] ? 'selected' : '' }}>
        {{ strtoupper($type['display']) }}
    </option>
@endforeach

                    </select>
                </div>
            </th>
            <th>
                <div class="filter-item">
                    <label for="bank-filter">BANK:</label>
                    <select id="bank-filter" name="bank" onchange="applyFilters()">
                        <option value="">ALL BANKS</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank }}" {{ request('bank') == $bank ? 'selected' : '' }}>
                                {{ strtoupper($bank) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </th>
                   <th style="cursor: pointer; user-select: none;">
                    <div class="filter-item">
                        <label onclick="toggleDateFilter()">Date:</label>
                        <span id="dateHeader" onclick="toggleDateFilter()">DATE</span>
                        <div id="dateFilterContainer" style="display: none; margin-top: 5px;" onclick="event.stopPropagation()">
                            From: <input type="date" id="fromDate" style="width: 120px;" onchange="applyFilters()" onclick="event.stopPropagation()">
                            To: <input type="date" id="toDate" style="width: 120px;" onchange="applyFilters()" onclick="event.stopPropagation()">
                            <br>
                            <button type="button" onclick="event.stopPropagation(); clearDateFilter()" style="margin-top: 5px;">Clear</button>
                        </div>
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
                data-date="{{ $lead->created_at }}">
                <td>{{ $lead->employee ? strtoupper($lead->employee->name) : 'N/A' }}</td>
                <td><strong>{{ strtoupper($lead->name) }}</strong></td>
                <td>{{ $lead->loan_account_number ? strtoupper($lead->loan_account_number) : 'N/A' }}</td>
                <td>{{ $lead->phone ? strtoupper($lead->phone) : 'N/A' }}</td>
                <td>{{ $lead->company_name ? strtoupper($lead->company_name) : 'N/A' }}</td>
                <td><strong>{{ \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount) }}</strong></td>
                <td>
                    <span class="lead-status status-{{ strtolower($lead->status) }}">
                        {{ strtoupper($lead->status) }}
                    </span>
                </td>
                <td>{{ formatLeadType($lead->lead_type) }}</td>
                <td>{{ $lead->bank_name ? strtoupper($lead->bank_name) : 'N/A' }}</td>
                <td>{{ $lead->updated_at ? $lead->updated_at : 'N/A' }}</td>
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
                                    <div class="contact-item"><i class="fas fa-phone"></i><input type="text" id="modalLeadPhone" class="editable-field" disabled/></div>
                                    <div class="contact-item"><i class="fas fa-envelope"></i><input type="email" id="modalLeadEmail" class="editable-field" disabled/></div>
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
                        <div class="lead-detail-right">
                            <div class="detail-section">
                                <h4>Lead Information</h4>
                                <div class="detail-grid">
                                    <div class="detail-item">
        <label>NAME</label>
        <input type="text" id="modalName" class="editable-field" disabled>
    </div>
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
                                    <div class="detail-item"><label>Loan Amount</label><input type="number" id="modalLeadAmount" class="editable-field" disabled></div>
                                    <div class="detail-item"><label>Amount in Words</label><span id="modalLeadAmountInWords"></span></div>
                                     <div class="detail-item"><label>COMPANY</label><input type="text" id="modalLeadCompany" class="editable-field" disabled></div>
                                    <div class="detail-item"><label>LOAN AC No</label><input type="text" id="modalLeadAccountNumber" class="editable-field" disabled></div>
                                    <div class="detail-item"><label>Status</label><span id="modalLeadStatus" class="lead-status"></span></div>
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
    <label>Lead Type</label>
    <span id="modalLeadTypeDisplay" class="lead-type-display"></span>
    <select id="modalLeadType" class="editable-field" style="display: none;" disabled>
        <option value="">Select Lead Type</option>
        <option value="personal_loan">Personal Loan</option>
        <option value="business_loan">Business Loan</option>
        <option value="home_loan">Home Loan</option>
    </select>
</div>
                                    <div class="detail-item"><label>Turnover Amount</label><input type="number" id="modalLeadTurnoverAmount" class="editable-field" disabled></div>
                                    <div class="detail-item"><label>Salary</label><input type="number" id="modalLeadSalary" class="editable-field" disabled></div>
                                    <div class="detail-item"><label>Bank Name</label><input type="text" id="modalLeadBank" class="editable-field" disabled></div>
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
                    @if(auth()->user()->hasDesignation('operations'))
                        <button class="btn-primary" id="loginButton" onclick="showLoginModal(currentLeadId)">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        <button class="btn-primary" id="approveButton" onclick="showApproveModal(currentLeadId)">
                            <i class="fas fa-check-circle"></i> Approve
                        </button>
                        <button class="btn-danger" id="rejectButton" onclick="showRejectModal(currentLeadId)">
                            <i class="fas fa-times-circle"></i> Reject
                        </button>
                        <button class="btn-success" id="disburseButton" onclick="showDisburseModal(currentLeadId)">
                            <i class="fas fa-rupee-sign"></i> Disburse
                        </button>
                    @endif
                </div>
            </div>
        </div>

         <!-- Login Confirmation Modal -->
<div class="modal-overlay" id="loginModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Login Lead</h2>
            <button class="modal-close" onclick="closeDocModal('loginModal')">
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
            <button class="btn-secondary" onclick="closeDocModal('loginModal')">Cancel</button>
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
            <button class="modal-close" onclick="closeDocModal('approveModal')">
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
            <button class="btn-secondary" onclick="closeDocModal('approveModal')">Cancel</button>
            <button class="btn-success" onclick="confirmApprove()">
                <i class="fas fa-check"></i> Yes, Approve
            </button>
        </div>
    </div>
</div>

        <!-- Reject Confirmation Modal -->
        <div class="modal-overlay" id="rejectModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Reject Lead</h2>
                    <button class="modal-close" onclick="closeDocModal('rejectModal')">
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
                    <button class="btn-secondary" onclick="closeDocModal('rejectModal')">Cancel</button>
                    <button class="btn-danger" onclick="confirmReject()">
                        <i class="fas fa-times"></i> Yes, Reject
                    </button>
                </div>
            </div>
        </div>

       <!-- Disburse Confirmation Modal -->
<div class="modal-overlay" id="disburseModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2 class="modal-title">Disburse Lead</h2>
            <button class="modal-close" onclick="closeDocModal('disburseModal')">
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
            <button class="btn-secondary" onclick="closeDocModal('disburseModal')">Cancel</button>
            <button class="btn-success" onclick="confirmDisburse()">
                <i class="fas fa-check"></i> Yes, Disburse
            </button>
        </div>
    </div>
</div>

        <!-- Add Document Modal -->
        <div class="modal-overlay" id="addDocumentModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Add New Document</h2>
                    <button class="modal-close" onclick="closeDocModal('addDocumentModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content">
                    <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="lead_id" id="documentLeadId">
                        <div class="form-group full-width">
                            <label for="documentName">Document Name <span class="required" style="color: var(--error-500);">*</span></label>
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
                            <label for="documentFile">Upload File <span class="required" style="color: var(--error-500);">*</span></label>
                            <input type="file" id="documentFile" name="document_file" class="form-control" required>
                        </div>
                        <div class="form-actions" style="margin-top: 24px;">
                            <button type="button" class="btn-secondary" onclick="closeDocModal('addDocumentModal')">Cancel</button>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-upload"></i> Save & Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

    function applyFilters() {
    // Get all filter values
    const executive = document.getElementById('executive-filter').value;
    const clientName = document.getElementById('client-name-filter').value;
    const loanAccount = document.getElementById('loan-account-filter').value;
    const mobile = document.getElementById('mobile-filter').value;
    const company = document.getElementById('company-filter').value;
    const loanAmount = document.getElementById('loan-amount-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    const leadType = document.getElementById('lead-type-filter').value;
    const bank = document.getElementById('bank-filter').value;

    // Get current URL
    const url = new URL(window.location.href);

    // Update or remove parameters
    const params = {
        executive,
        client_name: clientName,
        loan_account: loanAccount,
        mobile,
        company,
        loan_amount: loanAmount,
        status_filter: statusFilter,
        lead_type: leadType,
        bank
    };

    // Set or remove each parameter
    Object.entries(params).forEach(([key, value]) => {
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    });

    // Reload the page with new parameters
    window.location.href = url.toString();
}


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

        let currentLeadId = null;

        document.addEventListener('DOMContentLoaded', function () {
           calculateTotalAmount();

            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function() {
                    // This will naturally refresh the page with new filtered data
                    // The total amount will be calculated again on page load
                });
            }

            const nameInput = document.getElementById('modalName');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            if (!this.disabled) {
                // Force input to uppercase in the input field
                this.value = this.value.toUpperCase();
                const name = this.value.trim();
                // Convert to proper case for display
                const properName = name ? name.toLowerCase().replace(/(^|\s)\w/g, char => char.toUpperCase()) : 'N/A';
                document.getElementById('modalLeadName').textContent = properName;
                document.getElementById('modalLeadInitials').textContent = properName && properName !== 'N/A' ? properName.charAt(0).toUpperCase() : '';
            }
        });
    }

    // Loan amount to words conversion
    const leadAmountInput = document.getElementById('modalLeadAmount');
    if (leadAmountInput) {
        leadAmountInput.addEventListener('input', function() {
            if (!this.disabled) {
                const amount = parseInt(this.value);
                document.getElementById('modalLeadAmountInWords').textContent =
                    isNaN(amount) || amount <= 0 ? 'N/A' : numberToWords(amount);
            }
        });
    }
            // Attach click event to lead rows
            document.querySelectorAll('.lead-row').forEach(row => {
                row.addEventListener('click', function (e) {
                    if (e.target.closest('button')) return;
                    const leadId = this.dataset.leadId;
                    if (leadId) viewLeadDetails(leadId);
                });
            });

            // Handle document form submission
            document.getElementById('addDocumentForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const leadId = document.getElementById('documentLeadId').value;
                fetch(`/operations/leads/${leadId}/documents`, {
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
                    showNotification(data.message || 'Document added successfully.', 'success');
                    closeDocModal('addDocumentModal');
                    viewLeadDetails(leadId);
                })
                .catch(error => {
                    console.error("Error adding document:", error);
                    showNotification(`Failed to add document: ${error.message}`, 'error');
                });
            });
        });

        function viewLeadDetails(leadId) {
            currentLeadId = leadId;
            document.getElementById('documentLeadId').value = leadId;
            const modal = document.getElementById('leadDetailModal');
            const docList = document.getElementById('documentList');
            const addDocumentButton = document.getElementById('addDocumentButton');
            const loginButton = document.getElementById('loginButton');
            const approveButton = document.getElementById('approveButton');
            const rejectButton = document.getElementById('rejectButton');
            const disburseButton = document.getElementById('disburseButton');

            if (!modal || !docList) {
                console.error('Modal or document list element not found.');
                showNotification('Failed to open lead details.', 'error');
                return;
            }

            modal.classList.add('active');
            docList.innerHTML = '<p>Loading documents...</p>';

            fetch(`/operations/leads/${leadId}/details`)
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                    return res.json();
                })
                .then(data => {
                    const lead = data.lead;
                    document.getElementById('modalName').value = lead.name ? lead.name.toUpperCase() : 'N/A';
    document.getElementById('modalLeadName').textContent = lead.name ?? 'N/A';
    document.getElementById('modalLeadInitials').textContent = lead.name ? lead.name.charAt(0).toUpperCase() : '';
                    document.getElementById('modalLeadCompany').value = lead.company_name ?? 'N/A';
                    document.getElementById('modalLeadPhone').value = lead.phone ?? 'N/A';
                    document.getElementById('modalLeadEmail').value = lead.email ?? 'N/A';
                    document.getElementById('modalLeadDob').value = lead.dob ?? '';
                    document.getElementById('modalLeadState').value = lead.state ?? '';
                    document.getElementById('modalLeadDistrict').value = lead.district ?? '';
                    document.getElementById('modalLeadCity').value = lead.city ?? '';
                    document.getElementById('modalLeadAmount').value = lead.lead_amount ? `${lead.lead_amount}` : '';
                     document.getElementById('modalLeadAmountInWords').textContent = lead.lead_amount ? numberToWords(parseInt(lead.lead_amount)) : 'N/A';
                    document.getElementById('modalLeadAccountNumber').value = lead.loan_account_number ?? '';
                    document.getElementById('modalLeadStatus').textContent = lead.status ? lead.status.toUpperCase() : 'N/A';
                    document.getElementById('modalLeadExpectedMonth').value = lead.expected_month ?? '';
                    const leadTypeDisplay = lead.lead_type ? lead.lead_type.replace(/_/g, ' ').toUpperCase() : 'N/A';
document.getElementById('modalLeadTypeDisplay').textContent = leadTypeDisplay;
document.getElementById('modalLeadType').value = lead.lead_type || '';
                    document.getElementById('modalLeadTurnoverAmount').value = lead.turnover_amount ? `${lead.turnover_amount}` : '';
                    document.getElementById('modalLeadSalary').value = lead.salary ? `${lead.salary}` : '';
                    document.getElementById('modalLeadBank').value = lead.bank_name ?? '';
                    const voiceElement = document.getElementById('modalLeadVoiceRecording');
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
                    document.getElementById('modalLeadEmployeeName').textContent = lead.employee_name ?? 'N/A';
                    document.getElementById('modalLeadTeamLeadName').textContent = lead.team_lead_name ?? 'N/A';
                    document.getElementById('modalLeadInitials').textContent = lead.name ? lead.name.charAt(0).toUpperCase() : '';

                    const rejectionSection = document.getElementById('rejectionReasonSection');
                    if (lead.reason) {
                        rejectionSection.style.display = 'block';
                        document.getElementById('modalLeadReason').textContent = lead.reason;
                    } else {
                        rejectionSection.style.display = 'none';
                    }

                    const statusElement = document.getElementById('modalLeadStatus');
                    statusElement.className = `lead-status status-${lead.status ?? ''}`;

                    const isApprovedOrDisbursed = ['approved', 'disbursed'].includes(lead.status);
                    const isDisbursed = lead.status === 'disbursed';
                    const isLogin = lead.status === 'login';
                    const isRejected = lead.status === 'rejected';

                    if (addDocumentButton) {
                        addDocumentButton.disabled = isApprovedOrDisbursed || isRejected;
                    }
                    if (loginButton) {
                        loginButton.disabled = isLogin || isApprovedOrDisbursed || isRejected;
                    }
                    if (approveButton) {
                        approveButton.disabled = isApprovedOrDisbursed || isRejected;
                    }
                    if (rejectButton) {
                        rejectButton.disabled = isApprovedOrDisbursed || isRejected;
                    }
                    if (disburseButton) {
                        disburseButton.disabled = isDisbursed || isRejected;
                    }

                    docList.innerHTML = '';
                    if (data.documents.length > 0) {
                        data.documents.forEach(doc => {
                            const uploaded = doc.filepath && doc.filepath.trim() !== '';
                            docList.innerHTML += `
                                <div class="document-item">
                                    <label><strong>${doc.document_name}</strong></label>
                                    ${uploaded ? `
                                        <p>Uploaded: <a href="/storage/${doc.filepath}" target="_blank">View File</a></p>
                                        <form method="POST" action="/operations/leads/${leadId}/documents/${doc.document_id}/delete" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                            <button type="submit" style="margin-left: 10px; color: var(--error-500);" ${isApprovedOrDisbursed ? 'disabled="disabled"' : ''}>Delete</button>
                                        </form>
                                    ` : `
                                        ${isApprovedOrDisbursed ? `
                                            <p style="color: var(--gray-500);">Upload disabled (Lead is ${lead.status})</p>
                                        ` : `
                                            <form method="POST" action="/operations/leads/${leadId}/documents/${doc.document_id}/upload" enctype="multipart/form-data">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                                <input type="file" name="document_file" required>
                                                <button type="submit" class="btn-primary">Upload</button>
                                            </form>
                                        `}
                                    `}
                                </div>
                            `;
                        });
                    } else {
                        docList.innerHTML = '<p>No documents found for this lead.</p>';
                    }

                   // Update follow-up section
                const followupList = document.getElementById('followupList');
                followupList.innerHTML = '';

                if (data.followUps && data.followUps.length > 0) {
                    data.followUps.forEach(fu => {
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



                })
                .catch(error => {
                    console.error('Error fetching lead details:', error);
                    showNotification(`Failed to fetch lead details: ${error.message}`, 'error');
                    closeModal('leadDetailModal');
                });
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
    const loanAccountNumber = document.getElementById('loanAccountNumber').value.trim();
    if (!loanAccountNumber) {
        showNotification('Please provide a loan account number.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'login', null, loanAccountNumber);
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
    const loanAccountNumber = document.getElementById('loanAccountNumber1').value.trim();
    if (!loanAccountNumber) {
        showNotification('Please provide a loan account number.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'approved', null, loanAccountNumber);
}

        function showRejectModal(leadId) {
            if (!leadId) {
                console.error('No leadId provided');
                showNotification('Please select a lead first.', 'error');
                return;
            }
            currentLeadId = leadId;
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectModal').classList.add('active');
        }

         function confirmReject() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                showNotification('Rejection reason is required.', 'error');
                return;
            }
            updateLeadStatus(currentLeadId, 'rejected', reason);
            closeModal('rejectModal');
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
    const loanAccountNumber = document.getElementById('loanAccountNumber2').value.trim();
    if (!loanAccountNumber) {
        showNotification('Please provide a loan account number.', 'error');
        return;
    }
    updateLeadStatus(window.currentLeadId, 'disbursed', null, loanAccountNumber);
}





        function updateLeadStatus(leadId, status, reason = '', loanAccountNumber = '') {
           if (!leadId) {
        console.error('No leadId provided');
        showNotification('Please select a lead first.', 'error');
        return;
    }

    const data = {
        status: status,
        loan_account_number: loanAccountNumber
    };

    if (reason) {
        data.reason = reason;
    }

    fetch(`/operations/leads/${leadId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        return res.json();
    })
    .then(data => {
        showNotification(data.message, 'success');
        closeModal('leadDetailModal');
        closeModal(`${status}Modal`);
        refreshLeads();
    })
    .catch(error => {
        console.error("Status update error:", error.message);
        showNotification(`Failed to update status: ${error.message}`, 'error');
    });
        }

      function refreshLeads() {
            location.reload();
        }


        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('active');
                setTimeout(() => location.reload(), 1000);
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

        function openAddDocumentModal() {
            const modal = document.getElementById('addDocumentModal');
            if (modal) {
                modal.classList.add('active');
            }
        }

    function enableLeadEdit() {
    const currentStatus = document.getElementById('modalLeadStatus').textContent.toLowerCase().replace(' ', '_');
    const isReadOnlyStatus = ['disbursed', 'rejected', 'future_lead'].includes(currentStatus);

    const editButton = document.getElementById('editLeadButton');
    const saveButton = document.getElementById('saveLeadButton');
    const fields = document.querySelectorAll('.editable-field');

    // Define the missing variables
    const bankDropdown = document.getElementById('modalLeadBankDropdown');
    const bankCustomInput = document.getElementById('modalLeadBankCustom');
    // Get the value currently shown in the text input
    const currentBankName = document.getElementById('modalLeadBank').value;

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

    // Load states if not already loaded
    if (document.getElementById('modalLeadStateDropdown').options.length <= 1) {
        loadStates();
    }
}

function disableLeadEdit() {
    document.querySelectorAll('.editable-field').forEach(input => input.disabled = true);
    document.getElementById('editLeadButton').style.display = 'inline-block';
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

      document.getElementById('modalLeadBank').style.display = 'block';
    document.getElementById('modalLeadBankDropdown').style.display = 'none';
    document.getElementById('modalLeadBankCustom').style.display = 'none';
}

async function loadStates() {
    try {
        console.log('Loading states...');

        const response = await fetch('/operations/locations/states', {
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

        const response = await fetch(`/operations/locations/districts/${stateId}`, {
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

        const response = await fetch(`/operations/locations/cities/${districtId}`, {
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

   // 1. Determine Bank Name Logic
    const bankDropdown = document.getElementById('modalLeadBankDropdown');
    let finalBankName = '';

    if (bankDropdown.value === 'Other') {
        finalBankName = document.getElementById('modalLeadBankCustom').value.trim();
        // Validation: Ensure custom bank name is not empty
        if (finalBankName === '') {
            showNotification('Please enter a Bank Name', 'error');
            return;
        }
    } else {
        finalBankName = bankDropdown.value;
    }

    const leadId = currentLeadId;
    const data = {
        name: document.getElementById('modalName').value,
        phone: document.getElementById('modalLeadPhone').value,
        email: document.getElementById('modalLeadEmail').value,
        dob: document.getElementById('modalLeadDob').value,
           state: document.getElementById('modalLeadStateDropdown').options[document.getElementById('modalLeadStateDropdown').selectedIndex].text,
        district: document.getElementById('modalLeadDistrictDropdown').options[document.getElementById('modalLeadDistrictDropdown').selectedIndex].text,
        city: document.getElementById('modalLeadCityDropdown').options[document.getElementById('modalLeadCityDropdown').selectedIndex].text,
        company_name: document.getElementById('modalLeadCompany').value,
        lead_amount: document.getElementById('modalLeadAmount').value,
        loan_account_number: document.getElementById('modalLeadAccountNumber').value,
        expected_month: document.getElementById('modalLeadExpectedMonth').value,
        lead_type: document.getElementById('modalLeadType').value,
        turnover_amount: document.getElementById('modalLeadTurnoverAmount').value,
        salary: document.getElementById('modalLeadSalary').value,
        bank_name: finalBankName
    };

     showLoading(true);

   fetch(`/operations/leads/${leadId}/update`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
})
.then(async res => {
    const contentType = res.headers.get('content-type');
    const isJson = contentType && contentType.includes('application/json');
    const responseData = isJson ? await res.json() : await res.text();

    if (!res.ok) {
        let errorMsg = 'Something went wrong';

        // Handle Laravel validation error format
        if (isJson && typeof responseData.message === 'object') {
            // Show first validation error only
            const firstError = Object.values(responseData.message)[0][0];
            errorMsg = firstError;
        } else if (isJson && typeof responseData.message === 'string') {
            errorMsg = responseData.message;
        } else if (typeof responseData === 'string') {
            errorMsg = responseData;
        }

        throw new Error(errorMsg);
    }

    // Success handling
    showNotification('Lead updated successfully!', 'success');

    document.getElementById('modalLeadBank').value = finalBankName;

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
    document.getElementById('modalLeadTypeDisplay').textContent = leadType
        ? leadType.replace(/_/g, ' ').toUpperCase()
        : 'N/A';

})
.catch(error => {
    console.error('Error updating lead:', error);
    showNotification(`Failed to update lead: ${error.message}`, 'error');
});
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
                background: ${type === 'success' ? 'var(--success-100)' : type === 'error' ? 'var(--error-100)' : 'var(--primary-100)'};
                color: ${type === 'success' ? 'var(--success-700)' : type === 'error' ? 'var(--error-700)' : 'var(--primary-700)'};
                border-left: 4px solid ${type === 'success' ? 'var(--success-500)' : type === 'error' ? 'var(--error-500)' : 'var(--primary-500)'};
                padding: 16px;
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-sm);
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

function clearAllFilters() {
    // Clear the executive search
    document.getElementById('executiveSearch').value = '';

    // Reset all filter dropdowns to their default values
    const filterSelects = [
        'executive-filter',
        'client-name-filter',
        'loan-account-filter',
        'mobile-filter',
        'company-filter',
        'loan-amount-filter',
        'status-filter',
        'lead-type-filter',
        'bank-filter'
    ];

    filterSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            select.selectedIndex = 0; // Reset to first option (usually "ALL...")
        }
    });

    // Clear date filters
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    if (fromDateInput) fromDateInput.value = '';
    if (toDateInput) toDateInput.value = '';

    // Show all rows
    const rows = document.querySelectorAll('.lead-row');
    rows.forEach(row => {
        row.style.display = '';
    });

    // Update counts
    document.getElementById('totalLeadsCount').textContent = rows.length;
    calculateTotalAmount();

    // Optional: Clear URL parameters and reload to reset server-side filters
    const url = new URL(window.location.href);
    url.search = ''; // Clear all query parameters
    window.location.href = url.toString();
}

// Date Filter Functions
function parseDateString(dateStr) {
    // Assuming format like "07 NOV 2024" (DD MMM YYYY, uppercased)
    const parts = dateStr.trim().match(/(\d{1,2})\s+(\w{3})\s+(\d{4})/);
    if (parts) {
        const day = parseInt(parts[1], 10);
        const monthStr = parts[2].toUpperCase();
        const year = parseInt(parts[3], 10);
        const monthMap = {
            'JAN': 0, 'FEB': 1, 'MAR': 2, 'APR': 3, 'MAY': 4, 'JUN': 5,
            'JUL': 6, 'AUG': 7, 'SEP': 8, 'OCT': 9, 'NOV': 10, 'DEC': 11
        };
        const month = monthMap[monthStr];
        if (month !== undefined) {
            const date = new Date(year, month, day);
            if (!isNaN(date.getTime())) {
                return date;
            }
        }
    }
    // Fallback for ISO format "YYYY-MM-DD"
    const isoDate = new Date(dateStr);
    if (!isNaN(isoDate.getTime())) {
        return isoDate;
    }
    return null;
}

function toggleDateFilter() {
    const container = document.getElementById('dateFilterContainer');
    if (container) {
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
    }
}

function clearDateFilter() {
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';
    applyFilters();
    toggleDateFilter(); // Hide the container after clear
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
    const fromDate = fromDateStr ? new Date(fromDateStr + 'T00:00:00') : null;
    const toDate = toDateStr ? new Date(toDateStr + 'T23:59:59') : null;

    // Get dropdown filter values
    const executiveFilter = document.getElementById('executive-filter') ? document.getElementById('executive-filter').value : '';
    const clientNameFilter = document.getElementById('client-name-filter') ? document.getElementById('client-name-filter').value : '';
    const loanAccountFilter = document.getElementById('loan-account-filter') ? document.getElementById('loan-account-filter').value : '';
    const mobileFilter = document.getElementById('mobile-filter') ? document.getElementById('mobile-filter').value : '';
    const companyFilter = document.getElementById('company-filter') ? document.getElementById('company-filter').value : '';
    const loanAmountFilter = document.getElementById('loan-amount-filter') ? document.getElementById('loan-amount-filter').value : '';
    const statusFilter = document.getElementById('status-filter') ? document.getElementById('status-filter').value : '';
    const leadTypeFilter = document.getElementById('lead-type-filter') ? document.getElementById('lead-type-filter').value : '';
    const bankFilter = document.getElementById('bank-filter') ? document.getElementById('bank-filter').value : '';

    for (let i = 1; i < tr.length; i++) {
        const row = tr[i];
        const tds = row.getElementsByTagName('td');
        if (tds.length === 0) continue;

        // Search filter (all columns except date)
        let matchesSearch = !filter;
        if (filter) {
            matchesSearch = false;
            for (let j = 0; j < tds.length - 1; j++) {
                const cellText = tds[j].textContent || tds[j].innerText;
                if (cellText.toUpperCase().indexOf(filter) > -1) {
                    matchesSearch = true;
                    break;
                }
            }
        }

        // Date filter
        const dateTd = tds[tds.length - 1];
        const dateText = dateTd.textContent || dateTd.innerText;
        const rowDate = parseDateString(dateText);
        let matchesDate = true;
        if (fromDate && rowDate && rowDate < fromDate) {
            matchesDate = false;
        }
        if (toDate && rowDate && rowDate > toDate) {
            matchesDate = false;
        }

        // Dropdown filters - check each column
        let matchesFilters = true;

        // Executive filter (column 0)
        if (executiveFilter && tds[0]) {
            const executiveText = (tds[0].textContent || tds[0].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (executiveText === executiveFilter || executiveText.includes(executiveFilter));
        }

        // Client Name filter (column 1)
        if (clientNameFilter && tds[1]) {
            const clientText = (tds[1].textContent || tds[1].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (clientText === clientNameFilter.toUpperCase());
        }

        // Loan Account filter (column 2)
        if (loanAccountFilter && tds[2]) {
            const loanAccountText = (tds[2].textContent || tds[2].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (loanAccountText === loanAccountFilter.toUpperCase());
        }

        // Mobile filter (column 3)
        if (mobileFilter && tds[3]) {
            const mobileText = (tds[3].textContent || tds[3].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (mobileText === mobileFilter.toUpperCase());
        }

        // Company filter (column 4)
        if (companyFilter && tds[4]) {
            const companyText = (tds[4].textContent || tds[4].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (companyText === companyFilter.toUpperCase());
        }

        // Loan Amount filter (column 5)
        if (loanAmountFilter && tds[5]) {
            const amountText = (tds[5].textContent || tds[5].innerText).trim();
            const amount = parseIndianAmount(amountText);
            
            let matchesAmount = false;
            if (loanAmountFilter === '1-1000') {
                matchesAmount = amount >= 1 && amount <= 1000;
            } else if (loanAmountFilter === '1000-10000') {
                matchesAmount = amount >= 1000 && amount <= 10000;
            } else if (loanAmountFilter === '10000-100000') {
                matchesAmount = amount >= 10000 && amount <= 100000;
            } else if (loanAmountFilter === '100000-1000000') {
                matchesAmount = amount >= 100000 && amount <= 1000000;
            } else if (loanAmountFilter === '1000000+') {
                matchesAmount = amount >= 1000000;
            }
            matchesFilters = matchesFilters && matchesAmount;
        }

        // Status filter (column 6)
        if (statusFilter && tds[6]) {
            const statusText = (tds[6].textContent || tds[6].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (statusText === statusFilter.toUpperCase());
        }

        // Lead Type filter (column 7)
        if (leadTypeFilter && tds[7]) {
            const leadTypeText = (tds[7].textContent || tds[7].innerText).toUpperCase().trim();
            // Map filter values to display values (personal_loan -> PL, business_loan -> BL, etc.)
            let expectedValue = leadTypeFilter.toUpperCase();
            if (leadTypeFilter === 'personal_loan') {
                expectedValue = 'PL';
            } else if (leadTypeFilter === 'business_loan') {
                expectedValue = 'BL';
            } else if (leadTypeFilter === 'home_loan') {
                expectedValue = 'HL';
            }
            matchesFilters = matchesFilters && (leadTypeText === expectedValue || leadTypeText.includes(expectedValue));
        }

        // Bank filter (column 8)
        if (bankFilter && tds[8]) {
            const bankText = (tds[8].textContent || tds[8].innerText).toUpperCase().trim();
            matchesFilters = matchesFilters && (bankText === bankFilter.toUpperCase());
        }

        const shouldDisplay = matchesSearch && matchesDate && matchesFilters;
        row.style.display = shouldDisplay ? '' : 'none';
        if (shouldDisplay) count++;
    }

    document.getElementById('totalLeadsCount').textContent = count;
    calculateTotalAmount();
}

// Initialize counts on page load
 document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('.leads-table');
    if (table) {
        const tbody = table.querySelector('tbody');
        if (tbody) {
            const visibleRows = tbody.querySelectorAll('tr:not([style*="display: none"])');
            updateLeadCount();
            calculateTotalAmount();
        }
    }
});


function updateLeadCount() {
    const table = document.querySelector('.leads-table');
    const rows = table.querySelectorAll('tbody tr');

    let count = 0;

    rows.forEach(row => {
        // Ignore hidden rows
        if (row.style.display === "none") return;

        // Detect the “no data” row
        const isEmptyRow =
            row.cells.length === 1 ||
            row.innerText.toLowerCase().includes("no") && row.innerText.toLowerCase().includes("found");

        if (!isEmptyRow) {
            count++;
        }
    });

    document.getElementById('totalLeadsCount').textContent = count;
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

