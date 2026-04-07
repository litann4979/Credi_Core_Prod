<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Team Lead Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #f97316; 
            --primary-hover: #ea580c;
            --secondary-bg: #f3f4f6;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --white: #ffffff;
            --border-color: #e5e7eb;
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

        /* Content Card */
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

        /* Buttons */
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
        .btn-primary-custom:hover { background: var(--primary-hover); color: white; }

        /* Table */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom th {
            background: #f9fafb; color: var(--text-muted); font-weight: 600;
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;
            padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); white-space: nowrap;
        }
        .table-custom td {
            padding: 0.5rem 1rem; border-bottom: 1px solid var(--border-color);
            vertical-align: middle; color: var(--text-dark); font-size: 0.875rem; white-space: nowrap;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tr:hover td { background-color: #fff7ed; }

        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-avatar, .user-avatar-placeholder { width: 36px; height: 36px; border-radius: 50%; }
        .user-avatar { object-fit: cover; border: 1px solid var(--border-color); }
        .user-avatar-placeholder { background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; }
        .user-info h6 { margin: 0; font-weight: 600; font-size: 0.875rem; color: var(--text-dark); }
        .user-info span { font-size: 0.75rem; color: var(--text-muted); }

        .badge-custom { padding: 3px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
        .badge-active { background: #ecfdf5; color: #047857; }
        .badge-inactive { background: #fef2f2; color: #b91c1c; }

        /* Actions */
        .action-buttons-container { display: flex; justify-content: flex-end; gap: 6px; }
        .action-btn {
            width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center;
            border: 1px solid transparent; cursor: pointer; transition: all 0.2s; background: transparent; font-size: 0.875rem;
        }
        .btn-view { color: #6b7280; } .btn-view:hover { background: #f3f4f6; color: #1f2937; }
        .btn-salary { color: #059669; } .btn-salary:hover { background: #ecfdf5; color: #047857; }
        .btn-edit { color: #f97316; } .btn-edit:hover { background: #fff7ed; color: #c2410c; }
        .btn-status-on { color: #10b981; } .btn-status-on:hover { background: #ecfdf5; color: #047857; }
        .btn-status-off { color: #ef4444; } .btn-status-off:hover { background: #fef2f2; color: #b91c1c; }

        .salary-input { text-align: right; font-weight: 600; }

        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    @include('hr.Components.sidebar')

    <div class="main-content">
        @include('hr.Components.header')

        <div class="page-wrapper">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="fas fa-users-cog"></i></div>
                    <div class="stat-info">
                        <h3>{{ $totalTeamleads }}</h3>
                        <p>Total Team Leads</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3>{{ $activeTeamleads }}</h3>
                        <p>Active Team Leads</p>
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

            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="card-title"><i class="fas fa-list text-warning"></i> Team Lead List</h5>
                    <button type="button" class="btn-primary-custom" onclick="openAddModal()">
                        <i class="fas fa-plus me-1"></i> Add Team Lead
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Head</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamleads as $tl)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            @if($tl->profile_photo)
                                                <img src="{{ asset('storage/' . ltrim($tl->profile_photo,'storage/')) }}" alt="" class="user-avatar">
                                            @else
                                                <div class="user-avatar-placeholder">{{ strtoupper(substr($tl->name, 0, 1)) }}</div>
                                            @endif
                                            <div class="user-info">
                                                <h6>{{ $tl->name }}</h6>
                                                <span>{{ $tl->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column text-muted small" style="font-size: 0.75rem;">
                                            <span><i class="fas fa-phone me-1"></i> {{ $tl->phone }}</span>
                                            @if($tl->dob)
                                                <span><i class="fas fa-birthday-cake me-1"></i> {{ \Carbon\Carbon::parse($tl->dob)->format('d M') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><span class="text-muted small">{{ $tl->creator ? $tl->creator->name : 'N/A' }}</span></td>
                                    <td>
                                        @if($tl->trashed())
                                            <span class="badge badge-custom badge-inactive">Inactive</span>
                                        @else
                                            <span class="badge badge-custom badge-active">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="action-buttons-container">
                                            <button class="action-btn btn-view" onclick="viewTeamLead({{ $tl->id }})" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button class="action-btn btn-salary" onclick="openSalaryModal({{ $tl->id }})" title="Salary">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>

                                            <button class="action-btn btn-edit" 
                                                onclick="editTeamLead(
                                                    {{ $tl->id }}, '{{ addslashes($tl->name) }}', '{{ $tl->email }}', '{{ $tl->phone }}', '{{ addslashes($tl->address ?? '') }}', '{{ $tl->creator ? $tl->creator->id : '' }}', '{{ $tl->profile_photo ? asset('storage/' . ltrim($tl->profile_photo, 'storage/')) : '' }}', '{{ $tl->dob ? \Carbon\Carbon::parse($tl->dob)->format('Y-m-d') : '' }}'
                                                )" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>

                                            @if($tl->trashed())
                                                <form action="{{ route('hr.teamlead.activate', $tl->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="action-btn btn-status-on" title="Activate"><i class="fas fa-toggle-off"></i></button>
                                                </form>
                                            @else
                                                <form action="{{ route('hr.teamlead.deactivate', $tl->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="action-btn btn-status-off" title="Deactivate"><i class="fas fa-power-off"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted small">No Team Leads found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="teamleadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="teamleadModalLabel">Add Team Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="teamleadForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="method_field" name="_method" value="POST">
                        <input type="hidden" id="teamlead_id" name="teamlead_id">
                        <input type="hidden" name="designation" value="team_lead">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" class="form-control" id="name" name="name" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" required></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" class="form-control" id="phone" name="phone" required></div>
                            <div class="col-md-6"><label class="form-label">Head</label>
                                <select class="form-select" id="operation_id" name="operation_id" required>
                                    <option value="">Select Operations Head</option>
                                    @foreach($operations as $op)
                                        <option value="{{ $op->id }}">{{ $op->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label">DOB</label><input type="date" class="form-control" id="dob" name="dob"></div>
                            <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" id="address" name="address" rows="2"></textarea></div>
                            <div class="col-md-6" id="passwordFieldContainer" style="display:none;"><label class="form-label">Password</label><input type="password" class="form-control" id="password" name="password"></div>
                            <div class="col-md-6"><label class="form-label">Photo</label><div class="d-flex gap-2"><img id="photo_preview" style="width:40px;height:40px;border-radius:50%;display:none;"><input type="file" class="form-control" id="photo" name="photo"></div></div>
                            <div class="col-12" id="documentUploadSection" style="display:none;"><label class="form-label">Documents</label><input type="file" class="form-control" name="documents[]" multiple></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-custom" id="submitButton">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewTeamLeadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div id="viewModalLoader" class="text-center"><div class="spinner-border text-primary"></div></div>
                    <div id="viewModalContent" style="display:none;">
                        <div class="text-center mb-3">
                            <img id="view_avatar" src="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                            <h5 class="mt-2 fw-bold" id="view_name"></h5>
                            <span class="badge badge-custom badge-role">Team Lead</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom"><span>Email</span><span class="fw-bold" id="view_email"></span></div>
                        <div class="d-flex justify-content-between py-2 border-bottom"><span>Phone</span><span class="fw-bold" id="view_phone"></span></div>
                        <div class="d-flex justify-content-between py-2 border-bottom"><span>Head</span><span class="fw-bold" id="view_op"></span></div>
                        <div class="d-flex justify-content-between py-2 border-bottom"><span>DOB</span><span class="fw-bold" id="view_dob"></span></div>
                        <div class="mt-3"><h6 class="small fw-bold">DOCUMENTS</h6><div id="view_documents_list"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="salaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark"><i class="fas fa-file-invoice-dollar text-success me-2"></i> Salary Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="salaryLoader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>

                    <form id="salaryForm" style="display:none;">
                        @csrf
                        <input type="hidden" id="salary_user_id">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded border shadow-sm">
                            <div><small class="text-muted">Employee</small><h5 class="mb-0 fw-bold text-dark" id="salary_emp_name">--</h5></div>
                            <div class="text-end"><small class="text-muted">Role</small><div class="text-dark fw-medium" id="salary_emp_role">--</div></div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6 border-end">
                                <h6 class="text-success small fw-bold border-bottom pb-2">EARNINGS</h6>
                                @foreach(['basic_salary'=>'Basic Salary', 'hra'=>'HRA', 'conveyance_allowance'=>'Conveyance', 'medical_allowance'=>'Medical', 'special_allowance'=>'Special', 'performance_bonus'=>'Bonus', 'other_earnings'=>'Other'] as $field => $label)
                                <div class="mb-2 row align-items-center">
                                    <label class="col-6 col-form-label col-form-label-sm text-secondary">{{ $label }}</label>
                                    <div class="col-6"><input type="number" step="0.01" name="{{ $field }}" class="form-control form-control-sm salary-input salary-earn" placeholder="0.00"></div>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h6 class="text-danger small fw-bold border-bottom pb-2">DEDUCTIONS</h6>
                                @foreach(['pf_employee'=>'Provident Fund', 'esi_employee'=>'ESI', 'professional_tax'=>'Prof. Tax', 'tds'=>'TDS / Tax'] as $field => $label)
                                <div class="mb-2 row align-items-center">
                                    <label class="col-6 col-form-label col-form-label-sm text-secondary">{{ $label }}</label>
                                    <div class="col-6"><input type="number" step="0.01" name="{{ $field }}" class="form-control form-control-sm salary-input salary-ded" placeholder="0.00"></div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top bg-light rounded p-2">
                            <div class="col-4 text-center border-end"><small class="text-muted">Gross</small><span class="h5 text-success fw-bold d-block" id="disp_gross">₹ 0</span></div>
                            <div class="col-4 text-center border-end"><small class="text-muted">Deductions</small><span class="h5 text-danger fw-bold d-block" id="disp_deductions">₹ 0</span></div>
                            <div class="col-4 text-center"><small class="text-muted">Net Pay</small><span class="h4 text-primary fw-bold d-block" id="disp_net">₹ 0</span></div>
                        </div>

                        <div class="mt-3">
                            <h6 class="small fw-bold mb-2">BANK INFO</h6>
                            <div class="row g-2">
                                <div class="col-md-3"><input type="text" name="payment_mode" class="form-control form-control-sm" placeholder="Mode"></div>
                                <div class="col-md-3"><input type="text" name="bank_name" class="form-control form-control-sm" placeholder="Bank Name"></div>
                                <div class="col-md-3"><input type="text" name="account_number" class="form-control form-control-sm" placeholder="Account No"></div>
                                <div class="col-md-3"><input type="text" name="ifsc_code" class="form-control form-control-sm" placeholder="IFSC Code"></div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold"><i class="fas fa-save me-1"></i> Save Structure</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Constants for Routes
        const routes = {
            store: "{{ route('hr.teamlead.store') }}",
            updateBase: "{{ url('hr/teamlead') }}",
        };

        // --- Team Lead Add/Edit Logic ---
        function openAddModal() {
            const form = document.getElementById('teamleadForm');
            form.reset();
            form.action = routes.store;
            document.getElementById('method_field').value = 'POST';
            document.getElementById('teamleadModalLabel').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add Team Lead';
            document.getElementById('submitButton').innerText = 'Save Team Lead';
            document.getElementById('passwordFieldContainer').style.display = 'none';
            document.getElementById('documentUploadSection').style.display = 'none';
            document.getElementById('photo_preview').style.display = 'none';
            new bootstrap.Modal(document.getElementById('teamleadModal')).show();
        }

        function editTeamLead(id, name, email, phone, address, op_id, photo, dob) {
            const form = document.getElementById('teamleadForm');
            form.action = `${routes.updateBase}/${id}/update`; // Corrected update path
            document.getElementById('method_field').value = 'POST';
            document.getElementById('teamlead_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('address').value = address;
            document.getElementById('operation_id').value = op_id;
            document.getElementById('dob').value = dob;

            document.getElementById('teamleadModalLabel').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Team Lead';
            document.getElementById('submitButton').innerText = 'Update Team Lead';
            document.getElementById('passwordFieldContainer').style.display = 'block';
            document.getElementById('documentUploadSection').style.display = 'block';

            if(photo) {
                document.getElementById('photo_preview').src = photo;
                document.getElementById('photo_preview').style.display = 'block';
            } else {
                document.getElementById('photo_preview').style.display = 'none';
            }
            new bootstrap.Modal(document.getElementById('teamleadModal')).show();
        }

        // --- View Logic ---
        function viewTeamLead(id) {
            const modal = new bootstrap.Modal(document.getElementById('viewTeamLeadModal'));
            modal.show();
            document.getElementById('viewModalLoader').style.display = 'block';
            document.getElementById('viewModalContent').style.display = 'none';

            fetch(`${routes.updateBase}/${id}/details`)
                .then(res => res.json())
                .then(data => {
                    const tl = data.data;
                    document.getElementById('view_name').innerText = tl.name;
                    document.getElementById('view_email').innerText = tl.email;
                    document.getElementById('view_phone').innerText = tl.phone;
                    document.getElementById('view_op').innerText = data.operation_name;
                    document.getElementById('view_dob').innerText = data.formatted_dob;
                    document.getElementById('view_avatar').src = data.profile_url || `https://ui-avatars.com/api/?name=${tl.name}&background=f97316&color=fff`;

                    const docList = document.getElementById('view_documents_list');
                    docList.innerHTML = '';
                    if(data.documents && data.documents.length > 0) {
                        data.documents.forEach(doc => {
                            docList.innerHTML += `<div class="d-flex justify-content-between p-2 mb-1 bg-light rounded small"><span class="text-truncate" style="max-width:150px;">${doc.name}</span><a href="${doc.url}" target="_blank">Download</a></div>`;
                        });
                    } else {
                        docList.innerHTML = '<span class="text-muted small">No documents.</span>';
                    }
                    document.getElementById('viewModalLoader').style.display = 'none';
                    document.getElementById('viewModalContent').style.display = 'block';
                });
        }

        // --- Salary Logic ---
        function openSalaryModal(userId) {
            const modal = new bootstrap.Modal(document.getElementById('salaryModal'));
            modal.show();
            
            const form = document.getElementById('salaryForm');
            document.getElementById('salaryLoader').style.display = 'block';
            form.style.display = 'none';
            form.reset();
            document.getElementById('salary_user_id').value = userId;
            
            // Reset totals
            ['disp_gross', 'disp_deductions', 'disp_net'].forEach(id => document.getElementById(id).innerText = '₹ 0');

            fetch(`/hr/teamlead/${userId}/salary`)
                .then(res => res.json())
                .then(res => {
                    if(res.success) {
                        document.getElementById('salary_emp_name').innerText = res.user_name;
                        document.getElementById('salary_emp_role').innerText = res.designation || 'Team Lead';

                        if(res.exists) {
                            const d = res.data;
                            const setV = (n, v) => { const e = form.querySelector(`[name="${n}"]`); if(e) e.value = v; };
                            
                            setV('basic_salary', d.basic_salary); setV('hra', d.hra); setV('conveyance_allowance', d.conveyance_allowance);
                            setV('medical_allowance', d.medical_allowance); setV('special_allowance', d.special_allowance);
                            setV('performance_bonus', d.performance_bonus); setV('other_earnings', d.other_earnings);
                            setV('pf_employee', d.pf_employee); setV('esi_employee', d.esi_employee);
                            setV('professional_tax', d.professional_tax); setV('tds', d.tds);
                            setV('payment_mode', d.payment_mode); setV('bank_name', d.bank_name);
                            setV('account_number', d.account_number); setV('ifsc_code', d.ifsc_code);
                            
                            calcTotals();
                        }
                    }
                    document.getElementById('salaryLoader').style.display = 'none';
                    form.style.display = 'block';
                })
                .catch(err => { console.error(err); alert('Failed to load data'); modal.hide(); });
        }

        function calcTotals() {
            let gross = 0, ded = 0;
            document.querySelectorAll('.salary-earn').forEach(e => gross += parseFloat(e.value)||0);
            document.querySelectorAll('.salary-ded').forEach(e => ded += parseFloat(e.value)||0);
            
            document.getElementById('disp_gross').innerText = '₹ ' + gross.toFixed(2);
            document.getElementById('disp_deductions').innerText = '₹ ' + ded.toFixed(2);
            document.getElementById('disp_net').innerText = '₹ ' + (gross - ded).toFixed(2);
        }

        document.querySelectorAll('.salary-input').forEach(i => i.addEventListener('input', calcTotals));

        document.getElementById('salaryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const uid = document.getElementById('salary_user_id').value;
            const fd = new FormData(this);
            const dt = {}; fd.forEach((v,k)=>dt[k]=v);
            
            const btn = this.querySelector('button[type="submit"]');
            const txt = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...'; btn.disabled = true;

            fetch(`/hr/teamlead/${uid}/salary`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(dt)
            })
            .then(r => r.json())
            .then(d => {
                if(d.success) { alert('Salary Structure Saved!'); bootstrap.Modal.getInstance(document.getElementById('salaryModal')).hide(); }
                else alert('Error saving salary.');
            })
            .catch(e => { console.error(e); alert('System Error'); })
            .finally(() => { btn.innerHTML = txt; btn.disabled = false; });
        });

        // Photo Preview for Add/Edit
        document.getElementById('photo').addEventListener('change', function(e) {
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
    </script>
</body>
</html>