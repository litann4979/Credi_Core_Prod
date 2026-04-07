<?php

namespace App\Http\Controllers\HrController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\SalarySlip;
use App\Models\SalaryStructure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SalarySlipController extends Controller
{
    public function index()
    {
        $employees = User::whereIn('designation', ['team_lead', 'operations', 'employee'])->get();
        $salarySlips = SalarySlip::with('user')->latest()->get();
        return view('hr.salary_slips.index', compact('employees', 'salarySlips'));
    }

    public function fetchEmployeeData(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'month'   => 'required'
        ]);

        $userId = $request->user_id;
        $date   = Carbon::parse($request->month);
        $start  = $date->copy()->startOfMonth();
        $end    = $date->copy()->endOfMonth();

        $user = User::findOrFail($userId);
        
        // Fetch predefined structure if exists
        $structure = SalaryStructure::where('user_id', $userId)->where('is_active', true)->first();

        // Attendance Logic
        $totalDays = $date->daysInMonth;
        
        $presentDays = Attendance::where('employee_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->count();

        $paidLeaves = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('leave_type', '!=', 'unpaid')
            ->whereBetween('start_date', [$start, $end])
            ->count();

        // Calculate Payable Days
        $payableDays = $presentDays + $paidLeaves; 
        if($payableDays > $totalDays) $payableDays = $totalDays;
        
        $absentDays = $totalDays - $payableDays;
        $prorataFactor = ($totalDays > 0) ? ($payableDays / $totalDays) : 0;

        // Base Salary Data 
        $base = $structure->basic_salary ?? 0;
        $conveyance = $structure->conveyance_allowance ?? 0;
        $medical = $structure->medical_allowance ?? 0;
        $special = $structure->special_allowance ?? 0;
        $pf = $structure->pf_employee ?? 0;
        $pt = $structure->professional_tax ?? 0;
        $esi = $structure->esi_employee ?? 0;
        $tds= $structure->tds ?? 0;

        return response()->json([
            'success' => true,
            'attendance' => [
                'working_days' => $totalDays,
                'present_days' => $payableDays,
                'paid_leaves'  => $paidLeaves,
                'absent_days'  => $absentDays,
            ],
            'employee' => [
                'name' => $user->name,
                'code' => $user->employee_code ?? 'EMP-' . $user->id,
                'designation' => $user->employee_role ?? $user->designation,
                'department' => $user->department ?? 'Operations',
                'joining_date' => $user->created_at->format('Y-m-d'),
                // FIX: Prioritize Structure Data for Bank Info
                'pan' => $structure->pan_number ?? $user->pan_card ?? '', 
                'account' => $structure->account_number ?? $user->account_number ?? '',
                'bank' => $structure->bank_name ?? $user->bank_name ?? ''
            ],
            'salary' => [
                'basic_salary' => round($base * $prorataFactor, 2),
                'hra' => round(($base * 0.40) * $prorataFactor, 2),
                'conveyance_allowance' => round($conveyance * $prorataFactor, 2),
                'medical_allowance' => round($medical * $prorataFactor, 2),
                'special_allowance' => round($special * $prorataFactor, 2),
                'pf_employee' => $pf,
                'professional_tax' => $pt,
                'esi_employee' => $esi,
                'tds' => $tds,
            ]
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required',
            
         

            // Attendance
            'working_days' => 'required|numeric',
            'present_days' => 'required|numeric',
            'paid_leaves' => 'nullable|numeric',
            'absent_days' => 'nullable|numeric',

            // Earnings
            'basic_salary' => 'required|numeric',
            'hra' => 'nullable|numeric', 
            'conveyance_allowance' => 'nullable|numeric',
            'medical_allowance' => 'nullable|numeric',
            'special_allowance' => 'nullable|numeric',
            'performance_bonus' => 'nullable|numeric',
            'overtime_amount' => 'nullable|numeric',
            
            // Deductions
            'pf_employee' => 'nullable|numeric',
            'esi_employee' => 'nullable|numeric',
            'professional_tax' => 'nullable|numeric',
            'tds' => 'nullable|numeric',
            'loan_deduction' => 'nullable|numeric',
            'leave_deduction' => 'nullable|numeric',
            
            // Totals
            'gross_salary' => 'required|numeric',
            'total_deductions' => 'required|numeric',
            'net_salary' => 'required|numeric',
        ]);

    $user = User::findOrFail($request->user_id);
$structure = SalaryStructure::where('user_id', $user->id)->where('is_active', true)->first();

$salaryData = array_merge($validated, [
    'month' => $request->month . '-01',
    'payslip_number' => 'SAL-' . date('Ym') . '-' . $user->id,
    'status' => 'generated',
    'generated_at' => now(),
    'created_by' => auth()->id(),

    // ✅ EMPLOYEE SNAPSHOT (SAFE)
    'employee_name'   => $user->name,
    'employee_code'   => $user->employee_code ?? 'EMP-'.$user->id,
    'designation'     => $user->employee_role ?? $user->designation,
    'department'      => $user->department ?? 'Operations',
    'joining_date'    => $user->created_at,
    'pan_number'      => $structure->pan_number ?? $user->pan_card,
    'bank_name'       => $structure->bank_name ?? $user->bank_name,
    'account_number'  => $structure->account_number ?? $user->account_number,
]);


        // 3. Create or Update Record
        $salarySlip = SalarySlip::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'month' => $request->month . '-01'
            ],
            $salaryData
        );

        // 4. Generate PDF
        $amountInWords = $this->convertNumberToWords($salarySlip->net_salary);
        $pdf = Pdf::loadView('hr.salary_slips.pdf', compact('salarySlip', 'amountInWords'));
        
        $fileName = 'payslip_' . $salarySlip->user_id . '_' . date('M_Y', strtotime($request->month)) . '.pdf';
        $filePath = 'salary_slips/' . $fileName;
        
        Storage::disk('public')->put($filePath, $pdf->output());
        $salarySlip->update(['pdf_path' => $filePath]);

        // 5. Notify
        try {
            NotificationHelper::sendSalarySlipNotification(
                $salarySlip->user_id,
                $salarySlip->id,
                'Payslip Generated',
                'Your payslip for ' . date('F Y', strtotime($request->month)) . ' is ready.',
                $filePath
            );
        } catch (\Exception $e) {
            Log::error('Notification failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Payslip generated successfully!');
    }

    private function convertNumberToWords($number) {
           if ($number <= 0) {
        return "Zero Rupees Only";
    }

    $number = abs($number);
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'One', '2' => 'Two',
            '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
            '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
            '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
            '13' => 'Thirteen', '14' => 'Fourteen',
            '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
            '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
            '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
            '60' => 'Sixty', '70' => 'Seventy',
            '80' => 'Eighty', '90' => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        return $result . " Rupees Only";
    }

    public function download($id) {
        $slip = SalarySlip::findOrFail($id);
        if ($slip->pdf_path && Storage::disk('public')->exists($slip->pdf_path)) {
            return Storage::disk('public')->download($slip->pdf_path);
        }
        return back()->with('error', 'File not found.');
    }
    
    public function destroy($id) {
        $slip = SalarySlip::findOrFail($id);
        if ($slip->pdf_path && Storage::disk('public')->exists($slip->pdf_path)) {
            Storage::disk('public')->delete($slip->pdf_path);
        }
        $slip->delete();
        return back()->with('success', 'Deleted successfully');
    }
}