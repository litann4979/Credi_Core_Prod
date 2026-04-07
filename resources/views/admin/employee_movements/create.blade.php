<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign outside work</title>
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
                <h4>Assign outside work</h4>
                <a href="{{ route('admin.employee-movements.index') }}" class="btn btn-modern-outline btn-modern">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="p-4 p-md-5">
                <form action="{{ route('admin.employee-movements.store') }}" method="POST">
                    @csrf
                    @include('admin.employee_movements._form')
                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('admin.employee-movements.index') }}" class="btn btn-light text-muted fw-medium px-4">Cancel</a>
                        <button type="submit" class="btn-modern">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

