<div class="row g-3">
    <div class="col-md-6">
        <label for="employee_id" class="form-label text-muted fw-bold small text-uppercase">Employee</label>
        <select id="employee_id" name="employee_id" class="form-select" required>
            <option value="">Select employee</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" {{ (string) old('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                    {{ $employee->name }} ({{ $employee->email }})
                </option>
            @endforeach
        </select>
        @error('employee_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label for="start_time" class="form-label text-muted fw-bold small text-uppercase">Start time</label>
        <input
            type="datetime-local"
            id="start_time"
            name="start_time"
            class="form-control"
            value="{{ old('start_time') }}"
            required
        >
        @error('start_time')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label for="allowed_minutes" class="form-label text-muted fw-bold small text-uppercase">Allowed minutes</label>
        <input
            type="number"
            min="1"
            step="1"
            id="allowed_minutes"
            name="allowed_minutes"
            class="form-control"
            value="{{ old('allowed_minutes', $defaultAllowedMinutes ?? 20) }}"
            required
        >
        @error('allowed_minutes')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

