
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teams - Lead Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">
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

        .teams-container {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 32px;
            animation: fadeInDown 0.6s ease-out;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 8px;
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

        .teams-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 32px;
            animation: slideInUp 0.8s ease-out;
        }

        /* Employee Form Section */
        .employee-form-section {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            height: fit-content;
            position: sticky;
            top: 32px;
        }

        .form-header {
            margin-bottom: 24px;
            text-align: center;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .employee-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .required {
            color: #ef4444;
        }

        .form-input {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .photo-upload {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #e5e7eb;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .photo-preview:hover {
            border-color: #3b82f6;
            transform: scale(1.05);
        }

        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            color: #9ca3af;
            font-size: 32px;
        }

        .photo-input {
            display: none;
        }

        .photo-upload-btn {
            padding: 8px 16px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 12px;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .photo-upload-btn:hover {
            background: #e5e7eb;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* Employees Table Section */
        .employees-table-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-header {
            padding: 24px 32px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(29, 78, 216, 0.05));
        }

        .table-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .table-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .table-controls {
            padding: 20px 32px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 2px;
            transition: all 0.3s ease;
        }

        .search-input-wrapper:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: white;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: none;
            background: transparent;
            font-size: 14px;
            color: #1f2937;
        }

        .search-input:focus {
            outline: none;
        }

        .search-input-wrapper:hover i,
        .search-input-wrapper:focus-within i {
            color: #3b82f6;
        }

        .table-stats {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 14px;
            color: #6b7280;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-value {
            font-weight: 600;
            color: #3b82f6;
        }

        .employees-table-wrapper {
            overflow-x: auto;
        }

        .employees-table {
            width: 100%;
            border-collapse: collapse;
        }

        .employees-table th,
        .employees-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
        }

        .employees-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .employees-table td {
            font-size: 14px;
            color: #4b5563;
        }

        .employees-table tr:hover {
            background: rgba(59, 130, 246, 0.02);
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
        }

        .employee-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .employee-avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .designation-badge {
            padding: 4px 12px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(29, 78, 216, 0.1));
            color: #1d4ed8;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .contact-info {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #6b7280;
            font-size: 13px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-edit {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .btn-edit:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: scale(1.1);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
            transform: scale(1.1);
        }

        .empty-state {
            padding: 64px 32px;
            text-align: center;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state-description {
            font-size: 14px;
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

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: white;
            font-size: 32px;
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
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .modal-close-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #28a745;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }

        /* Animations */
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

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .teams-layout {
                grid-template-columns: 350px 1fr;
                gap: 24px;
            }
        }

        @media (max-width: 968px) {
            .teams-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .employee-form-section {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .teams-container {
                padding: 20px;
            }

            .table-controls {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .search-box {
                max-width: none;
            }

            .employees-table th,
            .employees-table td {
                padding: 12px 16px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @include('Opearation.Components.sidebar')

    <div class="main-content">
        @include('Opearation.Components.header', ['title' => 'Teamlead', 'subtitle' => 'Manage your teamleads.'])

        <div class="teams-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Team Management</h1>
                <p class="page-subtitle">Add, edit, and manage your team members efficiently</p>
            </div>

            <!-- Main Layout -->
            <div class="teams-layout">
                <!-- Team Lead Form Section -->
                <div class="employee-form-section">
                    <div class="form-header">
                        <h2 class="form-title" id="formTitle">Add New Team Lead</h2>
                        <p class="form-subtitle">Fill in the details to add a new team lead</p>
                    </div>

                    <form class="employee-form" id="employeeForm" method="POST" action="{{ route('operations.teamlead.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="employeeId" name="id">
                        <input type="hidden" name="designation" value="team_lead">

                        <!-- Profile Photo -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-camera"></i>
                                Profile Photo <span class="text-muted">(Optional)</span>
                            </label>
                            <div class="photo-upload">
                                <div class="photo-preview" onclick="document.getElementById('photoInput').click()">
                                    <div class="photo-placeholder" id="photoPlaceholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <img id="photoPreview" style="display: none;" alt="Preview">
                                </div>
                                <input type="file" id="photoInput" name="photo" class="photo-input" accept="image/*" onchange="handlePhotoUpload(event)">
                                <button type="button" class="photo-upload-btn" onclick="document.getElementById('photoInput').click()">
                                    <i class="fas fa-upload"></i>
                                    Choose Photo
                                </button>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="form-group">
                            <label for="employeeName" class="form-label">
                                <i class="fas fa-user"></i>
                                Full Name <span class="required">*</span>
                            </label>
                            <input type="text" id="employeeName" name="name" class="form-input" placeholder="Enter full name" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="employeeEmail" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email Address <span class="required">*</span>
                            </label>
                            <input type="email" id="employeeEmail" name="email" class="form-input" placeholder="Enter email address" required>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="employeePhone" class="form-label">
                                <i class="fas fa-phone"></i>
                                Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" id="employeePhone" name="phone" class="form-input" placeholder="Enter phone number" required>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="employeeAddress" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Address <span class="text-muted">(Optional)</span>
                            </label>
                            <textarea id="employeeAddress" name="address" class="form-input form-textarea" placeholder="Enter address" rows="3"></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus"></i>
                                Add Team Lead
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Team Leads Table Section -->
                <div class="employees-table-section">
                    <div class="table-header">
                        <h2 class="table-title">Team Leads</h2>
                        <p class="table-subtitle">Manage and view all team leads</p>
                    </div>

                    <div class="table-controls">
                        {{-- <div class="search-box">
                            <div class="search-input-wrapper">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchInput" class="search-input" placeholder="Search by name, email, or phone...">
                            </div>
                        </div> --}}
                        <div class="table-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>Total: <span class="stat-value" id="totalEmployees">{{ $totalTeamleads }}</span></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-user-check"></i>
                                <span>Active: <span class="stat-value" id="activeEmployees">{{ $activeTeamleads }}</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="employees-table-wrapper">
                        <table class="employees-table">
                            <thead>
                                <tr>
                                    <th>Team Lead</th>
                                    <th>Designation</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeesTableBody">
                                @forelse($teamleads as $teamlead)
                                    <tr>
                                        <td>
                                            <div class="employee-info">
                                                <div class="employee-avatar">
                                                    @if($teamlead->profile_photo)
                                                        <img src="{{ asset('storage/' . $teamlead->profile_photo) }}" alt="{{ $teamlead->name }}">
                                                    @else
                                                        <div class="employee-avatar-placeholder">
                                                            {{ strtoupper(substr($teamlead->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="employee-details">
                                                    <div class="employee-name">{{ $teamlead->name }}</div>
                                                    <div class="employee-email">{{ $teamlead->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="designation-badge">{{ $teamlead->designation }}</span>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <i class="fas fa-phone"></i> {{ $teamlead->phone }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <i class="fas fa-map-marker-alt"></i> {{ $teamlead->address ?? 'Not provided' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($teamlead->deleted_at)
                                                <span class="badge bg-danger">Inactive</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="action-btn btn-edit" onclick="editTeamlead({{ $teamlead->id }})" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ $teamlead->deleted_at ? route('operations.teamlead.activate', $teamlead->id) : route('operations.teamlead.deactivate', $teamlead->id) }}" method="POST" class="toggle-form m-0 p-0">
                                                    @csrf
                                                    <label class="switch mb-0">
                                                        <input type="checkbox" onchange="this.form.submit()" {{ !$teamlead->deleted_at ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="empty-state-title">No team leads found</div>
                                            <div class="empty-state-description">Start by adding your first team lead using the form on the left</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal-overlay" id="successModal">
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h3 class="modal-title" id="modalTitle">Team Lead Created Successfully!</h3>
                <p class="modal-message" id="modalMessage">
                    The team lead has been added to your team. A welcome email with login credentials has been sent to their registered email address.
                </p>
                <button class="modal-close-btn" onclick="closeSuccessModal()">
                    <i class="fas fa-check"></i>
                    Got it!
                </button>
            </div>
        </div>
    </div>

    <script>
        let teamleads = @json($teamleads ?? []);
        let filteredTeamleads = [...teamleads];
        let editingTeamleadId = null;

        // Debounce function to limit AJAX calls
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

        // Handle Edit Click
        function editTeamlead(id) {
            const tl = teamleads.find(t => t.id === id);
            if (!tl) return;

            editingTeamleadId = id;

            // Fill form fields
            document.getElementById('employeeId').value = tl.id;
            document.getElementById('employeeName').value = tl.name;
            document.getElementById('employeeEmail').value = tl.email;
            document.getElementById('employeePhone').value = tl.phone;
            document.getElementById('employeeAddress').value = tl.address || '';
            document.getElementById('formTitle').textContent = 'Edit Team Lead';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update';

            if (tl.profile_photo) {
                document.getElementById('photoPreview').src = '/storage/' + tl.profile_photo;
                document.getElementById('photoPreview').style.display = 'block';
                document.getElementById('photoPlaceholder').style.display = 'none';
            }

            document.querySelector('.employee-form-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Intercept Form Submission
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const id = editingTeamleadId;
            const formData = new FormData(this);
            const url = id ? `{{ route('operations.teamlead.update', ':id') }}`.replace(':id', id) : '{{ route('operations.teamlead.store') }}';
            const method = id ? 'POST' : 'POST'; // Use POST for both (with _method=PUT for updates)

            if (id) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error(id ? 'Update failed' : 'Creation failed');
                return res.json();
            })
            .then(data => {
                if (id) {
                    // Update existing team lead
                    const index = teamleads.findIndex(t => t.id === id);
                    if (index !== -1) {
                        teamleads[index] = data.teamlead;
                    }
                    showNotification('Team Lead updated successfully!', 'success');
                    document.getElementById('modalTitle').textContent = 'Team Lead Updated Successfully!';
                    document.getElementById('modalMessage').textContent = 'The team lead details have been updated successfully.';
                } else {
                    // Add new team lead
                    teamleads.push(data.teamlead);
                    filteredTeamleads = [...teamleads];
                    showNotification('Team Lead added successfully!', 'success');
                    document.getElementById('modalTitle').textContent = 'Team Lead Created Successfully!';
                    document.getElementById('modalMessage').textContent = 'The team lead has been added to your team. A welcome email with login credentials has been sent to their registered email address.';
                }
                renderTeamleadTable();
                resetForm();
                showSuccessModal();
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(id ? 'Error updating Team Lead' : 'Error adding Team Lead', 'error');
            });
        });

        // Reset form
        function resetForm() {
            editingTeamleadId = null;
            document.getElementById('employeeForm').reset();
            document.getElementById('employeeId').value = '';
            document.getElementById('formTitle').textContent = 'Add New Team Lead';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Team Lead';
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('photoPlaceholder').style.display = 'flex';
        }

        // Re-render Teamlead Table
        function renderTeamleadTable() {
            const tbody = document.getElementById('employeesTableBody');
            const emptyState = document.querySelector('.empty-state');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            if (filteredTeamleads.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="empty-state-title">No team leads found</div>
                            <div class="empty-state-description">Start by adding your first team lead using the form on the left</div>
                        </td>
                    </tr>
                `;
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
                tbody.innerHTML = filteredTeamleads.map(t => {
                    const isActive = !t.deleted_at;
                    return `
                        <tr>
                            <td>
                                <div class="employee-info">
                                    <div class="employee-avatar">
                                        ${t.profile_photo ? `<img src="/storage/${t.profile_photo}" alt="${t.name}">` : `<div class="employee-avatar-placeholder">${t.name.charAt(0).toUpperCase()}</div>`}
                                    </div>
                                    <div class="employee-details">
                                        <div class="employee-name">${t.name}</div>
                                        <div class="employee-email">${t.email}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="designation-badge">Team Lead</span></td>
                            <td><div class="contact-info"><i class="fas fa-phone"></i> ${t.phone}</div></td>
                            <td><div class="contact-info"><i class="fas fa-map-marker-alt"></i> ${t.address ?? 'Not provided'}</div></td>
                            <td>
                                <span class="badge ${isActive ? 'bg-success' : 'bg-danger'}">
                                    ${isActive ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn btn-edit" onclick="editTeamlead(${t.id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/operations/teamlead/${t.id}/${isActive ? 'deactivate' : 'activate'}" method="POST" class="toggle-form m-0 p-0">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <label class="switch mb-0">
                                            <input type="checkbox" onchange="this.form.submit()" ${isActive ? 'checked' : ''}>
                                            <span class="slider round"></span>
                                        </label>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Update stats
            document.getElementById('totalEmployees').textContent = filteredTeamleads.length;
            document.getElementById('activeEmployees').textContent = filteredTeamleads.filter(t => !t.deleted_at).length;
        }

        // Handle photo upload preview
        function handlePhotoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                showNotification('Please select a valid image file', 'error');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                showNotification('Image size should be less than 2MB', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
                document.getElementById('photoPreview').style.display = 'block';
                document.getElementById('photoPlaceholder').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        // Filter team leads with AJAX
        const filterTeamleads = debounce(function() {
            const searchTerm = document.getElementById('searchInput').value;
            const url = `{{ route('operations.teamlead.index') }}?search=${encodeURIComponent(searchTerm)}`;

            fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                filteredTeamleads = data.teamleads;
                renderTeamleadTable();
            })
            .catch(error => {
                console.error('Error fetching team leads:', error);
                showNotification('Failed to fetch team leads', 'error');
            });
        }, 300);

        // Show success modal
        function showSuccessModal() {
            document.getElementById('successModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Close success modal
        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                padding: 16px 20px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 500;
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
            `;

            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            notification.innerHTML = `
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
                <button style="background: none; border: none; color: white; cursor: pointer; margin-left: auto;">
                    <i class="fas fa-times"></i>
                </button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 5000);

            notification.querySelector('button').addEventListener('click', () => {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            });
        }

        // Add CSS for animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeSuccessModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuccessModal();
            }
        });

        // Attach search input event listener
        document.getElementById('searchInput').addEventListener('input', filterTeamleads);

        // Initialize table
        document.addEventListener('DOMContentLoaded', function() {
            renderTeamleadTable();
        });
    </script>
</body>
</html>

