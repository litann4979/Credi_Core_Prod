<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Leads Management - Lead Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    /* Existing styles unchanged */
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
        padding: 10px 16px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .filter-toggle:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    .advanced-filters {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        display: none;
        animation: slideDown 0.3s ease-out;
    }
    .advanced-filters.active {
        display: block;
    }
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
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
    }
    .filter-select, .filter-input {
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        color: #1f2937;
        background: #f9fafb;
        transition: all 0.2s ease;
    }
    .filter-select:focus, .filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    .search-input-wrapper {
        position: relative;
    }
    .search-input-wrapper i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    .filter-actions {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
    }
    .btn-reset {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-reset:hover {
        background: #e5e7eb;
    }
    .leads-content-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .lead-form-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        animation: slideInLeft 0.6s ease-out;
        height: fit-content;
    }
    .form-header {
        margin-bottom: 24px;
    }
    .form-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .form-header p {
        font-size: 14px;
        color: #6b7280;
    }
    .form-section {
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
    }
    .form-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title i {
        color: #3b82f6;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .form-group.full-width {
        grid-column: span 2;
    }
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .required {
        color: #ef4444;
    }
    .form-control {
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        color: #1f2937;
        background: #f9fafb;
        transition: all 0.2s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }
    .leads-table-container {
        background: white;
        border-radius: 16px;
        padding: 32px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: slideInRight 0.6s ease-out;
    }
    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
    }
    .table-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .table-header h2::before {
        content: '';
        width: 4px;
        height: 28px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-radius: 2px;
    }
    .table-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .results-info {
        font-size: 15px;
        color: #64748b;
        font-weight: 500;
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .btn-refresh {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .btn-refresh:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-color: #3b82f6;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
    .table-wrapper {
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: white;
    }
    .leads-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    .leads-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 600;
        color: black;
                background: linear-gradient(135deg, #f8f9fa 0%, #c4d6e9 100%);
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .leads-table th:first-child {
        border-top-left-radius: 12px;
    }
    .leads-table th:last-child {
        border-top-right-radius: 12px;
    }
    .leads-table td {
        padding: 20px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background: white;
        transition: all 0.2s ease;
    }
    .leads-table tbody tr {
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    .leads-table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .leads-table tbody tr:hover td {
        background: transparent;
    }
    .leads-table tbody tr:last-child td {
        border-bottom: none;
    }
    .lead-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .lead-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        position: relative;
    }
    .lead-avatar::after {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 14px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        z-index: -1;
        opacity: 0.2;
    }
    .lead-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .lead-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
        margin-bottom: 2px;
    }
    .lead-email {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
    .employee-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .employee-name {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    .company-info {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    .amount-display {
        font-weight: 700;
        color: #059669;
        font-size: 15px;
        background: #ecfdf5;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #a7f3d0;
    }
    .status {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .status.personal_lead {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    .status.authorized {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        border: 1px solid #3b82f6;
    }
    .status.approved {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        border: 1px solid #16a34a;
    }
    .status.rejected {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border: 1px solid #dc2626;
    }
    .status.disbursed {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border: 1px solid #059669;
    }
    .status.future_lead {
        background: linear-gradient(135deg, #fef9c3, #fef08a);
        color: #713f12;
        border: 1px solid #ca8a04;
    }
    .status.login {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #3730a3;
        border: 1px solid #6366f1;
    }
    .date-display {
        font-weight: 500;
        color: #64748b;
        font-size: 13px;
        background: #f8fafc;
        padding: 6px 10px;
        border-radius: 6px;
    }
    .row-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #4b5563;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-btn.edit:hover {
        background: #dbeafe;
        border-color: #93c5fd;
        color: #2563eb;
    }
    .action-btn.delete:hover {
        background: #fee2e2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 64px 24px;
        text-align: center;
        display: none;
    }
    .empty-state-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #9ca3af;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .empty-state h3 {
        font-size: 20px;
        font-weight: 700;
        color: #4b5563;
        margin-bottom: 8px;
    }
    .empty-state p {
        font-size: 15px;
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
        border-radius: 12px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .modal-container.modal-sm {
        max-width: 500px;
    }
    .modal-overlay.active .modal-container {
        transform: scale(1);
    }
    .modal-header {
        padding: 20px 24px;
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
    .modal-content {
        padding: 24px;
    }
    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
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
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .lead-basic-info p {
        color: #6b7280;
        margin-bottom: 20px;
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
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 16px;
        position: relative;
        padding-left: 16px;
    }
    .detail-section h4::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: #3b82f6;
        border-radius: 2px;
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
        background: #fee2e2;
        color: #dc2626;
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
    @media (max-width: 1200px) {
        .leads-content-grid {
            grid-template-columns: 1fr;
        }
        .lead-form-container {
            order: 2;
        }
        .leads-table-container {
            order: 1;
            margin-bottom: 24px;
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
        .form-row {
            grid-template-columns: 1fr;
        }
        .lead-detail-grid {
            grid-template-columns: 1fr;
        }
        .detail-grid {
            grid-template-columns: 1fr;
        }
        .leads-table-container {
            padding: 20px;
        }
        .table-header h2 {
            font-size: 20px;
        }
        .leads-table th,
        .leads-table td {
            padding: 12px;
        }
        .lead-avatar {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }
    }
  /* New styles for document list */
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
       /* Add this CSS to your existing styles */
/* Make specific columns uppercase */
.leads-table td:nth-child(1), /* EXECUTIVE column */
.leads-table td:nth-child(2), /* Lead column */
.leads-table td:nth-child(3), /* Company column */
.leads-table td:nth-child(6) { /* Team Lead column */
    text-transform: uppercase;
}


</style>
</head>
<body>
    @include('TeamLead.Components.sidebar')
    <div class="main-content">
        @include('TeamLead.Components.header', ['title' => 'Leads Management', 'subtitle' => 'Create, view, and manage your leads'])
        <div class="dashboard-container">
            <div class="leads-content-grid">
                <div class="leads-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-users"></i> All Leads</h2>

                    </div>
                    <div class="table-wrapper">
                        <table id="leadsTable" class="leads-table">
                            <thead>
                                <tr>
                                     <th><i class="fas fa-user-tie"></i>EXECUTIVE</th>
                                    <th><i class="fas fa-user"></i> Lead</th>

                                    <th><i class="fas fa-building"></i> Company</th>
                                    <th><i class="fas fa-rupee-sign"></i> Amount</th>
                                    <th><i class="fas fa-flag"></i> Status</th>
                                    <th><i class="fas fa-user-check"></i> Team Lead</th>
                                    <th><i class="fas fa-calendar"></i> Created At</th>

                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
                                @foreach($formattedLeads as $lead)
                                    <tr onclick="viewLeadDetails({{ $lead['id'] }})">

                                         <td>
                                            <div class="employee-info">
                                                <div class="employee-name uppercase">{{ $lead['employee_name'] }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="lead-info">
                                                <div class="lead-avatar uppercase">{{ substr($lead['name'], 0, 1) }}</div>
                                                <div class="lead-details">
                                                    <div class="lead-name uppercase">{{ $lead['name'] }}</div>
                                                    <div class="lead-email uppercase">{{ $lead['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="company-info uppercase">{{ $lead['company'] }}</div>
                                        </td>
                                        <td>
                                            <div class="amount-display">₹{{ number_format($lead['amount']) }}</div>
                                        </td>
                                        <td><span class="status {{ $lead['status'] }}">{{ str_replace('_', ' ', ucfirst($lead['status'])) }}</span></td>
                                        <td>
                                            <span class="status {{ $lead['team_lead_assigned'] ? 'approved' : 'personal_lead' }}">
                                                {{ $lead['team_lead_assigned'] ? 'Assigned' : 'Not Assigned' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="date-display">{{ $lead['created_at'] }}</div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="emptyState" class="empty-state" style="display: {{ $leads->isEmpty() ? 'flex' : 'none' }};">
                        <div class="empty-state-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No leads found</h3>
                        <p>Try adjusting your filters or add a new lead</p>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!--Lead Detail Modal-->
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
                                                <input type="text" id="modalLeadBankName" class="form-control editable-field">
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
                                <button class="btn-primary" id="futureLeadButton" onclick="showFutureLeadConfirm(currentLeadId)">
                                    <i class="fas fa-clock"></i> Mark as Future Lead
                                </button>
                                <button class="btn-primary" id="forwardOperationsButton" onclick="forwardToOperations(currentLeadId)">
                                    <i class="fas fa-share"></i> Forward to Operations
                                </button>
                                <button class="btn-danger" id="rejectButton" onclick="showRejectConfirm(currentLeadId)">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                                <button class="btn-danger" id="deleteButton" onclick="showDeleteConfirm(currentLeadId)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    <div class="modal fade" id="authorizeConfirmModal" tabindex="-1" aria-labelledby="authorizeConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Authorize</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="confirmAuthorize()">
                        <i class="fas fa-check-circle"></i>
                        Authorize
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="futureLeadConfirmModal" tabindex="-1" aria-labelledby="futureLeadConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Mark as Future Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="confirmFutureLead()">
                        <i class="fas fa-clock"></i>
                        Mark as Future Lead
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-labelledby="rejectConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Reject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="confirm-message">
                        <div class="confirm-icon">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">
                        <i class="fas fa-times-circle"></i>
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

  <!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmLabel">Confirm Delete</h5>  <!-- Added id here -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="confirm-message">
                    <div class="confirm-icon">
                        <i class="fas fa-trash"></i>
                    </div>
                    <p>Are you sure you want to delete this lead? This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

    {{-- Forward to operation modal --}}
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


        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
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
            document.getElementById('modalLeadBankName').value = lead.bank_name || '';
            document.getElementById('modalLeadAmount').value = lead.amount ?? '';
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
        expectedMonthField.style.backgroundColor = '#f3f4f6';
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



        // Initial leads data from server
        let leadsData = @json($formattedLeads);

        let filteredLeads = [...leadsData];
        let currentLeadId = null;
        let isEditing = false;

        // DOM Ready
        document.addEventListener('DOMContentLoaded', function() {
            renderLeadsTable();
        });

        // Toggle filters visibility
        function toggleFilters() {
            const filtersElement = document.getElementById('advancedFilters');
            filtersElement.classList.toggle('active');
            const icon = document.querySelector('.filter-toggle i:last-child');
            icon.className = filtersElement.classList.contains('active') ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
        }

        // Filter leads
        function filterLeads() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            window.location.href = `${window.location.pathname}?${params}`;
        }

        // Render leads table
        function renderLeadsTable() {
            const tableBody = document.getElementById('leadsTableBody');
            const emptyState = document.getElementById('emptyState');
            const tableWrapper = document.querySelector('.table-wrapper');
            if (filteredLeads.length === 0) {
                emptyState.style.display = 'flex';
                tableWrapper.style.display = 'none';
                return;
            }
            emptyState.style.display = 'none';
            tableWrapper.style.display = 'block';
            tableBody.innerHTML = '';
            filteredLeads.forEach(lead => {
                const row = document.createElement('tr');
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.user-profile')) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    viewLeadDetails(lead.id);
                });
                const formattedAmount = new Intl.NumberFormat('en-IN', {
                    style: 'currency',
                    currency: 'INR',
                    maximumFractionDigits: 0
                }).format(lead.amount);
                const initials = lead.name.split(' ').map(n => n[0]).join('');
                row.innerHTML = `

                    <td>
                        <div class="employee-info">
                            <div class="lead-name">${lead.employee_name || '-'}</div>
                        </div>
                    </td>
                    <td>
                        <div class="lead-info">
                            <div class="lead-avatar">${initials}</div>
                            <div class="lead-details">
                                <div class="lead-name">${lead.name}</div>
                                <div class="lead-email">${lead.email || '-'}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="company-info">${lead.company || '-'}</div>
                    </td>
                    <td>
                        <div class="amount-display">${formattedAmount}</div>
                    </td>
                    <td><span class="status ${lead.status}">${lead.status.replace('_', ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')}</span></td>
                    <td>
                        <span class="status ${lead.team_lead_assigned ? 'approved' : 'personal_lead'}">
                            ${lead.team_lead_assigned ? 'Assigned' : 'Not Assigned'}
                        </span>
                    </td>
                    <td>
                        <div class="date-display">${lead.created_at}</div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }




        // Show Authorize Confirmation
        function showAuthorizeConfirm(id) {
            currentLeadId = id;
            document.getElementById('authorizeRemarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('authorizeConfirmModal'));
            modal.show();
        }

        // Confirm Authorize
        function confirmAuthorize() {
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;
            setAuthorized(currentLeadId);
        }

        // Set authorized
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
                    if (remarks) lead.notes = remarks;
                    fetchLeads();
                    // Close both modals
                    bootstrap.Modal.getInstance(document.getElementById('authorizeConfirmModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error authorizing lead');
                console.error(error);
            });
        }

        // Show Future Lead Confirmation
        function showFutureLeadConfirm(id) {
            currentLeadId = id;
            document.getElementById('futureLeadRemarks').value = '';

            const modal = new bootstrap.Modal(document.getElementById('futureLeadConfirmModal'));
            modal.show();
        }

        // Confirm Future Lead
        function confirmFutureLead() {
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;
            setFutureLead(currentLeadId);
        }

        // Set future lead
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
                console.log('Response Status:', response.status);
                console.log('Response Headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Raw Response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        alert(data.message);
                        lead.status = 'future_lead';
                        if (remarks) lead.notes = remarks;
                        fetchLeads();
                        // Close both modals
                        bootstrap.Modal.getInstance(document.getElementById('futureLeadConfirmModal')).hide();
                        bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                    } else {
                        alert(data.message || 'Unknown error occurred');
                    }
                } catch (e) {
                    console.error('JSON Parsing Error:', e);
                    alert('Error parsing server response');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert(`Error updating lead: ${error.message}`);
            });
        }

        // Show Reject Confirmation
        function showRejectConfirm(id) {
            currentLeadId = id;
            document.getElementById('rejectRemarks').value = '';
            const modal = new bootstrap.Modal(document.getElementById('rejectConfirmModal'));
            modal.show();
        }

        // Confirm Reject
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

        // Reject lead
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
                console.log('Response Status:', response.status);
                console.log('Response Headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}, Body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response Data:', data);
                if (data.status === 'success') {
                    alert(data.message);
                    lead.status = 'rejected';
                    lead.reason = remarks;
                    fetchLeads();
                    // Close both modals
                    bootstrap.Modal.getInstance(document.getElementById('rejectConfirmModal')).hide();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                } else {
                    alert(data.message || 'Failed to reject lead');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert(`Error rejecting lead: ${error.message}`);
            });
        }

        // Edit lead
        function editLead(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) return;
            isEditing = true;
            currentLeadId = id;
            document.getElementById('formTitle').textContent = 'Edit Lead';
            document.getElementById('formSubtitle').textContent = 'Update the lead information';
            document.getElementById('saveButton').innerHTML = '<i class="fas fa-save"></i> Update Lead';
            document.getElementById('leadId').value = lead.id;
            document.getElementById('leadName').value = lead.name;
            document.getElementById('leadEmail').value = lead.email || '';
            document.getElementById('leadPhone').value = lead.phone;
            document.getElementById('leadDob').value = lead.dob || '';
            document.getElementById('leadCity').value = lead.city || '';
            document.getElementById('leadDistrict').value = lead.district || '';
            document.getElementById('leadState').value = lead.state || '';
            document.getElementById('leadCompany').value = lead.company || '';
            document.getElementById('leadBankName').value = lead.bank_name || '';
            document.getElementById('leadAmount').value = lead.amount;
            document.getElementById('leadSalary').value = lead.salary || '';
            document.getElementById('leadSuccessPercentage').value = lead.success_percentage || '';
            document.getElementById('leadExpectedMonth').value = lead.expected_month || '';
            document.getElementById('leadType').value = lead.lead_type || '';
            document.getElementById('leadTurnoverAmount').value = lead.turnover_amount || '';
            document.getElementById('leadVintageYear').value = lead.vintage_year || '';
            document.getElementById('leadVoiceRecording').value = lead.voice_recording || '';
            document.getElementById('leadNotes').value = lead.notes || '';
            document.getElementById('leadTeamLead').value = lead.team_lead_id || '';
            bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
            document.querySelector('.lead-form-container').scrollIntoView({ behavior: 'smooth' });
        }

        // Show Delete Confirmation
        // Show Delete Confirmation
function showDeleteConfirm(id) {
    if (!id) {
        console.error('No lead ID provided for delete confirmation');
        showNotification('Invalid lead ID', 'error');
        return;
    }
    currentLeadId = id;
    console.log('Opening delete modal for lead ID:', currentLeadId);  // Debug log
    const modalElement = document.getElementById('deleteConfirmModal');
    if (!modalElement) {
        console.error('Delete modal element not found');
        return;
    }
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

        // Delete lead
        function confirmDelete() {
            if (!currentLeadId) return;

            // Close the confirmation modal first
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            if (deleteModal) {
                deleteModal.hide();
            }

            fetch(`/team-lead/leads/${currentLeadId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    leadsData = leadsData.filter(lead => lead.id !== currentLeadId);
                    filteredLeads = filteredLeads.filter(lead => lead.id !== currentLeadId);
                    renderLeadsTable();

                    // Close the lead detail modal as well
                    const leadDetailModal = bootstrap.Modal.getInstance(document.getElementById('leadDetailModal'));
                    if (leadDetailModal) {
                        leadDetailModal.hide();
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error deleting lead', 'error');
                console.error(error);
            });
        }

        // Save or update lead
        function saveLead(event) {
            event.preventDefault();
            const form = document.getElementById('leadForm');
            const formData = new FormData(form);
            const leadData = Object.fromEntries(formData);
            const url = isEditing ? `/team-lead/leads/${leadData.id}` : '';
            const method = isEditing ? 'PUT' : 'POST';
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(leadData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    fetchLeads();
                    resetForm();
                } else {
                    showNotification(data.message || 'Error saving lead', 'error');
                }
            })
            .catch(error => {
                showNotification('Error saving lead', 'error');
                console.error(error);
            });
        }

        // Forward to team lead
        function forwardToTeamLead(id) {
            const lead = leadsData.find(lead => lead.id === id);
            if (!lead) return;
            const teamLeadId = prompt('Enter Team Lead ID:');
            const remarks = prompt('Enter remarks (optional):');
            if (!teamLeadId) {
                showNotification('Team Lead ID is required.', 'error');
                return;
            }
            fetch(`/team-lead/leads/${id}/forward`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    team_lead_id: teamLeadId,
                    remarks: remarks || undefined
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    lead.team_lead_assigned = true;
                    lead.team_lead_id = teamLeadId;
                    if (remarks) lead.notes = remarks;
                    fetchLeads();
                    bootstrap.Modal.getInstance(document.getElementById('leadDetailModal')).hide();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error forwarding lead', 'error');
                console.error(error);
            });
        }

        // Forward to admin
        // function forwardToAdmin(leadId) {
        //     let remarks = prompt("Enter remarks for forwarding to Admin:");
        //     if (remarks === null) return;
        //     remarks = remarks.trim();
        //     if (!remarks) {
        //         alert("Remarks are required to forward the lead.");
        //         return;
        //     }
        //     fetch(`/team-lead/leads/${leadId}/forward-admin`, {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        //         },
        //         body: JSON.stringify({ remarks })
        //     })
        //     .then(res => res.json())
        //     .then(data => {
        //         if (data.status === 'success') {
        //             alert('Lead forwarded to Admin.');
        //             location.reload();
        //         } else {
        //             alert(data.message || 'Something went wrong.');
        //         }
        //     });
        // }

        // Forward to operations
      let selectedLeadId = null;
function forwardToOperations(leadId) {
    selectedLeadId = leadId;
    document.getElementById('operation-remarks').value = ''; // Clear remarks field
    const modal = new bootstrap.Modal(document.getElementById('forwardOperationsModal'));
    modal.show();
}


       function submitForwardToOperations() {
    const remarks = document.getElementById('operation-remarks').value;
    fetch(`/team-lead/leads/${selectedLeadId}/forward-operations`, {
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

        // Fetch updated leads
        function fetchLeads() {
            fetch('{{ route('team_lead.leads.index') }}?' + new URLSearchParams({
                status: document.getElementById('statusFilter')?.value || '',
                assignment: document.getElementById('assignmentFilter')?.value || '',
                date_from: document.getElementById('dateFromFilter')?.value || '',
                date_to: document.getElementById('dateToFilter')?.value || ''
            }))
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('#leadsTableBody')?.innerHTML || '';
                const newEmptyState = doc.querySelector('#emptyState')?.style.display || 'none';
                document.getElementById('leadsTableBody').innerHTML = newTableBody;
                document.querySelector('.table-wrapper').style.display = newEmptyState === 'flex' ? 'none' : 'block';
                document.getElementById('emptyState').style.display = newEmptyState;
                return fetch('{{ route('team_lead.leads.index') }}?data_only=true');
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    leadsData = data.formattedLeads || [];
                    filteredLeads = [...leadsData];
                    renderLeadsTable();
                } else {
                    showNotification(data.message || 'Error fetching leads', 'error');
                }
            })
            .catch(error => {
                showNotification('Error refreshing leads: ' + error.message, 'error');
                console.error(error);
            });
        }

        // Reset form
        function resetForm() {
            document.getElementById('leadForm').reset();
            document.getElementById('leadId').value = '';
            document.getElementById('formTitle').textContent = 'Add New Lead';
            document.getElementById('formSubtitle').textContent = 'Fill in the details to create a new lead';
            document.getElementById('saveButton').innerHTML = '<i class="fas fa-save"></i> Save Lead';
            isEditing = false;
            currentLeadId = null;
        }

        // Refresh leads
        function refreshLeads() {
            fetchLeads();
            showNotification('Leads refreshed', 'info');
        }

        // Show notification
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
                background: ${type === 'success' ? '#dcfce7' : type === 'error' ? '#fee2e2' : '#dbeafe'};
                color: ${type === 'success' ? '#166534' : type === 'error' ? '#991b1b' : '#1e40af'};
                border-left: 4px solid ${type === 'success' ? '#16a34a' : type === 'error' ? '#dc2626' : '#3b82f6'};
                padding: 16px;
                border-radius: 6px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
                    if (notification.parentNode) container.removeChild(notification);
                }, 300);
            });
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) container.removeChild(notification);
                    }, 300);
                }
            }, 5000);
        }

        function submitFilters() {
            document.getElementById('filterForm').submit();
        }

        function resetFilters() {
            const url = new URL(window.location.href);
            url.search = '';
            window.location.href = url.toString();
        }

          let originalLeadData = {};

        function enableEditMode() {
    const lead = leadsData.find(lead => lead.id === currentLeadId);
    if (!lead) return;

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
            const lead = leadsData.find(lead => lead.id === currentLeadId);
            if (!lead) return;

            const updatedData = {
                name: document.getElementById('modalLeadName').value,
                loan_account_number: document.getElementById('modalLoanAccountNumber').value,
                phone: document.getElementById('modalLeadPhone').value,
                email: document.getElementById('modalLeadEmail').value,
                dob: document.getElementById('modaldob').value,
                state: document.getElementById('modalLeadStateDropdown').options[document.getElementById('modalLeadStateDropdown').selectedIndex].text,
                district: document.getElementById('modalLeadDistrictDropdown').options[document.getElementById('modalLeadDistrictDropdown').selectedIndex].text,
                city: document.getElementById('modalLeadCityDropdown').options[document.getElementById('modalLeadCityDropdown').selectedIndex].text,
                lead_amount: document.getElementById('modalLeadAmount').value,
                expected_month: document.getElementById('modalLeadExpectedMonth').value,
                lead_type: document.getElementById('modalLeadType').value,
                turnover_amount: document.getElementById('modalLeadTurnoverAmount').value,
                salary: document.getElementById('modalsalary').value,
                bank_name: document.getElementById('modalLeadBankName').value,
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
                    document.getElementById('editBtn').classList.remove('d-none');
            document.getElementById('saveChangesBtn').classList.add('d-none');
            document.getElementById('cancelEditBtn').classList.add('d-none');
                } else {
                    alert('Error updating lead: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error updating lead: ' + error.message);
                console.error(error);
            });
        }


    </script>
</body>
</html>
