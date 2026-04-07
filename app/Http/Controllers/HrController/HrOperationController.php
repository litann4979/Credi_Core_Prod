<?php

namespace App\Http\Controllers\HrController;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\LeaveBalance;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Models\UserDocument; // Imported
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Str;

class HrOperationController extends Controller
{
    public function indexOperation()
    {
        $teamleads = User::where('designation', 'operations')
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalTeamleads = $teamleads->count();
        $activeTeamleads = $teamleads->whereNull('deleted_at')->count();

        return view('hr.operation.index', compact('teamleads', 'totalTeamleads', 'activeTeamleads'));
    }

    // New Method for "Eye" Icon - Fetch Details via AJAX
    public function show($id)
    {
        $operation = User::withTrashed()->with(['documents', 'documents.uploader'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $operation,
            'profile_url' => $operation->profile_photo ? asset('storage/' . ltrim($operation->profile_photo, 'storage/')) : null,
            'formatted_dob' => $operation->dob ? Carbon::parse($operation->dob)->format('d M Y') : 'N/A',
            'documents' => $operation->documents->map(function($doc) {
                return [
                    'name' => $doc->file_name,
                    'url' => asset('storage/' . $doc->file_path),
                    'uploaded_at' => $doc->created_at->format('d M Y')
                ];
            })
        ]);
    }

    public function storeOperation(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'designation' => 'required|in:operations',
                'photo' => 'nullable|image|max:9999',
                'dob' => 'nullable|date|before:today',
            ]);

            $plainPassword = Str::random(10);
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->designation = 'operations';
            $user->address = $validated['address'] ?? null;
            $user->dob = $validated['dob'] ? Carbon::parse($validated['dob']) : null;
            $user->password = Hash::make($plainPassword);
            $user->created_by = Auth::id();

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo')->store('profile_photos', 'public');
                $user->profile_photo = $photo;
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

            try {
                Mail::to($user->email)->send(new UserCredentials($user, $plainPassword));
            } catch (\Exception $e) {
                Log::error("Mail failed for Operation: " . $e->getMessage());
            }

            return redirect()->route('hr.operations.index')->with('success', 'Operation added successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Operation: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'photo' => 'nullable|image|max:9999',
                'password' => 'nullable|string|min:8',
                'dob' => 'nullable|date|before:today',
                'documents.*' => 'nullable|file|max:10240',
            ]);

            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->address = $validated['address'] ?? null;
            $user->dob = $validated['dob'] ? Carbon::parse($validated['dob']) : null;

            if ($request->hasFile('photo')) {
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $path = $request->file('photo')->store('profile_photos', 'public');
                $user->profile_photo = $path;
            }

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            // Handle Multiple Documents Upload
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('operation_documents/' . $user->id, 'public');

                    UserDocument::create([
                        'user_id' => $user->id,
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_type' => $file->getClientOriginalExtension(),
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }

            // Ensure LeaveBalance exists
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type' => 'casual'],
                ['total' => 12, 'used' => 0, 'balance' => 12]
            );

            return redirect()->route('hr.operations.index')->with('success', 'Operation updated successfully!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Validation failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Operation: ' . $e->getMessage());
        }
    }

    public function activate($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            $user->restore();
        }
        return redirect()->back()->with('success', 'Operation activated successfully.');
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Operation deactivated successfully.');
    }

    public function edit($id)
    {
        // Not typically used with modal approach, but kept for compatibility
        $teamlead = User::where('designation', 'operations')->findOrFail($id);
        $teamleads = User::where('designation', 'operations')->withTrashed()->get();
        return view('hr.operation.index', compact('teamlead', 'teamleads'));
    }
   public function getSalary($id)
    {
        $operation = User::findOrFail($id);
        
        // Fetch active structure or return empty
        $salary = SalaryStructure::where('user_id', $id)->where('is_active', true)->first();

        return response()->json([
            'success' => true,
            'user_name' => $operation->name,
            'designation' => $operation->designation, // or employee_role
            'exists' => $salary ? true : false,
            'data' => $salary ?? []
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
            'performance_bonus' => 'nullable|numeric|min:0',
            'other_earnings' => 'nullable|numeric|min:0',
            
            // Deductions
            'pf_employee' => 'nullable|numeric|min:0',
            'esi_employee' => 'nullable|numeric|min:0',
            'professional_tax' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            
            // Bank Details
            'payment_mode' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        // Auto-calculate totals
        $earnings = [
            'basic_salary' => $request->basic_salary ?? 0,
            'hra' => $request->hra ?? 0,
            'conveyance_allowance' => $request->conveyance_allowance ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'special_allowance' => $request->special_allowance ?? 0,
            'performance_bonus' => $request->performance_bonus ?? 0,
            'other_earnings' => $request->other_earnings ?? 0,
        ];
        
        $gross = array_sum($earnings);

        $deductions = [
            'pf_employee' => $request->pf_employee ?? 0,
            'esi_employee' => $request->esi_employee ?? 0,
            'professional_tax' => $request->professional_tax ?? 0,
            'tds' => $request->tds ?? 0,
        ];

        $total_deductions = array_sum($deductions);

        SalaryStructure::updateOrCreate(
            ['user_id' => $id],
            array_merge($earnings, $deductions, [
                // Meta
                'employee_code' => $user->employee_code ?? $user->id,
                'designation' => $user->designation, // Operations usually utilize 'designation' field
                'department' => $user->department ?? 'Operations',
                
                // Totals
                'gross_salary' => $gross,
                'total_deductions' => $total_deductions,
                
                // Legacy Mapping (Optional)
                'basic' => $earnings['basic_salary'],
                'allowance' => ($gross - $earnings['basic_salary']),
                'deductions' => $total_deductions,

                // Bank Info
                'payment_mode' => $request->payment_mode,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                
                // Meta
                'is_active' => true,
                'updated_by' => Auth::id(),
                'created_by' => Auth::id() // Only used on create
            ])
        );

        return response()->json(['success' => true, 'message' => 'Salary structure saved successfully.']);
    }
}