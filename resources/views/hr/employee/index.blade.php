<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Theme Colors */
            --primary-color: #f97316; 
            --primary-hover: #ea580c;
            --secondary-bg: #f3f4f6;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --white: #ffffff;
            --border-color: #e5e7eb;
            
            /* Dimensions */
            --sidebar-width: 280px;
            --header-height: 80px;
        }

        body {
            background-color: var(--secondary-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            margin: 0;
            overflow-x: hidden;
        }

        /* Layout Structure */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .page-wrapper {
            padding: 1.5rem 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-icon.primary { background: #fff7ed; color: var(--primary-color); }
        .stat-icon.success { background: #ecfdf5; color: #10b981; }

        .stat-info h3 { font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1.2; }
        .stat-info p { margin: 0; color: var(--text-muted); font-size: 0.875rem; }

        /* Main Content Card */
        .content-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header-custom {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--white);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background: var(--primary-hover);
            color: white;
        }

        /* Compact Table Styling */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom th {
            background: #f9fafb;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem; /* Compact Header */
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .table-custom td {
            padding: 0.5rem 1rem; /* Reduced Padding for Slimmer Rows */
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-dark);
            font-size: 0.875rem;
            white-space: nowrap; /* Prevent text wrapping */
        }

        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tr:hover td { background-color: #fff7ed; }

        /* User Cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px; /* Slightly smaller avatar */
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .user-avatar-placeholder {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .user-info { display: flex; flex-direction: column; justify-content: center; }
        .user-info h6 { margin: 0; font-weight: 600; font-size: 0.875rem; color: var(--text-dark); }
        .user-info span { font-size: 0.75rem; color: var(--text-muted); }

        /* Badges */
        .badge-custom {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-active { background: #ecfdf5; color: #047857; }
        .badge-inactive { background: #fef2f2; color: #b91c1c; }
        .badge-role { background: #eff6ff; color: #1d4ed8; }

        /* Action Buttons - Horizontal Layout */
        .action-buttons-container {
            display: flex;
            justify-content: flex-end;
            gap: 6px; /* Space between buttons */
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            font-size: 0.875rem;
        }

        .btn-view { color: #6b7280; }
        .btn-view:hover { background: #f3f4f6; color: #1f2937; }

        .btn-edit { color: #f97316; }
        .btn-edit:hover { background: #fff7ed; color: #c2410c; }

        .btn-status-on { color: #10b981; }
        .btn-status-on:hover { background: #ecfdf5; color: #047857; }

        .btn-status-off { color: #ef4444; }
        .btn-status-off:hover { background: #fef2f2; color: #b91c1c; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
        }

        .btn-salary { color: #059669; }
    .btn-salary:hover { background: #ecfdf5; color: #047857; }
    
    /* Salary Modal Specifics */
    .salary-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }
    .salary-total-box {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #e5e7eb;
    }
    .salary-input { text-align: right; font-weight: 600; }
    </style>
</head>
<body>

    @include('hr.Components.sidebar')

    <div class="main-content">
        @include('hr.Components.header')

        <div class="page-wrapper">
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3>{{ $totalEmployees }}</h3>
                        <p>Total Employees</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success"><i class="fas fa-user-check"></i></div>
                    <div class="stat-info">
                        <h3>{{ $activeEmployees }}</h3>
                        <p>Active Employees</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="card-title">
                        <i class="fas fa-list text-warning"></i> Employee List
                    </h5>
                    <button type="button" class="btn-primary-custom" onclick="openAddModal()">
                        <i class="fas fa-plus me-1"></i> Add Employee
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Role</th>
                                <th>Contact</th>
                                <th>Team Lead</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            @if($employee->profile_photo)
                                                <img src="{{ asset('storage/' . ltrim($employee->profile_photo, 'storage/')) }}" alt="" class="user-avatar">
                                            @else
                                                <div class="user-avatar-placeholder">
                                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="user-info">
                                                <h6>{{ $employee->name }}</h6>
                                                <span>{{ $employee->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-custom badge-role">
                                            {{ $employee->employee_role ?? 'Employee' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column text-muted small" style="font-size: 0.75rem;">
                                            <span><i class="fas fa-phone me-1"></i> {{ $employee->phone }}</span>
                                            @if($employee->dob)
                                                <span><i class="fas fa-birthday-cake me-1"></i> {{ $employee->dob->format('d M') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($employee->teamLead)
                                            <span class="d-flex align-items-center text-dark fw-medium" style="font-size: 0.8rem;">
                                                <i class="fas fa-user-tie text-muted me-1 small"></i>
                                                {{ $employee->teamLead->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->trashed())
                                            <span class="badge badge-custom badge-inactive">Inactive</span>
                                        @else
                                            <span class="badge badge-custom badge-active">Active</span>
                                        @endif
                                    </td>
                                   <td class="text-end">
    <div class="action-buttons-container">
        <button class="action-btn btn-view" onclick="viewEmployee({{ $employee->id }})" title="View Profile">
            <i class="fas fa-eye"></i>
        </button>

        <button class="action-btn btn-salary" onclick="openSalaryModal({{ $employee->id }})" title="Manage Salary">
            <i class="fas fa-money-bill-wave"></i>
        </button>

        <button class="action-btn btn-edit" 
            onclick="editEmployee(
                {{ $employee->id }}, 
                '{{ addslashes($employee->name) }}', 
                '{{ $employee->email }}', 
                '{{ $employee->phone }}', 
                '{{ $employee->employee_role ?? '' }}', 
                '{{ addslashes($employee->address ?? '') }}', 
                '{{ $employee->team_lead_id }}', 
                '{{ $employee->profile_photo_url }}', 
                '{{ $employee->dob ? $employee->dob->format('Y-m-d') : '' }}'
            )"
            title="Edit">
            <i class="fas fa-pencil-alt"></i>
        </button>

        @if($employee->trashed())
            <form action="{{ route('hr.employees.activate', $employee->id) }}" method="POST">
                @csrf
                <button type="submit" class="action-btn btn-status-on" title="Activate"><i class="fas fa-toggle-off"></i></button>
            </form>
        @else
            <form action="{{ route('hr.employees.deactivate', $employee->id) }}" method="POST">
                @csrf
                <button type="submit" class="action-btn btn-status-off" title="Deactivate"><i class="fas fa-power-off"></i></button>
            </form>
        @endif
    </div>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted small">
                                            No employees found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="employeeForm" action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="method_field" name="_method" value="POST">
                        <input type="hidden" id="employee_id" name="employee_id">
                        <input type="hidden" name="designation" value="employee">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="john@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" required placeholder="9876543210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employee Role</label>
                                <input type="text" class="form-control" id="employee_role" name="employee_role" placeholder="e.g. Developer">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Team Lead</label>
                                <select class="form-select" id="team_lead_id" name="team_lead_id" required>
                                    <option value="">Select Team Lead</option>
                                    @foreach($teamleads as $teamlead)
                                        <option value="{{ $teamlead->id }}">{{ $teamlead->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" max="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full residential address"></textarea>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <div class="col-md-6" id="passwordFieldContainer" style="display:none;">
                                <label class="form-label">Password <span class="text-muted small fw-normal">(Leave empty to keep current)</span></label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Profile Photo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="photo_preview" src="" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; display: none; border: 1px solid #ddd;">
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                                </div>
                            </div>

                            <div class="col-12" id="documentUploadSection" style="display:none;">
                                <div class="p-3 bg-light rounded-3 border border-dashed border-secondary">
                                    <label class="form-label fw-bold text-primary mb-2">
                                        <i class="fas fa-cloud-upload-alt me-2"></i> Upload Documents
                                    </label>
                                    <input type="file" class="form-control" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                    <small class="text-muted d-block mt-1">
                                        Select multiple files (ID Proof, Resume, Contract). Max 10MB per file.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-custom" id="submitButton">
                                <i class="fas fa-save"></i> Save Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Employee Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="viewModalLoader" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    
                    <div id="viewModalContent" style="display: none;">
                        <div class="text-center mb-4">
                            <img id="view_avatar" src="" alt="Avatar" 
                                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color);">
                            <h5 class="mt-3 mb-0 fw-bold" id="view_name"></h5>
                            <span class="badge badge-custom badge-role mt-1" id="view_role"></span>
                        </div>

                        <div class="detail-row" style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; display: flex;">
                            <span style="width: 140px; color: #6b7280; font-weight: 500;">Email</span>
                            <span style="font-weight: 600;" id="view_email"></span>
                        </div>
                        <div class="detail-row" style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; display: flex;">
                            <span style="width: 140px; color: #6b7280; font-weight: 500;">Phone</span>
                            <span style="font-weight: 600;" id="view_phone"></span>
                        </div>
                        <div class="detail-row" style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; display: flex;">
                            <span style="width: 140px; color: #6b7280; font-weight: 500;">Team Lead</span>
                            <span style="font-weight: 600;" id="view_tl"></span>
                        </div>
                        <div class="detail-row" style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; display: flex;">
                            <span style="width: 140px; color: #6b7280; font-weight: 500;">DOB</span>
                            <span style="font-weight: 600;" id="view_dob"></span>
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="text-muted fw-bold small text-uppercase mb-2">Documents</h6>
                            <div id="view_documents_list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="salaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>Salary Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="salaryLoader" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                
                <form id="salaryForm" method="POST" style="display:none;">
                    @csrf
                    <div class="alert alert-info py-2 small"><i class="fas fa-info-circle me-1"></i> Managing Salary for: <strong id="salaryEmployeeName"></strong></div>

                    <div class="row">
                        <div class="col-md-6 border-end">
                            <div class="salary-section-title text-success">Earnings (Monthly)</div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">Basic Salary</label>
                                <div class="col-6"><input type="number" step="0.01" name="basic_salary" class="form-control form-control-sm salary-input calc-earn" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">HRA</label>
                                <div class="col-6"><input type="number" step="0.01" name="hra" class="form-control form-control-sm salary-input calc-earn" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">Conveyance</label>
                                <div class="col-6"><input type="number" step="0.01" name="conveyance_allowance" class="form-control form-control-sm salary-input calc-earn" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">Medical Allow.</label>
                                <div class="col-6"><input type="number" step="0.01" name="medical_allowance" class="form-control form-control-sm salary-input calc-earn" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">Special Allow.</label>
                                <div class="col-6"><input type="number" step="0.01" name="special_allowance" class="form-control form-control-sm salary-input calc-earn" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">Other Earnings</label>
                                <div class="col-6"><input type="number" step="0.01" name="other_earnings" class="form-control form-control-sm salary-input calc-earn" placeholder="0.00"></div>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <div class="salary-section-title text-danger">Deductions (Monthly)</div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">PF (Employee)</label>
                                <div class="col-6"><input type="number" step="0.01" name="pf_employee" class="form-control form-control-sm salary-input calc-ded" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">ESI (Employee)</label>
                                <div class="col-6"><input type="number" step="0.01" name="esi_employee" class="form-control form-control-sm salary-input calc-ded" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">Prof. Tax</label>
                                <div class="col-6"><input type="number" step="0.01" name="professional_tax" class="form-control form-control-sm salary-input calc-ded" placeholder="0.00"></div>
                            </div>
                            <div class="mb-2 row align-items-center">
                                <label class="col-6 col-form-label small">TDS / Tax</label>
                                <div class="col-6"><input type="number" step="0.01" name="tds" class="form-control form-control-sm salary-input calc-ded" placeholder="0.00"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            <div class="salary-total-box">
                                <small class="d-block text-muted">GROSS</small>
                                <span class="h5 text-success fw-bold" id="disp_gross">₹ 0</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="salary-total-box">
                                <small class="d-block text-muted">DEDUCTIONS</small>
                                <span class="h5 text-danger fw-bold" id="disp_ded">₹ 0</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="salary-total-box bg-white border-primary">
                                <small class="d-block text-muted">NET SALARY</small>
                                <span class="h4 text-primary fw-bold" id="disp_net">₹ 0</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="salary-section-title">Payment Information</div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small">Mode</label>
                            <select name="payment_mode" class="form-select form-select-sm">
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Account No.</label>
                            <input type="text" name="account_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Structure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const storeRoute = "{{ route('hr.employees.store') }}";
        const updateRouteBase = "{{ url('hr/employees') }}";

        function openAddModal() {
            document.getElementById('employeeForm').reset();
            document.getElementById('employeeForm').action = storeRoute;
            document.getElementById('method_field').value = 'POST';
            document.getElementById('employeeModalLabel').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add Employee';
            document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Add Employee';
            document.getElementById('passwordFieldContainer').style.display = 'none';
            document.getElementById('documentUploadSection').style.display = 'none';
            document.getElementById('photo_preview').style.display = 'none';
            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        }

        function editEmployee(id, name, email, phone, role, address, tl_id, photo, dob) {
            document.getElementById('employeeForm').action = `${updateRouteBase}/${id}`;
            document.getElementById('method_field').value = 'POST';
            document.getElementById('employee_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('employee_role').value = role;
            document.getElementById('address').value = address;
            document.getElementById('team_lead_id').value = tl_id;
            document.getElementById('dob').value = dob;

            document.getElementById('employeeModalLabel').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Employee';
            document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Update Employee';
            document.getElementById('passwordFieldContainer').style.display = 'block';
            document.getElementById('documentUploadSection').style.display = 'block';

            if(photo) {
                document.getElementById('photo_preview').src = photo;
                document.getElementById('photo_preview').style.display = 'block';
            } else {
                document.getElementById('photo_preview').style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        }

        function viewEmployee(id) {
            const modal = new bootstrap.Modal(document.getElementById('viewEmployeeModal'));
            modal.show();
            const loader = document.getElementById('viewModalLoader');
            const content = document.getElementById('viewModalContent');
            loader.style.display = 'block';
            content.style.display = 'none';

            fetch(`${updateRouteBase}/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    const emp = data.data;
                    document.getElementById('view_name').textContent = emp.name;
                    document.getElementById('view_role').textContent = emp.employee_role || 'Employee';
                    document.getElementById('view_email').textContent = emp.email;
                    document.getElementById('view_phone').textContent = emp.phone;
                    document.getElementById('view_tl').textContent = data.team_lead_name;
                    document.getElementById('view_dob').textContent = data.formatted_dob;
                    
                   const avatar = document.getElementById('view_avatar');

                   console.log('Profile URL:', data.profile_url);

avatar.src = data.profile_url 
    ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}&background=f97316&color=fff`;


                    const docList = document.getElementById('view_documents_list');
                    docList.innerHTML = '';
                    if(data.documents && data.documents.length > 0) {
                        data.documents.forEach(doc => {
                            docList.innerHTML += `
                                <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-light border rounded">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-alt text-primary"></i>
                                        <span class="small fw-medium text-truncate" style="max-width: 200px;">${doc.name}</span>
                                    </div>
                                    <a href="${doc.url}" target="_blank" class="btn btn-sm btn-light text-primary"><i class="fas fa-download"></i></a>
                                </div>
                            `;
                        });
                    } else {
                        docList.innerHTML = '<p class="text-muted small text-center my-2">No documents uploaded.</p>';
                    }
                    loader.style.display = 'none';
                    content.style.display = 'block';
                })
                .catch(err => {
                    console.error(err);
                    loader.innerHTML = '<p class="text-danger">Failed to load data.</p>';
                });
        }

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

        function openSalaryModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('salaryModal'));
        modal.show();
        
        const form = document.getElementById('salaryForm');
        const loader = document.getElementById('salaryLoader');
        
        form.style.display = 'none';
        loader.style.display = 'block';
        form.reset(); // Clear old values
        
        // Update Action URL
        form.action = `/hr/employees/${id}/salary`;

        // Fetch Data
        fetch(`/hr/employees/${id}/salary`)
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    document.getElementById('salaryEmployeeName').textContent = res.employee_name;
                    const d = res.data;

                    // Fill Inputs (Earnings)
                    form.querySelector('[name="basic_salary"]').value = d.basic_salary || '';
                    form.querySelector('[name="hra"]').value = d.hra || '';
                    form.querySelector('[name="conveyance_allowance"]').value = d.conveyance_allowance || '';
                    form.querySelector('[name="medical_allowance"]').value = d.medical_allowance || '';
                    form.querySelector('[name="special_allowance"]').value = d.special_allowance || '';
                    form.querySelector('[name="other_earnings"]').value = d.other_earnings || '';

                    // Fill Inputs (Deductions)
                    form.querySelector('[name="pf_employee"]').value = d.pf_employee || '';
                    form.querySelector('[name="esi_employee"]').value = d.esi_employee || '';
                    form.querySelector('[name="professional_tax"]').value = d.professional_tax || '';
                    form.querySelector('[name="tds"]').value = d.tds || '';

                    // Fill Bank
                    form.querySelector('[name="payment_mode"]').value = d.payment_mode || 'Bank Transfer';
                    form.querySelector('[name="bank_name"]').value = d.bank_name || '';
                    form.querySelector('[name="account_number"]').value = d.account_number || '';
                    form.querySelector('[name="ifsc_code"]').value = d.ifsc_code || '';

                    calculateSalary(); // Recalculate totals based on fetched data
                    
                    loader.style.display = 'none';
                    form.style.display = 'block';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error fetching salary data');
                modal.hide();
            });
    }

    // Auto Calculation Logic
    function calculateSalary() {
        let gross = 0;
        document.querySelectorAll('.calc-earn').forEach(inp => {
            gross += parseFloat(inp.value) || 0;
        });

        let ded = 0;
        document.querySelectorAll('.calc-ded').forEach(inp => {
            ded += parseFloat(inp.value) || 0;
        });

        let net = gross - ded;

        document.getElementById('disp_gross').textContent = '₹ ' + gross.toFixed(2);
        document.getElementById('disp_ded').textContent = '₹ ' + ded.toFixed(2);
        document.getElementById('disp_net').textContent = '₹ ' + net.toFixed(2);
    }

    // Attach Event Listeners to Inputs
    document.querySelectorAll('.salary-input').forEach(input => {
        input.addEventListener('input', calculateSalary);
    });
    </script>
</body>
</html>