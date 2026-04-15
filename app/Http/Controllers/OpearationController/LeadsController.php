<?php

namespace App\Http\Controllers\OpearationController;

use App\Helpers\FormatHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Document;
use App\Models\FollowUp;
use App\Models\LeadDocument;
use App\Models\LeadForwardedHistory;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\OfficeRule;
use App\Models\Score;
use App\Models\Target;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class LeadsController extends Controller
{
public function indexLeads(Request $request)
{
    $user = Auth::user();

    $forwardedHistories = LeadForwardedHistory::with(['lead.employee', 'lead.teamLead'])
        ->where('is_forwarded', 1)
        ->where('receiver_user_id', $user->id)
        ->latest('forwarded_at')
        ->get();

    // Format the leads for your Blade
    $formattedLeads = $forwardedHistories->map(function ($history) {
        $lead = $history->lead;
if (!$lead) {
        return null; // Skip if no lead found
    }

    return [
        'id' => $lead->id,
        'name' => $lead->name,
        'loan_account_number' => $lead->loan_account_number ?? '-',
        'bank_name' => $lead->bank_name ?? '-',
        'email' => $lead->email ?? '-',
        'dob' => $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('d M Y') : '-',
        'state' => $lead->state,
        'district' => $lead->district,
        'city' => $lead->city,
        'phone' => $lead->phone,
        'company' => $lead->company_name ?? '-',
        'amount' => FormatHelper::formatToIndianCurrency($lead->lead_amount ?? 0),
        'status' => $lead->status,
        'lead_type' => $lead->lead_type,
        'team_lead_assigned' => $lead->team_lead_id !== null,
        'created_at' => $lead->created_at ? $lead->created_at->format('d M Y') : '-',
        'employee_name' => optional($lead->employee)->name ?? '-',
    ];
})->filter(); // Remove null entries

    return view('Opearation.leads.index', [
        'forwardedLeads' => $formattedLeads,
    ]);
}



public function getLeadDetails($leadId)
{
    try {
        Log::info('Fetching lead details for lead_id: ' . $leadId);

        $lead = Lead::with(['employee', 'teamLead'])->findOrFail($leadId);
        Log::info('Lead fetched: ' . json_encode($lead));

        $documents = DB::table('lead_document')
            ->join('documents', 'lead_document.document_id', '=', 'documents.id')
            ->where('lead_document.lead_id', $leadId)
            ->select(
                'documents.id as document_id',
                'documents.name as document_name',
                'lead_document.filepath'
            )
            ->get();


            $followUps = FollowUp::where('lead_id', $leadId)
    ->with('user:id,name') // get user info
    ->orderBy('timestamp', 'desc')
    ->get(['id', 'user_id', 'message', 'recording_path', 'timestamp']);


        return response()->json([
            'lead' => [
                'name' => $lead->name,
                'loan_account_number' => $lead->loan_account_number,
                'company_name' => $lead->company_name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'location' => $lead->location,
                'bank_name' => $lead->bank_name,
                'dob' => $lead->dob?$lead->dob->format('Y-m-d') : null,
                'state' => $lead->state,
                'district' => $lead->district,
                'city' => $lead->city,
                'lead_amount' => $lead->lead_amount,
                'salary' => $lead->salary,
                'expected_month' => $lead->expected_month,
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
    } catch (Throwable $e) {
        Log::error("Error in getLeadDetails: " . $e->getMessage(), ['lead_id' => $leadId]);
        return response()->json([
            'error' => 'Internal server error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function upload(Request $request, $leadId, $documentId)
{
    $request->validate([
        'document_file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:9999',
    ]);

    $file = $request->file('document_file');
    $path = $file->store('lead_documents', 'public');

    $lead = Lead::findOrFail($leadId);

    DB::table('lead_document')
        ->where('lead_id', $leadId)
        ->where('document_id', $documentId)
        ->update([
            'filepath' => $path,
            'upload_by' => auth()->id(),
            'uploaded_at' => now(),
            'updated_at' => now(),
        ]);

    $message = 'Document uploaded successfully.';

    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['message' => $message]);
    }

    return redirect()->back()->with('success', $message);
}

public function updateStatus(Request $request, $id)
{
    Log::info('Request received to update lead status', [
        'lead_id' => $id,
        'request_data' => $request->except('voice_recording'),
        'has_voice_recording' => $request->hasFile('voice_recording'),
        'user_id' => auth()->id()
    ]);

    try {
        $request->validate([
            'status' => 'required|in:approved,rejected,disbursed,login',
            'reason' => 'required_if:status,rejected|nullable|string',
            'loan_account_number' => 'nullable|string|max:255',
            'voice_recording' => 'nullable|file|mimes:webm,mp3,wav,m4a,ogg,aac,mp4|max:10240', // 10MB max
        ]);

        if (!auth()->user()->hasDesignation('operations')) {
            Log::warning('Unauthorized status update attempt', [
                'user_id' => auth()->id(),
                'lead_id' => $id
            ]);
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead = Lead::findOrFail($id);
        $lead->status = $request->status;
        $voiceRecordingPath = null;

        if ($request->status === 'rejected') {
            $lead->reason = $request->reason;

            // Handle voice recording upload
            if ($request->hasFile('voice_recording')) {
                try {
                    $file = $request->file('voice_recording');
                    $originalExtension = $file->getClientOriginalExtension();

                    // Use webm if no extension or set proper extension
                    $extension = $originalExtension ?: 'webm';

                    $filename = 'rejection_voice_' . $id . '_' . time() . '.' . $extension;
                    $path = $file->storeAs('voice_recordings', $filename, 'public');
                    $lead->voice_recording = $path;
                    $voiceRecordingPath = $path;

                    Log::info('Voice recording uploaded successfully', [
                        'lead_id' => $id,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error uploading voice recording', [
                        'lead_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue without voice recording if upload fails
                }
            }
        }

        if (in_array($request->status, ['login', 'approved', 'disbursed']) && $request->filled('loan_account_number')) {
            $lead->loan_account_number = $request->loan_account_number;
        }

        $lead->save();

        if ($request->status === 'disbursed') {
            DB::beginTransaction();

            try {
                $leadAmount = (float) ($lead->lead_amount ?? 0);

                if ($lead->employee_id) {
                    $this->applyDisbursedIncrementToUserTargetAndTodayScore(
                        (int) $lead->employee_id,
                        $leadAmount,
                        false
                    );
                }

                $teamLeadUserId = $this->resolveTeamLeadUserId($lead);
                if (
                    $teamLeadUserId
                    && (!$lead->employee_id || $teamLeadUserId !== (int) $lead->employee_id)
                ) {
                    $this->applyDisbursedIncrementToUserTargetAndTodayScore(
                        $teamLeadUserId,
                        $leadAmount,
                        true
                    );
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        // ✅ Always insert new history
        DB::table('lead_histories')->insert([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'status' => $request->status,
            'comments' => $request->status === 'rejected' ? $request->reason : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info('Lead history inserted', [
            'lead_id' => $lead->id,
            'user_id' => auth()->id()
        ]);

        // 🔔 Create notification for the employee who owns the lead
        Notification::create([
            'user_id'   => $lead->employee_id,
            'lead_id'   => $lead->id,
            'message'   => 'Your lead "' . $lead->name . '" has been set to "' . $lead->status . '" by Operation.',
            'is_read'   => false,
        ]);
           // 2️⃣ Push Notification
        NotificationHelper::sendLeadNotification(
            $lead->employee_id,
            $lead->id,
            'Lead Status Updated',
            'Your lead "' . $lead->name . '" has been updated to "' . $lead->status . '".',
            $voiceRecordingPath
        );
        return response()->json([
            'message' => 'Lead status updated to ' . $request->status,
            'voice_recording_path' => $voiceRecordingPath
        ]);

    } catch (\Exception $e) {
        Log::error('Error while updating lead status', [
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'lead_id' => $id,
            'user_id' => auth()->id()
        ]);
        return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
    }
}





public function forwardToAdmin($id)
{
    $lead = Lead::findOrFail($id);

    // Ensure only "operations" users can perform this
    if (!auth()->user()->hasDesignation('operations')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Get first admin (or adjust logic as needed)
    $admin = User::where('designation', 'admin')->first();
    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }

    // ✅ Step 1: Set all previous forward records for this lead to is_forwarded = 0
    DB::table('lead_forwarded_histories')
        ->where('lead_id', $lead->id)
        ->where('receiver_user_id',auth()->id())
        ->where('is_forwarded', 1)
        ->update([
            'is_forwarded' => 0,
            'updated_at' => now()
        ]);

    // ✅ Step 2: Insert a new forward record with is_forwarded = 1
    DB::table('lead_forwarded_histories')->insert([
        'lead_id' => $lead->id,
        'sender_user_id' => auth()->id(),
        'receiver_user_id' => $admin->id,
        'is_forwarded' => 1,
        'forwarded_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);


    // ✅ Step 4: Handle lead_histories (insert or update Forwarded action)
    $updated = DB::table('lead_histories')
        ->where('lead_id', $lead->id)
        ->update([
            'action'=>'forwarded',
            'status' => $lead->status,
            'forwarded_to' => $admin->id,
            'comments'=>'Forwarded to Admin ID -'.$admin->id,
            'updated_at' => now()
        ]);

    if ($updated === 0) {
        DB::table('lead_histories')->insert([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'Forwarded',
            'status' => $lead->status,
            'forwarded_to' => $admin->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return response()->json(['message' => 'Lead successfully forwarded to admin.']);
}

public function deleteFile($leadId, $documentId)
{
    try {
        $record = LeadDocument::where('lead_id', $leadId)
                    ->where('document_id', $documentId)
                    ->firstOrFail();

        // Delete file from storage if it exists
        if ($record->filepath && Storage::disk('public')->exists($record->filepath)) {
            Storage::disk('public')->delete($record->filepath);
        }

        // Update the row to nullify the file path
        $record->update([
            'filepath' => null,
            'updated_at' => now()
        ]);

        // Return JSON response instead of redirect
        return response()->json([
            'success' => true,
            'message' => 'Document removed successfully. You can now upload it again.'
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error deleting document: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete document: ' . $e->getMessage()
        ], 500);
    }
}

public function storeDocument(Request $request, Lead $lead)
    {
        try {
            Log::info('Adding new document for lead_id: ' . $lead->id);

            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:9999',
            ]);

            // Create a new document record
            $document = Document::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Handle file upload
            $filePath = $request->file('document_file')->store('lead_documents', 'public');

            // Create lead_document record
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
            Log::error("Validation error in storeDocument: " . $e->getMessage(), ['lead_id' => $lead->id]);
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error("Error in storeDocument: " . $e->getMessage(), ['lead_id' => $lead->id]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
{
     try {
        $request->validate([
             'name' => 'required|string|max:255',
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

                Log::info("New Bank Created via Lead Edit: " . $request->bank_name);
            }
        }

         $lead->timestamps = false; // Disable automatic timestamps
        $lead->update([
            'name' => $request->name,
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

        if ($lead->status === 'disbursed') {
            DB::transaction(function () use ($lead) {
                $lead->refresh();
                $this->recalculateOpenLeadTargetAndTodayScoreForLeadOwners($lead);
            });
        }

        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
    } catch (ValidationException $e) {
        Log::error("Validation error in update: " . $e->getMessage(), ['lead_id' => $id]);
        return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], 422);
    } catch (Throwable $e) {
        Log::error("Error in update: " . $e->getMessage(), ['lead_id' => $id]);
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}


  public function updatereditcardStatus(Request $request, $id)
{
    Log::info('Request received to update lead status', [
        'lead_id' => $id,
        'request_data' => $request->all(),
        'user_id' => auth()->id()
    ]);

    try {
        $request->validate([
            'status' => 'required|in:personal_lead,authorized,approved,rejected,disbursed,future_lead,login',
            'reason' => 'required_if:status,rejected|nullable|string',
            'loan_account_number' => 'required_if:status,login,|nullable|string|max:255',
        ]);

        if (!auth()->user()->hasDesignation('operations')) {
            Log::warning('Unauthorized status update attempt', [
                'user_id' => auth()->id(),
                'lead_id' => $id
            ]);
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

        Log::info('Lead status updated', [
            'lead_id' => $lead->id,
            'new_status' => $request->status
        ]);

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
        'message'   => 'Your lead "' . $lead->name . '" has been set"'.$lead->status.'" by Operation.',
        'is_read'   => false,
    ]);

        // 2️⃣ Push Notification
        NotificationHelper::sendLeadNotification(
            $lead->employee_id,
            $lead->id,
            'Lead Status Updated',
            'Your lead "' . $lead->name . '" has been updated to "' . $lead->status . '".',
            null
        );

        Log::info('Lead history inserted', [
            'lead_id' => $lead->id,
            'user_id' => auth()->id()
        ]);

        return response()->json(['message' => 'Lead status updated to ' . $request->status]);

    } catch (\Exception $e) {
        Log::error('Error while updating lead status', [
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'lead_id' => $id,
            'user_id' => auth()->id()
        ]);
        return response()->json(['message' => 'Something went wrong'], 500);
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

        if ($lead->status === 'disbursed') {
            DB::transaction(function () use ($lead) {
                $lead->refresh();
                $this->recalculateOpenLeadTargetAndTodayScoreForLeadOwners($lead);
            });
        }

        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
    } catch (ValidationException $e) {
        Log::error("Validation error in update: " . $e->getMessage(), ['lead_id' => $id]);
        return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], 422);
    } catch (Throwable $e) {
        Log::error("Error in update: " . $e->getMessage(), ['lead_id' => $id]);
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}


  /**
     * Team lead for scoring: lead.team_lead_id, else employee.users.team_lead_id.
     */
    private function resolveTeamLeadUserId(Lead $lead): ?int
    {
        if ($lead->team_lead_id) {
            return (int) $lead->team_lead_id;
        }
        if (!$lead->employee_id) {
            return null;
        }
        $emp = User::query()->find($lead->employee_id);

        return $emp && $emp->team_lead_id ? (int) $emp->team_lead_id : null;
    }

    /**
     * On disburse: increment open lead-target achieved_value and refresh today's target_score (employee vs team-lead aggregate).
     *
     * @param  bool  $aggregateByTeamLeadOnLead  If true, today's sum uses leads.team_lead_id; else leads.employee_id.
     */
    private function applyDisbursedIncrementToUserTargetAndTodayScore(
        int $userId,
        float $leadAmount,
        bool $aggregateByTeamLeadOnLead
    ): void {
        if ($userId <= 0) {
            return;
        }

        $target = Target::where('user_id', $userId)
            ->where('is_completed', 0)
            ->where('type', 'lead')
            ->lockForUpdate()
            ->first();

        if (!$target) {
            return;
        }

        if ($leadAmount > 0) {
            $target->achieved_value += (int) round($leadAmount);

            if ($target->achieved_value >= $target->target_value) {
                $target->is_completed = 1;
            }

            $target->save();
        }

        $this->refreshTodayTargetScoreForUser($userId, $target, $aggregateByTeamLeadOnLead);
    }

    /**
     * After lead fields change while disbursed: realign achieved_value from all disbursed leads and refresh today's score.
     */
    private function recalculateOpenLeadTargetAndTodayScoreForLeadOwners(Lead $lead): void
    {
        if ($lead->status !== 'disbursed') {
            return;
        }

        if ($lead->employee_id) {
            $this->recalculateOpenLeadTargetAndTodayScoreForUser(
                (int) $lead->employee_id,
                false
            );
        }

        $teamLeadUserId = $this->resolveTeamLeadUserId($lead);
        if (
            $teamLeadUserId
            && (!$lead->employee_id || $teamLeadUserId !== (int) $lead->employee_id)
        ) {
            $this->recalculateOpenLeadTargetAndTodayScoreForUser($teamLeadUserId, true);
        }
    }

    private function recalculateOpenLeadTargetAndTodayScoreForUser(
        int $userId,
        bool $aggregateByTeamLeadOnLead
    ): void {
        if ($userId <= 0) {
            return;
        }

        $target = Target::where('user_id', $userId)
            ->where('is_completed', 0)
            ->where('type', 'lead')
            ->lockForUpdate()
            ->first();

        if (!$target) {
            return;
        }

        $scopeQuery = Lead::query()->where('status', 'disbursed');
        if ($aggregateByTeamLeadOnLead) {
            $scopeQuery->where('team_lead_id', $userId);
        } else {
            $scopeQuery->where('employee_id', $userId);
        }

        $target->achieved_value = (int) round((float) (clone $scopeQuery)->sum('lead_amount'));
        $target->is_completed = $target->achieved_value >= $target->target_value ? 1 : 0;
        $target->save();

        $this->refreshTodayTargetScoreForUser($userId, $target, $aggregateByTeamLeadOnLead);
    }

    private function refreshTodayTargetScoreForUser(
        int $userId,
        Target $target,
        bool $aggregateByTeamLeadOnLead
    ): void {
        $totalAchieved = (float) Lead::query()
            ->where('status', 'disbursed')
            ->whereDate('updated_at', today())
            ->when(
                $aggregateByTeamLeadOnLead,
                fn ($q) => $q->where('team_lead_id', $userId),
                fn ($q) => $q->where('employee_id', $userId)
            )
            ->sum('lead_amount');

        $dailyTarget = (float) ($target->target_value / 25);
        $officeRule = OfficeRule::query()->latest('id')->first();
        $targetMark = (float) ($officeRule->target_mark ?? 20);
        if ($targetMark <= 0) {
            $targetMark = 20;
        }

        $targetScoreRaw = $dailyTarget > 0
            ? ($totalAchieved / $dailyTarget) * $targetMark
            : 0.0;

        $targetScore = min($targetMark, $targetScoreRaw);
        $additionalTargetScore = max(0, $targetScoreRaw - $targetMark);

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
            $score->discipline_score = 0;
            $score->attendance_score = 0;
            $score->leave_score = 0;
            $score->additional_lead_score = 0;
            $score->total_score = 0;
        }

        $score->target_score = $targetScore;
        $score->additional_target_score = $additionalTargetScore;
        $score->total_score =
            (float) ($score->target_score ?? 0) +
            (float) ($score->lead_score ?? 0) +
            (float) ($score->discipline_score ?? 0) +
            (float) ($score->leave_score ?? 0);

        $score->save();
    }

}

