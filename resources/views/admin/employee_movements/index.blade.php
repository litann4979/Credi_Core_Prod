<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee work outside</title>
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

        <div class="card-modern">
            <div class="card-header-modern">
                <h4>Employee work outside</h4>
                <a href="{{ route('admin.employee-movements.create') }}" class="btn-modern">
                    <i class="fas fa-plus"></i> Assign work
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th width="8%">#</th>
                            <th>Employee</th>
                            <th width="18%">Start time</th>
                            <th width="12%">Allowed (min)</th>
                            <th width="10%">Type</th>
                            <th width="12%">Status</th>
                            <th width="12%">Penalty</th>
                            <th width="12%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td>#{{ $movement->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium d-block">{{ $movement->employee?->name ?? '—' }}</span>
                                            <span class="text-muted small">{{ $movement->employee?->email ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($movement->start_time)->format('M d, Y h:i A') ?? '—' }}</td>
                                <td>{{ (int) $movement->allowed_minutes }}</td>
                                <td><span class="text-capitalize">{{ $movement->type }}</span></td>
                                <td>
                                    @if($movement->status === 'approved')
                                        <span class="badge bg-success-subtle text-success fw-medium">Approved</span>
                                    @elseif($movement->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning fw-medium">Pending</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fw-medium">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($movement->penalty_applied)
                                        <span class="badge bg-danger-subtle text-danger fw-medium">Applied</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary fw-medium">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.employee-movements.destroy', $movement) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this movement?');">
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
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-route fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">No work movement records found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
                <div class="p-4 border-top">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

