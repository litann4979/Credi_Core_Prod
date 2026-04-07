<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Update Password - Lead Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }
        .card-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .card-body {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #374151;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        .input-group-text {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: white;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-text {
            font-size: 0.75rem;
            color: #6b7280;
        }
        .invalid-feedback {
            font-size: 0.75rem;
            color: #dc2626;
        }
        .is-invalid {
            border-color: #dc2626;
        }
    </style>
</head>
<body>
        @include('TeamLead.Components.sidebar')

    <div class="container">
         @include('TeamLead.Components.header')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Update Password') }}</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div id="errorContainer" style="display: none;"></div>

                        <form id="passwordUpdateForm" method="POST" action="{{ route('team_lead.password.update') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="current_password" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Current Password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="current_password" type="password"
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               name="current_password" required autocomplete="current-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="current_password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @error('current_password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">
                                    {{ __('New Password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="password" type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               name="password" required autocomplete="new-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Minimum 8 characters, with at least one uppercase, one lowercase, one number, and one special character.
                                    </small>
                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Confirm Password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="password-confirm" type="password" class="form-control"
                                               name="password_confirmation" required autocomplete="new-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="password-confirm">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key"></i> {{ __('Update Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Password visibility toggle
            $('.toggle-password').click(function () {
                const target = $(this).data('target');
                const input = $('#' + target);
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // AJAX form submission
            $('#passwordUpdateForm').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);
                const errorContainer = $('#errorContainer');
                errorContainer.empty().hide();

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        // Clear any previous errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        // Show success message
                        const successAlert = $(`
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Password updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                        errorContainer.html(successAlert).show();
                        form[0].reset(); // Reset form
                    },
                    error: function (xhr) {
                        // Clear previous errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (field, messages) {
                                const input = $(`#${field.replace('.', '-')}`);
                                input.addClass('is-invalid');
                                const errorMessage = `<span class="invalid-feedback d-block" role="alert"><strong>${messages[0]}</strong></span>`;
                                input.closest('.input-group').after(errorMessage);
                            });
                        } else {
                            // Show generic error
                            const errorAlert = $(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    An error occurred. Please try again later.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);
                            errorContainer.html(errorAlert).show();
                        }

                        // Log error to console for debugging
                        console.error('Password update error:', xhr.responseJSON || xhr.statusText);
                    }
                });
            });
        });
    </script>
</body>
</html>
