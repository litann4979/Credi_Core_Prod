<?php

namespace App\Http\Controllers\AdminController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Document;
use App\Models\FollowUp;
use App\Models\LeadDocument;
use App\Models\LeadForwardedHistory;
use App\Models\LeadHistory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\OfficeRule;
use App\Models\Score;
use App\Models\Target;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminLeadsController extends Controller
{
    public function indexLeads(Request $request)
    {
        $user = Auth::user();

        $leads = Lead::with(['employee', 'teamLead'])
            ->whereHas('forwardedHistories', function ($query) use ($user) {
                $query->where('receiver_user_id', $user->id)
                      ->where('is_forwarded', 1);
            })
            ->latest('created_at')
            ->get();

      $formattedLeads = $leads->map(function ($lead) {
    return [
        'id' => $lead->id,
        'name' => $lead->name,
        'email' => $lead->email ?? '-',
        'loan_account_number' => $lead->loan_account_number ?? '-',
        'dob' => optional($lead->dob)->format('d M Y') ?? '-',
        'state' => $lead->state,
        'district' => $lead->district,
        'city' => $lead->city,
        'phone' => $lead->phone,
        'company' => $lead->company_name ?? '-',

        'amount' => \App\Helpers\FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0),
        'status' => $lead->status,
        'lead_type' => $lead->lead_type,
        'bank_name' => $lead->bank_name ?? '-',
        'expected_month' => $lead->expected_month ?? '-', // Added
        'team_lead_assigned' => $lead->team_lead_id !== null,
        'created_at' => optional($lead->created_at)->format('d M Y'),
        'employee_name' => optional($lead->employee)->name ?? '-',
    ];
});

        return view('admin.leads.index', [
            'forwardedLeads' => $formattedLeads,
        ]);
    }

    public function getLeadDetails($leadId)
{
    try {
        $lead = Lead::with(['employee', 'teamLead'])->findOrFail($leadId);
        $documents = DB::table('lead_document')
            ->join('documents', 'lead_document.document_id', '=', 'documents.id')
            ->where('lead_document.lead_id', $leadId)
            ->select('documents.id as document_id', 'documents.name as document_name', 'lead_document.filepath')
            ->get()
            ->map(function ($doc) {
                return [
                    'document_id' => $doc->document_id,
                    'document_name' => $doc->document_name,
                    'filepath' => $doc->filepath ? Storage::url($doc->filepath) : null,
                ];
            });

              $followUps = FollowUp::where('lead_id', $leadId)
    ->with('user:id,name') // get user info
    ->orderBy('timestamp', 'desc')
    ->get(['id', 'user_id', 'message', 'recording_path', 'timestamp']);


        return response()->json([
            'lead' => [
                'id' => $lead->id,
                'loan_account_number' => $lead->loan_account_number,
                'name' => $lead->name,
                'company_name' => $lead->company_name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'location' => $lead->location,
                'bank_name' => $lead->bank_name,
                'dob' =>$lead->dob?$lead->dob->format('Y-m-d') : null,
                'state' => $lead->state,
                'district' => $lead->district,
                'city' => $lead->city,
                'lead_amount' => $lead->lead_amount,
                'salary' => $lead->salary,
                'expected_month' => $lead->expected_month
                    ? trim((string) $lead->expected_month)
                    : null,
                'status' => $lead->status,
                'lead_type' => $lead->lead_type,
                'turnover_amount' => $lead->turnover_amount,
                'voice_recording' => $lead->voice_recording,
                'employee_name' => optional($lead->employee)->name ?? 'N/A',
                'team_lead_name' => optional($lead->teamLead)->name ?? 'N/A',
                'rejection_reason' => $lead->reason,
            ],
            'documents' => $documents,
             'followUps' => $followUps,
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Lead not found'], 404);
    } catch (Throwable $e) {
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}

  public function upload(Request $request, $leadId, $documentId)
{
    try {
        $request->validate([
            'document_file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:9999', // 9MB limit
        ]);

        $leadDocument = LeadDocument::where('lead_id', $leadId)
            ->where('document_id', $documentId)
            ->firstOrFail();
         $lead = Lead::findOrFail($leadId);

        if ($request->hasFile('document_file')) {
            // Delete the old file if it exists
            if ($leadDocument->filepath && Storage::exists($leadDocument->filepath)) {
                Storage::delete($leadDocument->filepath);
            }

            $filePath = $request->file('document_file')->store('lead_documents', 'public');
            $leadDocument->update([
                'filepath' => $filePath,
                'upload_by' => auth()->id(),
                'uploaded_at' => now(),
                'updated_at' => now(),
            ]);

            //  $lead->touch();

            return response()->json([
                'message' => 'Document uploaded successfully.',
                'filepath' => Storage::url($filePath),
            ], 200);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    } catch (ValidationException $e) {
        return response()->json([
            'error' => true,
            'message' => $e->validator->errors()->first('document_file') ?: 'Validation failed',
        ], 422);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Document not found.'], 404);
    } catch (Throwable $e) {
        return response()->json(['error' => true, 'message' => 'Internal server error'], 500);
    }
}

    public function updateStatus(Request $request, $id)
{

    try {
        $request->validate([
            'status' => 'required|in:personal_lead,authorized,approved,rejected,disbursed,future_lead,login',
            'reason' => 'required_if:status,rejected|nullable|string',
            'loan_account_number' => 'required_if:status,login,approved,disbursed|nullable|string|max:255',
        ]);

        if (!auth()->user()->hasDesignation('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead = Lead::findOrFail($id);
        $lead->status = $request->status;

        if ($request->status === 'rejected') {
            $lead->reason = $request->reason;
        } elseif(in_array($request->status,['login','approved','disbursed'])) {
            $lead->loan_account_number = $request->loan_account_number;
        }

        $lead->save();


        if ($request->status === 'disbursed') {

            DB::beginTransaction();

            try {
                $userId = $lead->employee_id;
                $leadAmount = $lead->lead_amount ?? 0;

                // ✅ 1. Find active target
                $target = Target::where('user_id', $userId)
                    ->where('is_completed', 0)
                    ->where('type', 'lead')
                    ->lockForUpdate() // 🔥 prevents race condition
                    ->first();

                if ($target) {

                    // ✅ 2. Update achieved value
                    $target->achieved_value += $leadAmount;

                    // ✅ 3. Check completion
                    if ($target->achieved_value >= $target->target_value) {
                        $target->is_completed = 1;
                    }

                    $target->save();

                    // ✅ 4. Score Calculation
                    $dailyTarget = $target->target_value / 25;
                    $officeRule = OfficeRule::query()->latest('id')->first();
                    $targetMark = (float) ($officeRule->target_mark ?? 20);
                    if ($targetMark <= 0) {
                        $targetMark = 20;
                    }

                    $targetScoreRaw = $dailyTarget > 0
                        ? ($leadAmount / $dailyTarget) * $targetMark
                        : 0;

                    $targetScore = min($targetMark, $targetScoreRaw);

                    $additionalTargetScore = $targetScoreRaw > $targetMark
                        ? ($targetScoreRaw - $targetMark)
                        : 0;

                    // ✅ 5. Create / Update daily score
                    $score = Score::where('user_id', $userId)
                        ->whereDate('date', today())
                        ->lockForUpdate()
                        ->first();

                    if (!$score) {
                        $score = new Score();
                        $score->user_id = $userId;
                        $score->date = today();
                        $score->target_score = 0;
                        $score->additional_target_score = 0;
                        $score->lead_score = 0;
                        $score->attendance_score = 0;
                        $score->leave_score = 0;
                        $score->additional_lead_score = 0;
                        $score->total_score = 0;
                    }

                    $score->target_score += $targetScore;
                    $score->additional_target_score += $additionalTargetScore;

                    // ✅ Recalculate total
                    $score->total_score =
                        $score->target_score +
                        $score->lead_score +
                        $score->attendance_score +
                        $score->leave_score +
                        $score->additional_target_score +
                        $score->additional_lead_score;

                    $score->save();
                }

                DB::commit(); // ✅ SUCCESS

            } catch (\Exception $e) {
                DB::rollBack(); // ❌ FAIL SAFE
                throw $e; // rethrow so main catch handles it
            }
        }


        DB::table('lead_histories')->insert([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'status' => $request->status,
            'comments' => $request->status === 'rejected' ? $request->reason : ($request->status === 'future_lead' ? 'Marked as future lead' : null),
            'created_at' => now(),
            'updated_at' => now()
        ]);

          // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" has been set"'.$lead->status.'" by Admin.',
        'is_read'   => false,
    ]);

       // 🔔 Push Notification
        NotificationHelper::sendLeadNotification(
            $lead->employee_id,
            $lead->id,
            "Lead Status Updated",
            'Your lead "' . $lead->name . '" is updated to "' . $lead->status . '" by Admin.',
            null
        );
        return response()->json(['message' => 'Lead status updated to ' . $request->status]);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Something went wrong'], 500);
    }
}

   public function updatereditcardStatus(Request $request, $id)
{
    try {
        $request->validate([
            'status' => 'required|in:personal_lead,authorized,approved,rejected,disbursed,future_lead,login',
            'reason' => 'required_if:status,rejected|nullable|string',
            'loan_account_number' => 'required_if:status,login,|nullable|string|max:255',
        ]);

        if (!auth()->user()->hasDesignation('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead = Lead::findOrFail($id);
        $lead->status = $request->status;

        if ($request->status === 'rejected') {
            $lead->reason = $request->reason;
        } elseif(in_array($request->status,['login','approved','disbursed'])) {
            $lead->loan_account_number = $request->loan_account_number;
        }

        $lead->save();
        DB::table('lead_histories')->insert([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'status' => $request->status,
            'comments' => $request->status === 'rejected' ? $request->reason : ($request->status === 'future_lead' ? 'Marked as future lead' : null),
            'created_at' => now(),
            'updated_at' => now()
        ]);

          // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" has been set"'.$lead->status.'" by Admin.',
        'is_read'   => false,
    ]);

       // 🔔 Push Notification
        NotificationHelper::sendLeadNotification(
            $lead->employee_id,
            $lead->id,
            "Lead Status Updated",
            'Your lead "' . $lead->name . '" was updated to "' . $lead->status . '" by Admin.',
            null
        );
        return response()->json(['message' => 'Lead status updated to ' . $request->status]);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Something went wrong'], 500);
    }
}

    public function deleteFile($leadId, $documentId)
{
    $record = LeadDocument::where('lead_id', $leadId)
        ->where('document_id', $documentId)
        ->firstOrFail();

    if ($record->filepath && Storage::exists($record->filepath)) {
        Storage::delete($record->filepath);
    }

    $record->update([
        'filepath' => null,
        'updated_at' => now()
    ]);

    return response()->json(['message' => 'Document removed successfully.']);
}

    public function storeDocument(Request $request, Lead $lead)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:9999',
            ]);

            $document = Document::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $filePath = $request->file('document_file')->store('lead_documents', 'public');

            LeadDocument::create([
                'lead_id' => $lead->id,
                'document_id' => $document->id,
                'upload_by' => Auth::id(),
                'filepath' => $filePath,
                'uploaded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // $lead->touch();
            return response()->json([
                'message' => 'Document added successfully.',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

public function update(Request $request, $id)
{
    try {
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $em = $request->input('expected_month');
        if ($em === '' || $em === null) {
            $request->merge(['expected_month' => null]);
        } elseif (is_string($em)) {
            $trimmed = trim($em);
            $canonical = collect($monthNames)->first(
                fn ($m) => strcasecmp($m, $trimmed) === 0
            );
            $request->merge(['expected_month' => $canonical ?? $trimmed]);
        }

        $request->validate([
            'name'=>'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'loan_account_number' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'state' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'lead_amount' => 'nullable|numeric|min:0',
            'expected_month' => 'nullable|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'lead_type' => 'nullable|string|max:255',
            'turnover_amount' => 'nullable|numeric|min:0',
            'salary' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
        ]);

        $lead = Lead::findOrFail($id);

        // --- BANK CREATION LOGIC START ---
        if (!empty($request->bank_name)) {
            // Check if bank exists (case insensitive search recommended)
            $existingBank = Bank::where('bank_name', $request->bank_name)->first();

            if (!$existingBank) {
                // Create new Bank if it doesn't exist
                Bank::create([
                    'bank_name' => $request->bank_name,
                    'is_active' => true, // Assuming you have this flag
                    // Add default values for other required fields if any
                ]);


            }
        }

        $lead->timestamps = false;
        // Transform the name to proper case (e.g., "MANISH KUMAR" -> "Manish Kumar")
        $name = $request->name ? ucwords(strtolower(trim($request->name))) : null;


        $lead->update([
            'name' => $name,
            'company_name' => $request->company_name,
            'loan_account_number' => $request->loan_account_number,
            'phone' => $request->phone,
            'email' => $request->email,
            'dob' => $request->dob,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'lead_amount' => $request->lead_amount,
            'expected_month' => $request->expected_month,
            'lead_type' => $request->lead_type,
            'turnover_amount' => $request->turnover_amount,
            'salary' => $request->salary,
            'bank_name' => $request->bank_name,
        ]);



        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
    } catch (ValidationException $e) {

        return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], 422);
    } catch (Throwable $e) {
       
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}

public function creditcardUpdate(Request $request, $id)
{
    try {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'state' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'lead_amount' => 'nullable|numeric|min:0',
            'lead_type' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
        ]);

        $lead = Lead::findOrFail($id);

        $lead->timestamps = false;
        // Transform the name to proper case (e.g., "MANISH KUMAR" -> "Manish Kumar")
        $name = $request->name ? ucwords(strtolower(trim($request->name))) : null;


        $lead->update([
            'name' => $name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'dob' => $request->dob,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'lead_amount' => $request->lead_amount,
            'lead_type' => $request->lead_type,
            'salary' => $request->salary,
            'bank_name' => $request->bank_name,
        ]);
        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
    } catch (ValidationException $e) {
        return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], 422);
    } catch (Throwable $e) {
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}

    public function getFilters(Request $request)
{
    try {
        $states = Lead::distinct()->pluck('state')->filter()->values();
        $districts = Lead::distinct()->pluck('district')->filter()->values();
        $cities = Lead::distinct()->pluck('city')->filter()->values();
        $leadTypes = Lead::distinct()->pluck('lead_type')->filter()->values();

        return response()->json([
            'states' => $states,
            'districts' => $districts,
            'cities' => $cities,
            'leadTypes' => $leadTypes,
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'error' => 'Internal server error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

// public function forwardLeadToOperation(Request $request, $leadId)
// {
//     // Admin-only check
//     if (Auth::user()->designation !== 'admin') {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Unauthorized'
//         ], 403);
//     }

//     try {
//         $request->validate([
//             'remarks' => 'nullable|string'
//         ]);

//         // Step 1: Get lead
//         $lead = Lead::findOrFail($leadId);
//         // Step 2: Get employee from lead
//         $employee = User::findOrFail($lead->employee_id);
//         // Step 3: Get team lead who created employee
//         $teamLead = User::findOrFail($employee->team_lead_id);
//         // Step 4: Get operation user who created team lead
//         $operationUser = User::find($teamLead->created_by);
//         if (!$operationUser || $operationUser->designation !== 'operations') {
//             throw new \Exception('No operation user found who created this team lead.');
//         }

//         // Save remarks
//         $lead->remarks = $request->remarks;
//         $lead->save();
//         // Reset previous forwards
//         LeadForwardedHistory::where('lead_id', $lead->id)
//             ->where('is_forwarded', true)
//             ->update(['is_forwarded' => false]);
//         // Create new forward history
//         LeadForwardedHistory::create([
//             'lead_id' => $lead->id,
//             'sender_user_id' => Auth::id(),
//             'receiver_user_id' => $operationUser->id,
//             'is_forwarded' => true,
//             'forwarded_at' => now()
//         ]);
//         // Insert into lead_histories
//         DB::table('lead_histories')->insert([
//             'lead_id' => $lead->id,
//             'user_id' => Auth::id(),
//             'action' => 'forwarded',
//             'status' => $lead->status,
//             'forwarded_to' => $operationUser->id,
//             'comments' => $request->remarks ?? 'Lead forwarded to operations team',
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);
//         return response()->json([
//             'status' => 'success',
//             'message' => 'Lead forwarded to operations team'
//         ]);

//     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Lead not found'
//         ], 404);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }

public function forwardLeadToOperationByAdmin(Request $request, $leadId)
{
    // Admin-only check
    if (Auth::user()->designation !== 'admin') {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 403);
    }

    try {
        $request->validate([
            'remarks' => 'nullable|string'
        ]);

        // Step 1: Get lead
        $lead = Lead::findOrFail($leadId);
        // Step 2: Get employee from lead
        $employee = User::findOrFail($lead->employee_id);
        // Step 3: Get team lead who created employee
        $teamLead = User::findOrFail($employee->team_lead_id);
        // Step 4: Get operation user who created team lead
        $operationUser = User::find($teamLead->created_by);
        if (!$operationUser || $operationUser->designation !== 'operations') {
            throw new \Exception('No operation user found who created this team lead.');
        }


         // Check if lead was already forwarded by admin
        $alreadyForwarded = LeadForwardedHistory::where('lead_id', $leadId)
            ->where('receiver_user_id', $operationUser->id)
            ->where('is_forwarded', true)
            ->exists();

        if ($alreadyForwarded) {
            return response()->json([
                'status' => 'error',
                'message' => 'This lead has already been forwarded to operations by admin'
            ], 400);
        }
        // Save remarks
        $lead->remarks = $request->remarks;
        $lead->save();
        // Set all previous forwards of this lead to false
LeadForwardedHistory::where('lead_id', $lead->id)
    ->where('is_forwarded', true)
    ->update(['is_forwarded' => false]);

        // Create new forward history
        LeadForwardedHistory::create([
            'lead_id' => $lead->id,
            'sender_user_id' => Auth::id(),
            'receiver_user_id' => $operationUser->id,
            'is_forwarded' => true,
            'forwarded_at' => now()
        ]);
        // Insert into lead_histories
        DB::table('lead_histories')->insert([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'forwarded',
            'status' => $lead->status,
            'forwarded_to' => $operationUser->id,
            'comments' => $request->remarks ?? 'Lead forwarded to operations team',
            'created_at' => now(),
            'updated_at' => now()
        ]);
          // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" has been forwarded to operation by Admin.',
        'is_read'   => false,
    ]);

    // 🔔 Push Notification
NotificationHelper::sendLeadNotification(
    $lead->employee_id,
    $lead->id,
    "Lead Forwarded",
    'Your lead "' . $lead->name . '" has been forwarded to Operations by Admin.',
    null
);

        return response()->json([
            'status' => 'success',
            'message' => 'Lead forwarded to operations team'
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lead not found'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
/**
 * Soft delete a lead and record in LeadHistory
 */
public function destroy(Lead $lead)
{
    try {
        DB::beginTransaction();

        LeadHistory::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'soft_deleted',
            'status' => $lead->status,
            'comments' => 'Lead soft deleted by admin',
        ]);

        $lead->delete(); // Soft delete

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Lead deleted successfully',
        ]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete lead: ' . $e->getMessage(),
        ], 500);
    }
}


/**
 * Get all soft-deleted leads (admin only)
 */
public function getDeletedLeads(){
    try {
        $user = Auth::user();

        if ($user->designation !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to view deleted leads',
            ], 403);
        }

        $deletedLeads = Lead::onlyTrashed()
            ->with([
                'employee',
                'teamLead',
                'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc')->with('user');
                },
            ])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted leads retrieved successfully',
            'data' => $deletedLeads,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch deleted leads: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Restore a soft-deleted lead
 */
public function restore(int $leadId)
{
    $user = Auth::user();

    if ($user->designation !== 'admin') {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized to restore leads',
        ], 403);
    }

    $lead = Lead::withTrashed()->findOrFail($leadId);

    if (!$lead->trashed()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lead is not deleted',
        ], 422);
    }

    try {
        DB::beginTransaction();

        LeadHistory::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'restored',
            'status' => $lead->status,
            'comments' => 'Lead restored by admin',
        ]);

        $lead->restore();

        $lead->load([
            'employee',
            'teamLead',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            },
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Lead restored successfully',
            'data' => $lead,
        ]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to restore lead: ' . $e->getMessage(),
        ], 500);
    }
}

public function showDeletedLeadsPage()
{
    return view('admin.leads.deleted_leads');
}






}
