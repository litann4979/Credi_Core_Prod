<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Holiday Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Added Google Fonts and Font Awesome for professional typography and icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* Added CSS custom properties for consistent design system */
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-600: #4b5563;
        --gray-800: #1f2937;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --primary-50: #f0f9ff;
            --primary-100: #e0f2fe;
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
    }

    /* Enhanced body typography and background */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
        color: var(--gray-800);
        line-height: 1.6;
    }

    /* Professional content wrapper with better spacing */
    .content-wrapper {
        margin-top: 90px;
        margin-left: 250px;
        padding: 40px;
        min-height: calc(100vh - 90px);
    }

    /* Enhanced heading with better typography */
    .page-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 2rem;
        position: relative;
        text-align: center;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        border-radius: 2px;
    }

    /* Professional card styling with enhanced shadows */
    .professional-card {
        background: #ffffff;
        border: 1px solid var(--gray-200);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .professional-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    .card-header-custom {
         background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%);
       color: #060606 !important;
        padding: 1.5rem;
        border: none;
        font-weight: 600;
        font-size: 1.5rem;
    }

    /* Enhanced form styling */
    .form-control {
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: var(--gray-50);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
        background: #ffffff;
        transform: translateY(-1px);
    }

    /* Professional button styling with icons */
    .btn {
        border-radius: 10px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: linear-gradient(135deg, var(--primary-hover) 0%, #1e40af 100%);
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #ea580c 100%);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .btn-warning:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #b91c1c 100%);
        box-shadow: var(--shadow-sm);
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%);
        box-shadow: var(--shadow-sm);
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-sm {
        min-width: 90px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    /* Enhanced table styling */
    .table-professional {
        border-collapse: separate;
        border-spacing: 0;
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table-professional thead th {
        background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%);
         color: #060606 !important;
        font-weight: 600;
        padding: 1.25rem 1rem;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-professional tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--gray-100);
    }

    .table-professional tbody tr:hover {
        background: var(--gray-50);
        transform: scale(1.01);
    }

    .table-professional tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border: none;
        font-size: 0.9rem;
    }

    /* Enhanced status badges */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-active {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: var(--success-color);
        border: 1px solid #86efac;
    }

    .status-inactive {
        background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        color: var(--danger-color);
        border: 1px solid #fca5a5;
    }

    /* Action buttons container */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--gray-600);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 1rem;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .content-wrapper {
            margin-left: 0;
            padding: 20px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-sm {
            min-width: auto;
        }
    }
</style>
</head>
<body>
    @include('hr.Components.sidebar')

    <div class="content-wrapper">
        @include('hr.Components.header')

        <!-- Enhanced page title with professional styling -->
        <h2 class="page-title">
            <i class="fas fa-calendar-alt me-3"></i>Holiday Management
        </h2>

        {{-- Add / Update Holiday Form --}}
        <!-- Enhanced card with professional styling and header -->
        <div class="professional-card mb-5">
            <div class="card-header-custom">
                <i class="fas  me-2"></i>
                {{ isset($editHoliday) ? 'Update Holiday' : 'Add New Holiday' }}
            </div>
            <div class="card-body p-4">
                <form class="row g-4" method="POST" action="{{ isset($editHoliday) ? route('hr.holidays.update', $editHoliday->id) : route('hr.holidays.store') }}">
                    @csrf
                    <div class="col-md-5">
                        <!-- Enhanced input with icon -->
                        <div class="position-relative">
                            <i class="fas fa-tag position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray-600);"></i>
                            <input type="text" name="name" class="form-control ps-5" placeholder="Holiday Name" value="{{ old('name', $editHoliday->name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Enhanced date input with icon -->
                        <div class="position-relative">
                            <i class="fas fa-calendar position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray-600);"></i>
                            <input type="date" name="date" class="form-control ps-5" value="{{ old('date', isset($editHoliday) ? $editHoliday->date->toDateString() : '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <!-- Enhanced button with icon -->
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas {{ isset($editHoliday) ? 'fa-edit' : 'fa-plus' }}"></i>
                            {{ isset($editHoliday) ? 'Update Holiday' : 'Add Holiday' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Holiday List --}}
        <!-- Enhanced card with professional styling and header -->
        <div class="professional-card">
            <div class="card-header-custom">
                <i class="fas fa-list me-2"></i>
                Holiday List
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <!-- Enhanced table with professional styling -->
                    <table class="table table-professional mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag me-2"></i>Holiday Name</th>
                                <th><i class="fas fa-calendar me-2"></i>Date</th>
                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($holidays as $holiday)
                            <tr>
                                <td class="fw-medium">{{ $holiday->name }}</td>
                                <td>
                                    <i class="fas fa-calendar-day me-2 text-muted"></i>
                                    {{ $holiday->date->format('d M Y') }}
                                </td>
                                <td>
                                    <!-- Enhanced status badges with icons -->
                                    <span class="status-badge {{ $holiday->trashed() ? 'status-inactive' : 'status-active' }}">
                                        <i class="fas {{ $holiday->trashed() ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                        {{ $holiday->trashed() ? 'Inactive' : 'Active' }}
                                    </span>
                                </td>
                                <td>
                                    <!-- Enhanced action buttons with better layout -->
                                    <div class="action-buttons">
                                        <a href="{{ route('hr.holidays.edit', $holiday->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('hr.holidays.toggle', $holiday->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $holiday->trashed() ? 'btn-success' : 'btn-danger' }}">
                                                <i class="fas {{ $holiday->trashed() ? 'fa-play' : 'fa-pause' }}"></i>
                                                {{ $holiday->trashed() ? 'Activate' : 'Deactivate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <div class="fw-medium">No holidays found</div>
                                    <small class="text-muted">Add your first holiday using the form above</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
