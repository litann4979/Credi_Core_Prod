<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --bg-color: #f3f4f6;
        --text-color: #1f2937;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-color);
        color: var(--text-color);
        overflow-x: hidden;
    }

    .main-container {
        margin-left: 250px;
        transition: margin-left 0.3s ease;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .container-fluid {
        padding: 2rem;
    }

    .card-modern {
        background: white;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .card-header-modern {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-modern h4 {
        font-weight: 700;
        color: #111827;
        margin: 0;
        font-size: 1.25rem;
    }

    .btn-modern {
        background-color: var(--primary-color);
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-modern:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }

    .btn-modern-outline {
        background: white;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
    }

    .btn-modern-outline:hover {
        background: #eef2ff;
        color: var(--primary-hover);
    }

    .table-modern {
        width: 100%;
        margin-bottom: 0;
    }

    .table-modern th {
        background-color: #f9fafb;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-modern td {
        padding: 1rem 2rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 0.875rem;
    }

    .table-modern tr:hover td {
        background-color: #f9fafb;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        transition: all 0.2s;
        background: transparent;
        border: 1px solid transparent;
    }

    .action-btn:hover {
        background-color: #f3f4f6;
        color: var(--primary-color);
    }

    .action-btn.delete:hover {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border-color: #d1d5db;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .section-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .assignee-box {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        background: #fafafa;
        height: 100%;
    }

    @media (max-width: 768px) {
        .main-container { margin-left: 0; }
    }
</style>
