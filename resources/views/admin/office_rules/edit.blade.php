<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Rules Configuration</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/kredipalfinallogo.png') }}">
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
                <h4>Office Rules Configuration</h4>
            </div>
            <div class="p-4 p-md-5">
                <form action="{{ route('admin.office_rules.update') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Office Timing</div></div>
                        <div class="col-md-6">
                            <label class="form-label">Office Start Time</label>
                            <input type="time" name="office_start_time" class="form-control" value="{{ old('office_start_time', substr((string) $rule->office_start_time, 0, 5)) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Office End Time</label>
                            <input type="time" name="office_end_time" class="form-control" value="{{ old('office_end_time', substr((string) $rule->office_end_time, 0, 5)) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Lunch</div></div>
                        <div class="col-md-4"><label class="form-label">Lunch Start</label><input type="time" name="lunch_start" class="form-control" value="{{ old('lunch_start', substr((string) $rule->lunch_start, 0, 5)) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Lunch End</label><input type="time" name="lunch_end" class="form-control" value="{{ old('lunch_end', substr((string) $rule->lunch_end, 0, 5)) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Lunch Allowed Minutes</label><input type="number" step="1" min="0" name="lunch_allowed_minutes" class="form-control" value="{{ old('lunch_allowed_minutes', $rule->lunch_allowed_minutes) }}" required></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Break</div></div>
                        <div class="col-md-4"><label class="form-label">Break Start</label><input type="time" name="break_start" class="form-control" value="{{ old('break_start', substr((string) $rule->break_start, 0, 5)) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Break End</label><input type="time" name="break_end" class="form-control" value="{{ old('break_end', substr((string) $rule->break_end, 0, 5)) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Break Allowed Minutes</label><input type="number" step="1" min="0" name="break_allowed_minutes" class="form-control" value="{{ old('break_allowed_minutes', $rule->break_allowed_minutes) }}" required></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Work & Geofence</div></div>
                        <div class="col-md-6"><label class="form-label">Work Allowed Minutes</label><input type="number" step="1" min="0" name="work_allowed_minutes" class="form-control" value="{{ old('work_allowed_minutes', $rule->work_allowed_minutes) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Geofence Radius</label><input type="number" step="0.01" min="0" name="geofence_radius" class="form-control" value="{{ old('geofence_radius', $rule->geofence_radius) }}" required></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Score Settings</div></div>
                        <div class="col-md-4"><label class="form-label">Default Score</label><input type="number" step="0.01" name="default_score" class="form-control" value="{{ old('default_score', $rule->default_score) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Target Mark</label><input type="number" step="0.01" name="target_mark" class="form-control" value="{{ old('target_mark', $rule->target_mark) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Lead Mark</label><input type="number" step="0.01" name="lead_mark" class="form-control" value="{{ old('lead_mark', $rule->lead_mark) }}" required></div>
                        <div class="col-md-12"><label class="form-label">Personal Lead Count</label><input type="number" step="1" min="0" name="personal_lead_count" class="form-control" value="{{ old('personal_lead_count', $rule->personal_lead_count) }}" required></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Penalties</div></div>
                        <div class="col-md-4"><label class="form-label">Late Penalty</label><input type="number" step="0.01" min="0" name="late_penalty" class="form-control" value="{{ old('late_penalty', $rule->late_penalty) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Late 15 Min Penalty</label><input type="number" step="0.01" min="0" name="late_15min_penalty" class="form-control" value="{{ old('late_15min_penalty', $rule->late_15min_penalty) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Unauthorized Outside Penalty</label><input type="number" step="0.01" min="0" name="unauthorized_outside_penalty" class="form-control" value="{{ old('unauthorized_outside_penalty', $rule->unauthorized_outside_penalty) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Unauthorized Penalty Window (Minutes)</label><input type="number" step="1" min="1" max="1440" name="unauthorized_penalty_window_minutes" class="form-control" value="{{ old('unauthorized_penalty_window_minutes', $rule->unauthorized_penalty_window_minutes ?? 15) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Extra Break Penalty</label><input type="number" step="0.01" min="0" name="extra_break_penalty" class="form-control" value="{{ old('extra_break_penalty', $rule->extra_break_penalty) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Extra Lunch Penalty</label><input type="number" step="0.01" min="0" name="extra_lunch_penalty" class="form-control" value="{{ old('extra_lunch_penalty', $rule->extra_lunch_penalty) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Early Checkout Penalty</label><input type="number" step="0.01" min="0" name="early_checkout_penalty" class="form-control" value="{{ old('early_checkout_penalty', $rule->early_checkout_penalty) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Work Delay Penalty</label><input type="number" step="0.01" min="0" name="work_delay_penalty" class="form-control" value="{{ old('work_delay_penalty', $rule->work_delay_penalty) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Penalty Per Minute</label><input type="number" step="0.01" min="0" name="penalty_per_minute" class="form-control" value="{{ old('penalty_per_minute', $rule->penalty_per_minute) }}" required></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12"><div class="section-label">Settings</div></div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="late_15min_enabled" id="late_15min_enabled" value="1" @checked(old('late_15min_enabled', $rule->late_15min_enabled))>
                                <label class="form-check-label" for="late_15min_enabled">Late 15 Min Enabled</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="per_minute_deduction_enabled" id="per_minute_deduction_enabled" value="1" @checked(old('per_minute_deduction_enabled', $rule->per_minute_deduction_enabled))>
                                <label class="form-check-label" for="per_minute_deduction_enabled">Per Minute Deduction Enabled</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="allow_admin_override" id="allow_admin_override" value="1" @checked(old('allow_admin_override', $rule->allow_admin_override))>
                                <label class="form-check-label" for="allow_admin_override">Allow Admin Override</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-4">
                        <button type="submit" class="btn-modern">
                            <i class="fas fa-save"></i> Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
