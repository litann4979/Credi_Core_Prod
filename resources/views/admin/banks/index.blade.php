<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5; /* Indigo */
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

        /* Layout Adjustments matching your snippet */
        .main-container {
            margin-left: 250px; /* Adjust based on your sidebar width */
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            padding: 2rem;
        }

        /* Modern Card Styling */
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

        /* Button Styling */
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

        /* Table Styling */
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
            padding: 0.7rem 1.2rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-modern td {
            padding: 0.55rem 1.2rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 0.82rem;
            line-height: 1.15;
        }

        .bank-icon-wrap {
            width: 28px;
            height: 28px;
        }

        .bank-name-text {
            font-size: 0.82rem;
            line-height: 1.1;
        }

        .status-chip {
            font-size: 0.68rem;
            padding: 0.18rem 0.42rem;
            line-height: 1;
        }

        .table-modern .action-col {
            white-space: nowrap;
            min-width: 110px;
        }

        .table-modern tr:hover td {
            background-color: #f9fafb;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            transition: all 0.2s;
            background: transparent;
            border: 1px solid transparent;
        }

        .action-group {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: nowrap;
            justify-content: flex-end;
        }

        .action-group form {
            margin: 0;
            display: inline-flex;
        }

        .action-btn:hover {
            background-color: #f3f4f6;
            color: var(--primary-color);
        }

        .action-btn.delete:hover {
            background-color: #fee2e2;
            color: #ef4444;
        }

        /* Modal Customization */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            border-bottom: 1px solid #f3f4f6;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border-color: #d1d5db;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-container { margin-left: 0; }
        }
    </style>
</head>
<body>

    @include('admin.Components.sidebar')

    <div class="main-container">

        <div class="container-fluid pb-0">
             @include('admin.Components.header')
        </div>

        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


            <div class="card-modern">
                <div class="card-header-modern">
                    <h4>Bank Master List</h4>
                     <div class="d-flex align-items-center gap-3">
        {{-- Status Filter --}}
        <form method="GET">
            <select name="status"
                    class="form-select form-select-sm"
                    style="min-width: 160px;"
                    onchange="this.form.submit()">
                <option value="">All Banks</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </form>

        {{-- Add Bank Button --}}
        <button type="button"
                class="btn-modern"
                data-bs-toggle="modal"
                data-bs-target="#addBankModal">
            <i class="fas fa-plus"></i> Add New Bank
        </button>
    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th width="10%">#ID</th>
                                <th>Bank Name</th>
                                <th width="15%">Status</th>
                                <th width="15%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banks as $bank)
                                <tr>
                                    <td>#{{ $bank->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 bank-icon-wrap">
                                                <i class="fas fa-university text-primary"></i>
                                            </div>
                                            <span class="fw-medium bank-name-text">{{ $bank->bank_name }}</span>
                                        </div>
                                    </td>
                                    <td>
    @if($bank->is_active)
        <span class="badge bg-success-subtle text-success fw-medium status-chip">
            <i class="fas fa-check-circle me-1"></i> Active
        </span>
    @else
        <span class="badge bg-danger-subtle text-danger fw-medium status-chip">
            <i class="fas fa-ban me-1"></i> Inactive
        </span>
    @endif
</td>

                                    <td class="text-end action-col">
                                        <div class="action-group">
                                        <button class="action-btn"
                                                onclick="editBank({{ $bank->id }}, '{{ addslashes($bank->bank_name) }}')"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                      <form action="{{ route('admin.banks.toggle', $bank->id) }}" method="POST" class="d-inline">
    @csrf
    @method('PATCH')
    <button type="submit"
        class="action-btn {{ $bank->is_active ? 'text-warning' : 'text-success' }}"
        title="{{ $bank->is_active ? 'Deactivate' : 'Activate' }}"
        onclick="return confirm('Are you sure?')">
        <i class="fas {{ $bank->is_active ? 'fa-ban' : 'fa-check' }}"></i>
    </button>
</form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No banks found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($banks->hasPages())
                    <div class="p-4 border-top">
                        {{ $banks->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.banks.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label text-muted fw-bold small text-uppercase">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name" placeholder="Ex: HDFC Bank" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light text-muted fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modern">Save Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editBankForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_bank_name" class="form-label text-muted fw-bold small text-uppercase">Bank Name</label>
                            <input type="text" class="form-control" id="edit_bank_name" name="bank_name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light text-muted fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modern">Update Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function editBank(id, name) {
            // Set the form action dynamically
            let form = document.getElementById('editBankForm');
            form.action = `/admin/banks/${id}`;

            // Set the input value
            document.getElementById('edit_bank_name').value = name;

            // Show the modal
            let myModal = new bootstrap.Modal(document.getElementById('editBankModal'));
            myModal.show();
        }
    </script>
</body>
</html>
