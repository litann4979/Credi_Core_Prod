<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Targets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @include('admin.target.partials.styles')
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
                <h4>Targets</h4>
                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <select name="type" class="form-select form-select-sm" style="min-width: 180px;" onchange="this.form.submit()">
                            <option value="">All types</option>
                            <option value="lead" {{ ($type ?? '') === 'lead' ? 'selected' : '' }}>Lead</option>
                            <option value="attendance" {{ ($type ?? '') === 'attendance' ? 'selected' : '' }}>Attendance</option>
                            <option value="leave" {{ ($type ?? '') === 'leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                    </form>
                    <a href="{{ route('admin.targets.create') }}" class="btn-modern">
                        <i class="fas fa-plus"></i> Add target
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th width="8%">#</th>
                            <th>User</th>
                            <th width="12%">Type</th>
                            <th width="10%">Target</th>
                            <th width="10%">Achieved</th>
                            <th width="12%">Start</th>
                            <th width="12%">End</th>
                            <th width="10%">Status</th>
                            <th width="12%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($targets as $target)
                            <tr>
                                <td>#{{ $target->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium d-block">{{ $target->user?->name ?? '—' }}</span>
                                            @if($target->user?->designation)
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">{{ str_replace('_', ' ', $target->user->designation) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-capitalize">{{ $target->type }}</span></td>
                                <td>{{ number_format($target->target_value) }}</td>
                                <td>{{ number_format($target->achieved_value) }}</td>
                                <td>{{ $target->start_date->format('M d, Y') }}</td>
                                <td>{{ $target->end_date->format('M d, Y') }}</td>
                                <td>
                                    @if($target->is_completed)
                                        <span class="badge bg-success-subtle text-success fw-medium">
                                            <i class="fas fa-check-circle me-1"></i> Done
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning fw-medium">
                                            <i class="fas fa-clock me-1"></i> Open
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.targets.edit', $target) }}" class="action-btn me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.targets.destroy', $target) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this target?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-bullseye fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">No targets found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($targets->hasPages())
                <div class="p-4 border-top">
                    {{ $targets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
