<?php

namespace App\Http\Controllers\HrController;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\LeaveBalance;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Models\UserDocument; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Str;

class HrEmployeeController extends Controller
{
    public function indexTeams()
    {
        $teamleads = User::where('designation', 'team_lead')->get();
        $employees = User::withTrashed()
            ->where('designation', 'employee')
            ->with('teamLead')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalEmployees = User::withTrashed()->where('designation', 'employee')->count();
        $activeEmployees = User::where('designation', 'employee')->whereNull('deleted_at')->count();

        return view('hr.employee.index', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'teamleads'
        ));
    }

    // New Method for "Eye" Icon - Fetch Details via AJAX
    public function show($id)
    {
        $employee = User::withTrashed()->with(['teamLead', 'documents', 'documents.uploader'])->findOrFail($id);
        
        // Return a JSON response for the modal
        return response()->json([
            'success' => true,
            'data' => $employee,
            'profile_url' => $employee->profile_photo
    ? asset('storage/' . ltrim($employee->profile_photo, 'storage/'))
    : null,
            'team_lead_name' => $employee->teamLead ? $employee->teamLead->name : 'N/A',
            'formatted_dob' => $employee->dob ? Carbon::parse($employee->dob)->format('d M Y') : 'N/A',
            'documents' => $employee->documents->map(function($doc) {
                return [
                    'name' => $doc->file_name,
                    'url' => asset('storage/' . $doc->file_path),
                    'uploaded_at' => $doc->created_at->format('d M Y')
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'designation' => 'required|in:employee',
                'phone' => 'required|string|max:20',
                'employee_role' => 'nullable|string',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:9999',
                'team_lead_id' => 'required|exists:users,id,designation,team_lead',
                'dob' => 'nullable|date|before:today',
            ]);

            $plainPassword = Str::random(10);
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->designation = 'employee';
            $user->phone = $validated['phone'];
            $user->employee_role = $validated['employee_role'] ?? null;
            $user->address = $validated['address'] ?? null;
            $user->dob = $validated['dob'] ? Carbon::parse($validated['dob']) : null;
            $user->password = Hash::make($plainPassword);
            $user->created_by = Auth::id(); // Fixed: Use Auth::id() instead of team_lead_id for creator
            $user->team_lead_id = $validated['team_lead_id'];

            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

            $user->save();

            // Create LeaveBalance
            LeaveBalance::create([
                'user_id'   => $user->id,
                'leave_type'=> 'casual',
                'total'     => 12,
                'used'      => 0,
                'balance'   => 12,
            ]);

            // Send Email
            try {
                Mail::to($user->email)->send(new UserCredentials($user, $plainPassword));
            } catch (\Exception $mailEx) {
                Log::error('Mail sending failed: ' . $mailEx->getMessage());
                // Don't block creation if mail fails, but flash a warning maybe?
            }

            return redirect()->route('hr.employees.index')->with('success', 'Employee added successfully!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            Log::error('Failed to add employee: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add Employee: ' . $e->getMessage());
        }
    }

   public function update(Request $request, $id)
{
    Log::info('Employee update request started', [
        'user_id' => $id,
        'request_has_documents' => $request->hasFile('documents'),
        'request_files' => array_keys($request->allFiles())
    ]);

    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'employee_role' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:9999',
            'team_lead_id' => 'required|exists:users,id,designation,team_lead',
            'password' => 'nullable|string|min:8',
            'dob' => 'nullable|date|before:today',
            'documents.*' => 'nullable|file|max:10240',
        ]);

        $user = User::findOrFail($id);

        $user->fill([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'employee_role' => $validated['employee_role'] ?? null,
            'address'       => $validated['address'] ?? null,
            'dob'           => $validated['dob'] ? Carbon::parse($validated['dob']) : null,
            'team_lead_id'  => $validated['team_lead_id'],
        ]);

        if ($request->hasFile('profile_photo')) {
            Log::info('Profile photo upload detected', ['user_id' => $user->id]);

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = $request->file('profile_photo')
                                            ->store('profile_photos', 'public');
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        Log::info('Employee basic data updated', ['user_id' => $user->id]);

        // ---------------- DOCUMENT UPLOAD LOGGING ----------------
        if ($request->hasFile('documents')) {

            Log::info('Documents upload started', [
                'user_id' => $user->id,
                'document_count' => count($request->file('documents'))
            ]);

            foreach ($request->file('documents') as $index => $file) {

                Log::info('Processing document', [
                    'index' => $index,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ]);

                $filePath = $file->store(
                    'employee_documents/' . $user->id,
                    'public'
                );

                Log::info('File stored successfully', [
                    'file_path' => $filePath
                ]);

                $doc = UserDocument::create([
                    'user_id'     => $user->id,
                    'file_path'  => $filePath,
                    'file_name'  => $file->getClientOriginalName(),
                    'file_type'  => $file->getClientOriginalExtension(),
                    'uploaded_by'=> Auth::id(),
                    'uploaded_at'=> now(),
                ]);

                Log::info('UserDocument record created', [
                    'user_document_id' => $doc->id
                ]);
            }

        } else {
            Log::warning('No documents found in request', [
                'user_id' => $user->id
            ]);
        }

        // ---------------- LEAVE BALANCE ----------------
        LeaveBalance::firstOrCreate(
            ['user_id' => $user->id, 'leave_type' => 'casual'],
            ['total' => 12, 'used' => 0, 'balance' => 12]
        );

        Log::info('Employee update completed successfully', [
            'user_id' => $user->id
        ]);

        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'Employee updated successfully!');

    } catch (ValidationException $e) {

        Log::error('Employee update validation failed', [
            'errors' => $e->errors(),
            'user_id' => $id
        ]);

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput()
            ->with('error', 'Validation failed');

    } catch (\Exception $e) {

        Log::critical('Employee update failed', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()
            ->with('error', 'Failed to update Employee: ' . $e->getMessage());
    }
}

    public function activate($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            $user->restore();
        }
        return redirect()->back()->with('success', 'Employee activated successfully.');
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Employee deactivated successfully.');
    }

    public function getSalary($id)
    {
        $employee = User::findOrFail($id);
        $salary = $employee->salaryStructure ?? new SalaryStructure();

        return response()->json([
            'success' => true,
            'employee_name' => $employee->name,
            'data' => $salary
        ]);
    }

    public function saveSalary(Request $request, $id)
    {
        $request->validate([
            // Earnings
            'basic_salary' => 'nullable|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',
            'other_earnings' => 'nullable|numeric|min:0',
            
            // Deductions
            'pf_employee' => 'nullable|numeric|min:0',
            'esi_employee' => 'nullable|numeric|min:0',
            'professional_tax' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            
            // Bank Details
            'payment_mode' => 'required|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);

        // Auto-calculate totals to ensure DB consistency
        $gross = $request->basic_salary + $request->hra + $request->conveyance_allowance + 
                 $request->medical_allowance + $request->special_allowance + $request->other_earnings;

        $total_deductions = $request->pf_employee + $request->esi_employee + 
                            $request->professional_tax + $request->tds;

        $net = $gross - $total_deductions;

        SalaryStructure::updateOrCreate(
            ['user_id' => $id],
            [
                // Earnings
                'basic_salary' => $request->basic_salary ?? 0,
                'hra' => $request->hra ?? 0,
                'conveyance_allowance' => $request->conveyance_allowance ?? 0,
                'medical_allowance' => $request->medical_allowance ?? 0,
                'special_allowance' => $request->special_allowance ?? 0,
                'other_earnings' => $request->other_earnings ?? 0,
                'gross_salary' => $gross,

                // Legacy support mapping
                'basic' => $request->basic_salary ?? 0,
                'allowance' => ($request->conveyance_allowance + $request->medical_allowance + $request->special_allowance),
                
                // Deductions
                'pf_employee' => $request->pf_employee ?? 0,
                'esi_employee' => $request->esi_employee ?? 0,
                'professional_tax' => $request->professional_tax ?? 0,
                'tds' => $request->tds ?? 0,
                'total_deductions' => $total_deductions,
                'deductions' => $total_deductions, // legacy

                // Bank Info
                'payment_mode' => $request->payment_mode,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                
                // Meta
                'updated_by' => Auth::id(),
                'is_active' => true
            ]
        );

        return redirect()->back()->with('success', 'Salary structure updated successfully.');
    }
}