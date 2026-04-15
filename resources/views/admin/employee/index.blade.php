<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* --- MODERN UI THEME (Matches Previous Project) --- */
        :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --purple-500: #8b5cf6;
            --purple-600: #7c3aed;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --success-500: #10b981;
            --danger-500: #ef4444;
            --warning-500: #f59e0b;
        }

        body {
            background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
            font-family: 'Inter', sans-serif;
            color: var(--gray-700);
        }

           /* Layout Container */
       .main-container {
    min-height: 100vh;
    padding: 1.5rem 1rem 2rem;
    padding-left: 280px;
    max-width: 1500px;
    margin: 0 auto;
    margin-top: 30px; /* Add this line to push content down below fixed header */
}

       @media (max-width: 768px) {
    .main-container {
        padding-left: 1rem;
        padding-right: 1rem;
        padding-top: 1rem;
        margin-top: 60px; /* Adjust based on mobile header height */
    }
}

        /* Typography */
        .page-title {
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 0.2rem;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--primary-600) 50%, var(--purple-500) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .page-subtitle {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .page-hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.1rem;
            padding: 1rem 1.1rem;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        /* Modern Stat Cards */
        .stat-card-modern {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            padding: 1rem 1.1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-500), var(--purple-500));
        }

        .stat-card-modern.active-card::before {
            background: linear-gradient(to bottom, var(--success-500), #059669);
        }

        .stat-number {
            font-size: 1.95rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1;
        }

        .stat-label {
            color: var(--gray-500);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        /* Modern Table */
        .table-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 0.75rem;
        }

        @media (max-width: 992px) {
            .page-hero {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.6rem;
            }
        }

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: left;
            border: none;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--gray-100);
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, var(--primary-50) 0%, var(--gray-50) 100%);
        }

        .table-modern td {
            padding: 0.65rem 1rem;
            color: var(--gray-700);
            vertical-align: middle;
            font-size: 0.82rem;
            border-bottom: 1px solid var(--gray-100);
            line-height: 1.2;
        }

        /* Modern Buttons */
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
            transition: background 0.25s ease, box-shadow 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-modern-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 8px 15px -3px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Status Badges */
        .status-badge-modern {
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .status-active {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid #86efac;
        }

        .status-inactive {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .role-badge-modern {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #fcd34d;
        }

        .team-lead-badge-modern {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            padding: 0.28rem 0.6rem;
            border-radius: 8px;
            font-size: 0.74rem;
            font-weight: 600;
            border: 1px solid #93c5fd;
        }

        /* Profile Images */
        .profile-img-modern {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 2px solid white;
        }

        .profile-placeholder {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-400);
            border: 2px solid white;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--purple-500) 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .form-control, .form-select {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            background-color: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        tbody tr {
            animation: fadeInUp 0.3s ease forwards;
        }
    </style>
</head>
<body>
    @include('admin.Components.sidebar')

    <div class="main-container">
        @include('admin.Components.header')

        <div class="page-hero mt-2">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-users me-2"></i>Employee Management
                </h1>
                <p class="page-subtitle">Manage your team members, roles, and assignments efficiently.</p>
            </div>

            <button type="button" class="btn-modern-primary" data-bs-toggle="modal" data-bs-target="#employeeModal" onclick="resetForm()">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Employee</span>
            </button>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-lg-6">
                <div class="stat-card-modern">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-number">{{ $totalEmployees }}</p>
                            <p class="stat-label">Total Employees</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-2xl">
                            <i class="fas fa-users fa-2x text-blue-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="stat-card-modern active-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-number text-green-600">{{ $activeEmployees }}</p>
                            <p class="stat-label">Active Employees</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-2xl">
                            <i class="fas fa-user-check fa-2x text-green-500"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center shadow-sm border-0 rounded-3 mb-4">
                <i class="fas fa-check-circle me-2 fa-lg"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 rounded-3 mb-4">
                <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="table-card">
            <div class="p-4 border-bottom bg-light">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3 border-gray-200">
                        <i class="fas fa-search text-primary-500"></i>
                    </span>
                    <input
                        type="text"
                        id="employeeSearchInput"
                        class="form-control border-start-0"
                        placeholder="Search by name, email, phone, role, address, or team lead..."
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th><i class="fas fa-image me-1"></i> Photo</th>
                            <th><i class="fas fa-user me-1"></i> Name</th>
                            <th><i class="fas fa-envelope me-1"></i> Email</th>
                            <th><i class="fas fa-phone me-1"></i> Phone</th>
                            <th><i class="fas fa-briefcase me-1"></i> Role</th>
                            <th><i class="fas fa-map-marker-alt me-1"></i> Address</th>
                            <th><i class="fas fa-user-tie me-1"></i> Team Lead</th>
                            <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                            <th class="text-end"><i class="fas fa-cogs me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        @forelse($employees as $employee)
                            <tr>
                                <td>
                                    @if($employee->profile_photo)
                                        <img src="{{ asset('storage/' . ltrim(str_replace('Uploads/profile_photos/', 'profile_photos/', $employee->profile_photo), 'storage/')) }}"
                                             alt="{{ $employee->name }}"
                                             class="profile-img-modern">
                                    @else
                                        <div class="profile-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-gray-900">{{ $employee->name }}</div>
                                </td>
                                <td>
                                    <div class="text-gray-500 text-sm">
                                        {{ $employee->email }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-gray-600 fw-medium">
                                        {{ $employee->phone }}
                                    </div>
                                </td>
                                <td>
                                    @if($employee->employee_role)
                                        <span class="role-badge-modern">
                                            {{ $employee->employee_role }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm italic">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-gray-500 text-sm" style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $employee->address }}">
                                        {{ $employee->address ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    @if($employee->teamLead)
                                        <span class="team-lead-badge-modern">
                                            <i class="fas fa-user-tie me-1"></i>{{ $employee->teamLead->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm italic">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employee->trashed())
                                        <span class="status-badge-modern status-inactive">
                                            <i class="fas fa-times-circle"></i> Inactive
                                        </span>
                                    @else
                                        <span class="status-badge-modern status-active">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button class="btn btn-warning btn-sm text-white shadow-sm" style="border-radius: 8px;"
                                                onclick="editEmployee({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->email }}', '{{ $employee->phone }}', '{{ $employee->employee_role ?? '' }}', '{{ $employee->address ?? '' }}', '{{ $employee->team_lead_id }}', '{{ $employee->profile_photo ? asset('storage/' . ltrim(str_replace('Uploads/profile_photos/', 'profile_photos/', $employee->profile_photo), 'storage/')) : '' }}')"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if($employee->trashed())
                                            <form action="{{ route('admin.employees.activate', $employee->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm text-white shadow-sm" style="border-radius: 8px;" title="Activate">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.employees.deactivate', $employee->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm text-white shadow-sm" style="border-radius: 8px;" title="Deactivate">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-gray-400">
                                        <div class="p-4 bg-gray-50 rounded-full mb-3">
                                            <i class="fas fa-users fa-3x text-gray-300"></i>
                                        </div>
                                        <p class="mb-0 fw-medium">No employees found in the system.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="employeeModalLabel">
                        <i class="fas fa-user-plus"></i> Add Employee
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <form id="employeeForm" action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">
                        <input type="hidden" id="employee_id" name="employee_id">

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 border-gray-200">
                                        <i class="fas fa-user text-primary-500"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 border-gray-200">
                                        <i class="fas fa-envelope text-primary-500"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0" id="email" name="email" value="{{ old('email') }}" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" placeholder="john@example.com">
                                </div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                             <div class="col-12" id="passwordFieldContainer" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 rounded-start-3 border-gray-200">
                                                <i class="fas fa-lock text-primary-500"></i>
                                            </span>
                                            <input type="password" class="form-control border-start-0" id="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep current">
                                        </div>
                                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 border-gray-200">
                                        <i class="fas fa-phone text-primary-500"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="phone" name="phone" value="{{ old('phone') }}" required pattern="^[6-9]\d{9}$" placeholder="9876543210">
                                </div>
                                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="employee_role" class="form-label">Role / Designation</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 border-gray-200">
                                        <i class="fas fa-briefcase text-primary-500"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="employee_role" name="employee_role" value="{{ old('employee_role') }}" placeholder="e.g. Sales Executive">
                                </div>
                                @error('employee_role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="team_lead_id" class="form-label">Team Lead</label>
                                <select class="form-select" id="team_lead_id" name="team_lead_id" required>
                                    <option value="">Select Team Lead</option>
                                    @foreach($teamleads as $teamlead)
                                        <option value="{{ $teamlead->id }}" {{ old('team_lead_id', isset($employee) && $employee->team_lead_id == $teamlead->id ? 'selected' : '') }}>
                                            {{ $teamlead->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_lead_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="profile_photo" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                                @error('profile_photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter complete residential address">{{ old('address') }}</textarea>
                                @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <label class="form-label d-block text-muted small">Preview</label>
                                    <img id="photo_preview" src="" alt="Preview" class="rounded-3 border shadow-sm" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="designation" value="employee">

                        <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                            <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-modern-primary px-4" id="submitButton">
                                <i class="fas fa-save me-2"></i> Save Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('employeeForm').reset();
            document.getElementById('employeeForm').action = "{{ route('admin.employees.store') }}";
            document.getElementById('method').value = 'POST';
            document.getElementById('employee_id').value = '';
            document.getElementById('photo_preview').style.display = 'none';
            document.getElementById('passwordFieldContainer').style.display = 'none';
            document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Add Employee';
            document.getElementById('employeeModalLabel').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add Employee';
        }

        function editEmployee(id, name, email, phone, employee_role, address, team_lead_id, photo) {
            document.getElementById('employeeForm').action = "{{ url('admin/employees') }}/" + id;
            document.getElementById('method').value = 'POST';
            document.getElementById('employee_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('employee_role').value = employee_role;
            document.getElementById('address').value = address;
            document.getElementById('team_lead_id').value = team_lead_id;

            // Show password field in edit mode
            document.getElementById('passwordFieldContainer').style.display = 'block';

            if (photo) {
                document.getElementById('photo_preview').src = photo;
                document.getElementById('photo_preview').style.display = 'block';
            } else {
                document.getElementById('photo_preview').style.display = 'none';
            }

            document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Update Employee';
            document.getElementById('employeeModalLabel').innerHTML = '<i class="fas fa-user-edit me-2"></i>Update Employee';

            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        }

        // Photo preview functionality
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo_preview').src = e.target.result;
                    document.getElementById('photo_preview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Dynamic table search
        document.getElementById('employeeSearchInput')?.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#employeeTableBody tr');

            rows.forEach((row) => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(query) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
