@php
    $t = $target ?? null;
    $user = $t?->user;
    $selEmployee = old('user_id_employee', ($user && $user->designation === 'employee') ? $t->user_id : '');
@endphp

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="section-label"><i class="fas fa-user me-1"></i> Employee</div>
        <div class="assignee-box">
            <label for="user_id_employee" class="form-label text-muted fw-bold small text-uppercase">Assign to employee</label>
            <select name="user_id_employee" id="user_id_employee" class="form-select" required>
                <option value="">— Select employee —</option>
                @foreach($employees as $u)
                    <option value="{{ $u->id }}" @selected((string) $selEmployee === (string) $u->id)>
                        {{ $u->name }} @if($u->email)<span class="text-muted">({{ $u->email }})</span>@endif
                    </option>
                @endforeach
            </select>
            @error('user_id_employee')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <label for="type" class="form-label text-muted fw-bold small text-uppercase">Type</label>
        <select name="type" id="type" class="form-select" required>
            <option value="">— Select —</option>
            <option value="lead" @selected(old('type', $t?->type) === 'lead')>Lead</option>
            <option value="attendance" @selected(old('type', $t?->type) === 'attendance')>Attendance</option>
            <option value="leave" @selected(old('type', $t?->type) === 'leave')>Leave</option>
        </select>
        @error('type')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="target_value" class="form-label text-muted fw-bold small text-uppercase">Target value</label>
        <input type="number" min="0" step="1" class="form-control" name="target_value" id="target_value"
               value="{{ old('target_value', $t?->target_value) }}" required>
        @error('target_value')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="achieved_value" class="form-label text-muted fw-bold small text-uppercase">Achieved value</label>
        <input type="number" min="0" step="1" class="form-control" name="achieved_value" id="achieved_value"
               value="{{ old('achieved_value', $t?->achieved_value ?? 0) }}">
        @error('achieved_value')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="start_date" class="form-label text-muted fw-bold small text-uppercase">Start date</label>
        <input type="date" class="form-control" name="start_date" id="start_date"
               value="{{ old('start_date', $t?->start_date?->format('Y-m-d')) }}" required>
        @error('start_date')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="end_date" class="form-label text-muted fw-bold small text-uppercase">End date</label>
        <input type="date" class="form-control" name="end_date" id="end_date"
               value="{{ old('end_date', $t?->end_date?->format('Y-m-d')) }}" required>
        @error('end_date')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_completed" id="is_completed" value="1"
                   @checked(old('is_completed', $t?->is_completed ?? false))>
            <label class="form-check-label fw-medium" for="is_completed">Mark as completed</label>
        </div>
        @error('is_completed')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

