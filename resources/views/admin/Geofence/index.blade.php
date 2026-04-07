<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geofence Settings</title>
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
                <h4>Geofence Settings</h4>
                <a href="{{ route('admin.geofence.create') }}" class="btn-modern">
                    <i class="fas fa-plus"></i> Add geofence
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th width="8%">#</th>
                            <th>Office Name</th>
                            <th width="18%">Latitude</th>
                            <th width="18%">Longitude</th>
                            <th width="14%">Radius</th>
                            <th width="12%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($geofences as $geofence)
                            <tr>
                                <td>#{{ $geofence->id }}</td>
                                <td class="fw-medium">{{ $geofence->office_name }}</td>
                                <td>{{ number_format((float) $geofence->latitude, 6) }}</td>
                                <td>{{ number_format((float) $geofence->longitude, 6) }}</td>
                                <td>{{ number_format((float) $geofence->radius, 0) }} m</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.geofence.edit', $geofence) }}" class="action-btn me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.geofence.destroy', $geofence) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this geofence setting?');">
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-map-marker-alt fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">No geofence settings found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($geofences->hasPages())
                <div class="p-4 border-top">
                    {{ $geofences->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
