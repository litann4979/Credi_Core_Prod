<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { width: 100%; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { max-height: 50px; float: left; }
        .company-info { text-align: right; float: right; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #4f46e5; }
        
        .title { text-align: center; font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; background: #e0e7ff; padding: 5px; border: 1px solid #ccc; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 5px; width: 25%; }
        .label { font-weight: bold; color: #555; }
        
        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ccc; }
        .salary-table th { background: #f3f4f6; padding: 8px; text-align: left; border: 1px solid #ccc; font-weight: bold; }
        .salary-table td { padding: 8px; border: 1px solid #ccc; vertical-align: top; }
        .amount { text-align: right; }
        
        .net-pay { background: #4f46e5; color: white; padding: 10px; text-align: center; font-weight: bold; font-size: 14px; }
        
        .footer { margin-top: 40px; font-size: 10px; text-align: center; color: #777; border-top: 1px solid #ccc; padding-top: 10px; }
        .clear { clear: both; }
        .inner-table { width: 100%; border: none; }
        .inner-table td { border: none; padding: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('logo.png') }}" class="logo">
            <div class="company-info">
                <div class="company-name">Kredipal</div>
                <div>Bhubaneswar, Odisha</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="title">Payslip for the month of {{ $salarySlip->month->format('F Y') }}</div>

        <table class="info-table">
            <tr>
                <td class="label">Employee Name:</td> <td>{{ $salarySlip->employee_name }}</td>
                <td class="label">Employee Code:</td> <td>{{ $salarySlip->employee_code ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Designation:</td> <td>{{ $salarySlip->designation }}</td>
                <td class="label">Department:</td> <td>{{ $salarySlip->department ?? 'General' }}</td>
            </tr>
            <tr>
                <td class="label">Date of Joining:</td> <td>{{ $salarySlip->joining_date ?? 'N/A' }}</td>
                <td class="label">PAN Number:</td> <td>{{ $salarySlip->pan_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Bank Name:</td> <td>{{ $salarySlip->bank_name ?? 'N/A' }}</td>
                <td class="label">Account No:</td> <td>{{ $salarySlip->account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Working Days:</td> <td>{{ $salarySlip->working_days }}</td>
                <td class="label">Paid Days:</td> <td>{{ $salarySlip->present_days + $salarySlip->paid_leaves }}</td>
            </tr>
        </table>

        <table class="salary-table">
            <thead>
                <tr>
                    <th width="50%">Earnings</th>
                    <th width="15%" class="amount">Amount (₹)</th>
                    <th width="20%">Deductions</th>
                    <th width="15%" class="amount">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="inner-table">
                            @if($salarySlip->basic_salary > 0) <tr><td>Basic Salary</td><td class="amount">{{ number_format($salarySlip->basic_salary, 2) }}</td></tr> @endif
                            @if($salarySlip->conveyance_allowance > 0) <tr><td>Conveyance</td><td class="amount">{{ number_format($salarySlip->conveyance_allowance, 2) }}</td></tr> @endif
                            @if($salarySlip->medical_allowance > 0) <tr><td>Medical</td><td class="amount">{{ number_format($salarySlip->medical_allowance, 2) }}</td></tr> @endif
                            @if($salarySlip->special_allowance > 0) <tr><td>Special Allowance</td><td class="amount">{{ number_format($salarySlip->special_allowance, 2) }}</td></tr> @endif
                            @if($salarySlip->performance_bonus > 0) <tr><td>Bonus</td><td class="amount">{{ number_format($salarySlip->performance_bonus, 2) }}</td></tr> @endif
                            @if($salarySlip->overtime_amount > 0) <tr><td>Overtime</td><td class="amount">{{ number_format($salarySlip->overtime_amount, 2) }}</td></tr> @endif
                        </table>
                    </td>
                    <td class="amount"></td> 

                    <td>
                        <table class="inner-table">
                            @if($salarySlip->pf_employee > 0) <tr><td>Provident Fund</td><td class="amount">{{ number_format($salarySlip->pf_employee, 2) }}</td></tr> @endif
                            @if($salarySlip->esi_employee > 0) <tr><td>ESI</td><td class="amount">{{ number_format($salarySlip->esi_employee, 2) }}</td></tr> @endif
                            @if($salarySlip->professional_tax > 0) <tr><td>Prof. Tax</td><td class="amount">{{ number_format($salarySlip->professional_tax, 2) }}</td></tr> @endif
                            @if($salarySlip->tds > 0) <tr><td>TDS</td><td class="amount">{{ number_format($salarySlip->tds, 2) }}</td></tr> @endif
                            @if($salarySlip->loan_deduction > 0) <tr><td>Loan Repayment</td><td class="amount">{{ number_format($salarySlip->loan_deduction, 2) }}</td></tr> @endif
                            @if($salarySlip->leave_deduction > 0) <tr><td>Leave Deduction</td><td class="amount">{{ number_format($salarySlip->leave_deduction, 2) }}</td></tr> @endif
                        </table>
                    </td>
                    <td class="amount"></td>
                </tr>
                <tr style="background: #f9fafb; font-weight: bold;">
                    <td>Total Earnings</td>
                    <td class="amount">{{ number_format($salarySlip->gross_salary, 2) }}</td>
                    <td>Total Deductions</td>
                    <td class="amount">{{ number_format($salarySlip->total_deductions, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="net-pay">
            NET SALARY PAYABLE: ₹ {{ number_format($salarySlip->net_salary, 2) }} <br>
            <span style="font-size: 10px; font-weight: normal; text-transform: uppercase;">({{ $amountInWords ?? 'Only' }})</span>
        </div>

        <div style="margin-top: 40px;">
            <table width="100%">
                <tr>
                    <td align="left" style="padding-top: 30px;">
                        ______________________<br>
                        Employee Signature
                    </td>
                    <td align="right">
                        <img src="{{ public_path('signature.png') }}" style="height: 40px;"><br>
                        ______________________<br>
                        Authorized Signatory
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Generated on {{ now()->format('d M Y') }} | This is a computer-generated document.
        </div>
    </div>
</body>
</html>