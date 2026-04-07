<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payroll Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary: #4f46e5; --bg: #f3f4f6; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        .main-content { margin-left: 280px; padding: 100px 30px; }
        
        .card-box {
            background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            padding: 25px; margin-bottom: 25px; border: 1px solid #e5e7eb;
        }
        .section-header {
            font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; margin-bottom: 15px;
        }
        .form-label { font-weight: 500; font-size: 0.85rem; color: #374151; margin-bottom: 4px; }
        .form-control, .form-select { border-radius: 6px; font-size: 0.9rem; padding: 8px 12px; }
        .form-control[readonly] { background-color: #f9fafb; color: #6b7280; }
        
        .amount-input { font-weight: 600; color: #1f2937; text-align: right; }
        .total-row { background: #f8fafc; border-top: 2px solid #e5e7eb; padding-top: 15px; margin-top: 10px; }
        .net-pay { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
    </style>
</head>
<body>
    @include('hr.Components.sidebar')
    @include('hr.Components.header')

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Generate Payslip</h3>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('hr.salary_slips.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-box">
                        <div class="section-header">Employee Selection</div>
                        <div class="mb-3">
                            <label class="form-label">Select Employee</label>
                            <select name="user_id" id="user_id" class="form-control select2" required>
                                <option value="">Search Employee...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code ?? $emp->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Month</label>
                            <input type="month" name="month" id="month" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-primary w-100 mb-3" id="fetchBtn">
                            <i class="fas fa-sync-alt me-1"></i> Fetch Data
                        </button>

                        <div class="section-header mt-4">Attendance Details</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Working Days</label>
                                <input type="number" name="working_days" id="working_days" class="form-control" readonly required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Present Days</label>
                                <input type="number" name="present_days" id="present_days" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Paid Leaves</label>
                                <input type="number" name="paid_leaves" id="paid_leaves" class="form-control" value="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Absent/LWP</label>
                                <input type="number" name="absent_days" id="absent_days" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <div class="section-header text-success">Earnings</div>
                                @foreach([
                                    'basic_salary' => 'Basic Salary',
                                    'hra' => 'House Rent Allowance',
                                    'conveyance_allowance' => 'Conveyance Allowance',
                                    'medical_allowance' => 'Medical Allowance',
                                    'special_allowance' => 'Special Allowance',
                                    'performance_bonus' => 'Performance Bonus',
                                    'overtime_amount' => 'Overtime',
                                ] as $key => $label)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">{{ $label }}</label>
                                    <input type="number" step="0.01" name="{{ $key }}" id="{{ $key }}" class="form-control amount-input calc-earn" style="width: 120px;" value="0">
                                </div>
                                @endforeach
                            </div>

                            <div class="col-md-6 ps-4">
                                <div class="section-header text-danger">Deductions</div>
                                @foreach([
                                    'pf_employee' => 'PF (Employee)',
                                    'esi_employee' => 'ESI (Employee)',
                                    'professional_tax' => 'Professional Tax',
                                    'tds' => 'TDS / Tax',
                                    'loan_deduction' => 'Loan Repayment',
                                    'leave_deduction' => 'Leave Deduction',
                                ] as $key => $label)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">{{ $label }}</label>
                                    <input type="number" step="0.01" name="{{ $key }}" id="{{ $key }}" class="form-control amount-input calc-ded" style="width: 120px;" value="0">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="total-row row align-items-center">
                            <div class="col-md-4 text-center">
                                <small class="text-muted text-uppercase d-block">Gross Earnings</small>
                                <h5 class="text-success fw-bold" id="disp_gross">₹ 0.00</h5>
                                <input type="hidden" name="gross_salary" id="gross_salary">
                            </div>
                            <div class="col-md-4 text-center border-start border-end">
                                <small class="text-muted text-uppercase d-block">Total Deductions</small>
                                <h5 class="text-danger fw-bold" id="disp_deductions">₹ 0.00</h5>
                                <input type="hidden" name="total_deductions" id="total_deductions">
                            </div>
                            <div class="col-md-4 text-center">
                                <small class="text-muted text-uppercase d-block">Net Payable</small>
                                <h3 class="net-pay" id="disp_net">₹ 0.00</h3>
                                <input type="hidden" name="net_salary" id="net_salary">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                                <i class="fas fa-check-circle me-2"></i> Generate Payslip
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="card-box mt-4">
            <div class="section-header">Recent Salary Slips</div>
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                        <th>Generated On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salarySlips as $slip)
                    <tr>
                        <td>{{ $slip->employee_name }} <br> <small class="text-muted">{{ $slip->employee_code }}</small></td>
                        <td>{{ $slip->month->format('M Y') }}</td>
                        <td class="fw-bold text-dark">₹ {{ number_format($slip->net_salary, 2) }}</td>
                        <td><span class="badge bg-success">Generated</span></td>
                        <td>{{ $slip->created_at->format('d M, h:i A') }}</td>
                        <td>
                            @if($slip->pdf_path)
    <form action="{{ route('hr.salary_slips.download', $slip->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-download"></i>
        </button>
    </form>
@endif
                            <form action="{{ route('hr.salary_slips.destroy', $slip->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            function calculate() {
                let gross = 0;
                $('.calc-earn').each(function() { gross += parseFloat($(this).val()) || 0; });
                
                let ded = 0;
                $('.calc-ded').each(function() { ded += parseFloat($(this).val()) || 0; });

                let net = gross - ded;

                $('#disp_gross').text('₹ ' + gross.toFixed(2));
                $('#gross_salary').val(gross);

                $('#disp_deductions').text('₹ ' + ded.toFixed(2));
                $('#total_deductions').val(ded);

                $('#disp_net').text('₹ ' + net.toFixed(2));
                $('#net_salary').val(net);
            }

            $('.amount-input').on('input', calculate);

            $('#fetchBtn').click(function() {
                let user_id = $('#user_id').val();
                let month = $('#month').val();
                if(!user_id || !month) { alert('Select user & month'); return; }

                $.ajax({
                    url: "{{ route('hr.salary_slips.fetch_data') }}",
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}", user_id: user_id, month: month },
                    success: function(res) {
                        if(res.success) {
                            // Attendance
        $('#working_days').val(res.attendance.working_days);
        $('#present_days').val(res.attendance.present_days);
        $('#paid_leaves').val(res.attendance.paid_leaves);
        $('#absent_days').val(res.attendance.absent_days);

        // Earnings
        $('#basic_salary').val(res.salary.basic_salary);
        $('#hra').val(res.salary.hra);
        $('#conveyance_allowance').val(res.salary.conveyance_allowance);
        $('#medical_allowance').val(res.salary.medical_allowance);
        $('#special_allowance').val(res.salary.special_allowance);

        // ✅ Deductions (MISSING PART)
        $('#pf_employee').val(res.salary.pf_employee ?? 0);
        $('#professional_tax').val(res.salary.professional_tax ?? 0);

        // Optional (if backend sends later)
        $('#esi_employee').val(res.salary.esi_employee ?? 0);
        $('#tds').val(res.salary.tds ?? 0);

         calculate();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>