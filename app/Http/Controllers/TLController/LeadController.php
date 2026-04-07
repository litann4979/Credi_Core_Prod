<?php

namespace App\Http\Controllers\TLController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Document;
use App\Models\LeadDocument;
use App\Models\LeadForwardedHistory;
use App\Models\LeadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class LeadController extends Controller
{


// public function indexLeads(Request $request)
// {
//     $user = Auth::user();
//     $query = Lead::with(['employee', 'teamLead'])
//         ->whereNull('deleted_at');

//     // 🔹 Show leads forwarded to the logged-in team lead
//     $forwardedLeadIds = LeadForwardedHistory::where('receiver_user_id', $user->id)
//         ->where('is_forwarded', 1)
//         ->pluck('lead_id')
//         ->toArray();

//     // 🔹 Leads either assigned to the team lead or forwarded to them
//     $query->where(function ($q) use ($user, $forwardedLeadIds) {
//         $q->where('team_lead_id', $user->id)
//           ->orWhereIn('id', $forwardedLeadIds);
//     });

//     // Default to current month's leads
//     // $query->whereMonth('created_at', Carbon::now()->month)
//     //       ->whereYear('created_at', Carbon::now()->year);

//     // ... (keep rest of your filter logic as is)

//     // Final paginate
//     $leads = $query->orderBy('created_at', 'desc')->paginate(10);

//     // Format leads
//    $formattedLeads = $leads->getCollection()->map(function ($lead) {
//     return [
//         'id' => $lead->id,
//         'name' => $lead->name,
//         'email' => $lead->email ?? '-',
//         'phone' => $lead->phone,
//         'location' => $lead->city ? "{$lead->city}, {$lead->state}" : ($lead->state ?? '-'),
//         'company' => $lead->company_name ?? '-',
//         'position' => $lead->position ?? '-',
//         'industry' => $lead->lead_type ?? '-',
//         'website' => $lead->website ?? '-',
//         'amount' => (int) $lead->lead_amount,
//         'status' => $lead->status,
//         'source' => $lead->lead_source ?? '-',
//         'expected_date' => $lead->expected_month ? Carbon::parse($lead->expected_month)->format('M d, Y') : '-',
//         'notes' => $lead->remarks ?? '-',
//         'created_at' => $lead->created_at->format('d M Y'),
//         'assigned' => $lead->team_lead_id !== null,
//         'team_lead_assigned' => $lead->team_lead_id ? true : false,
//         'employee_name' => $lead->employee ? $lead->employee->name : '-',
//         'team_lead_name' => $lead->teamLead ? $lead->teamLead->name : '-'
//     ];
// });

//    Log::info('Formatted Leads Count: ' . $formattedLeads->count());

//     // Pass data to the view
//     return view('TeamLead.leads.index', [
//         'leads' => $leads,
//         'formattedLeads' => $formattedLeads
//     ]);
// }


    // public function forwardToTeamLead(Request $request, Lead $lead)
    // {
    //     $user = Auth::user();

    //     // Authorization: Only the employee who created the lead can forward it
    //     if ($lead->employee_id !== $user->id || !$lead->is_personal_lead || $lead->status !== 'personal_lead') {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Unauthorized to forward this lead or lead is not a personal lead.'
    //         ], 403);
    //     }

    //     // Validate request
    //     $request->validate([
    //         'team_lead_id' => 'required|exists:users,id',
    //         'remarks' => 'nullable|string|max:1000'
    //     ]);

    //     // Update lead
    //     $lead->update([
    //         'team_lead_id' => $request->team_lead_id,
    //         'remarks' => $request->remarks ?? $lead->remarks,
    //         'is_personal_lead' => false
    //     ]);

    //     // Log history
    //     $lead->histories()->create([
    //         'user_id' => $user->id,
    //         'action' => 'forwarded_to_team_lead',
    //         'remarks' => $request->remarks ?? 'Lead forwarded to team lead by employee.'
    //     ]);

    //     // Notify team lead
    //     $teamLead = User::find($request->team_lead_id);
    //     if ($teamLead) {
    //         Notification::send($teamLead, new NewLeadAssigned($lead, $user));
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Lead forwarded to team lead successfully.'
    //     ]);
    // }




public function indexLeads(Request $request)
{
    $user = Auth::user();

    // 🔹 Get only lead IDs that are actively forwarded to this team lead
    $forwardedLeadIds = LeadForwardedHistory::where('receiver_user_id', $user->id)
        ->where('is_forwarded', true)
        ->pluck('lead_id')
        ->toArray();

    // 🔹 Main query: Only forwarded leads
    $query = Lead::with(['employee', 'teamLead'])
        ->whereIn('id', $forwardedLeadIds)
        ->whereNull('deleted_at');

    // 🔹 Filter: Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🔹 Filter: Assignment
    if ($request->filled('assignment')) {
        if ($request->assignment === 'assigned') {
            $query->whereNotNull('team_lead_id');
        } elseif ($request->assignment === 'unassigned') {
            $query->whereNull('team_lead_id');
        }
    }

    // 🔹 Filter: Date Range
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // 🔹 Filter: Search (by name or company)
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('company_name', 'like', "%{$searchTerm}%");
        });
    }

    // 🔹 Pagination and formatting
    $leads = $query->orderBy('created_at', 'desc')->get();

    $formattedLeads = $leads->map(function ($lead) {
    return [
        'id' => $lead->id,
        'name' => $lead->name,
        'email' => $lead->email ?? '-',
        'phone' => $lead->phone,
        'loan_account_number' => $lead->loan_account_number ?? '-',
       'dob' => $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('Y-m-d') : null,
        'district' => $lead->district ?? '-',
        'salary' => $lead->salary ?? '-',
        'location' => $lead->city ? "{$lead->city}, {$lead->state}" : ($lead->state ?? '-'),
        'company' => $lead->company_name ?? '-',
        'position' => $lead->position ?? '-',
        'lead_type' => $lead->lead_type ?? '-',
        'website' => $lead->website ?? '-',
        'amount' => (int) $lead->lead_amount,
        'status' => $lead->status,
        'source' => $lead->lead_source ?? '-',
        'expected_month' => $lead->expected_month ?? '-',
        'notes' => $lead->remarks ?? '-',
        'created_at' => $lead->created_at->format('d M Y'),
        'assigned' => $lead->team_lead_id !== null,
        'team_lead_assigned' => $lead->team_lead_id ? true : false,
        'employee_name' => $lead->employee ? $lead->employee->name : '-',
        'team_lead_name' => $lead->teamLead ? $lead->teamLead->name : '-',
        'city' => $lead->city ?? '-',
        'state' => $lead->state ?? '-',
        'bank_name' => $lead->bank_name ?? '-',
        'turnover_amount' => $lead->turnover_amount ?? 0,
        'voice_recording' => $lead->voice_recording ?? '-',
        'reason' => $lead->reason ?? '-'

    ];
});

    // Check for data_only parameter
    if ($request->input('data_only')) {
        return response()->json([
            'status' => 'success',
            'formattedLeads' => $formattedLeads,
            'total' => $leads->count()
        ]);
    }

    return view('TeamLead.leads.index', [
        'leads' => $leads,
        'formattedLeads' => $formattedLeads
    ]);
}



 public function authorizeLead($id, Request $request)
{
    $lead = Lead::findOrFail($id);
    $lead->status = 'authorized';
    $lead->remarks = $request->input('remarks');
    $lead->save();

    // Always insert a new history record
    DB::table('lead_histories')->insert([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action' => 'status_changed',
        'status' => 'authorized',
        'created_at' => now(),
        'updated_at' => now()
    ]);

     // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" has been authorized by the team lead.',
        'is_read'   => false,
    ]);

     // 🔔 Send notification with push notification
    NotificationHelper::sendLeadNotification(
        $lead->employee_id,
        $lead->id,
        'Lead Authorized',
        'Your lead "' . $lead->name . '" has been authorized by the team lead.'
    );


    return response()->json(['status' => 'success', 'message' => 'Lead authorized successfully.']);
}

 public function markPersonalLead($id, Request $request)
{
    $lead = Lead::findOrFail($id);
    $lead->status = 'personal_lead';
    $lead->remarks = $request->input('remarks');
    $lead->save();

    // Always insert a new history record
    DB::table('lead_histories')->insert([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action' => 'status_changed',
        'status' => 'personal_lead',
        'created_at' => now(),
        'updated_at' => now()
    ]);

     // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" status has been changed to personal Lead by the team lead.',
        'is_read'   => false,
    ]);

     // 🔔 Send notification with push notification
    NotificationHelper::sendLeadNotification(
        $lead->employee_id,
        $lead->id,
        'Lead Status chnaged to Personal Lead',
        'Your lead "' . $lead->name . '" status has been changed to Personal lead by the team lead.'
    );


    return response()->json(['status' => 'success', 'message' => 'Lead Status Changed successfully.']);
}


public function markFutureLead($id, Request $request)
{
    $lead = Lead::findOrFail($id);
    $lead->status = 'future_lead';
    $lead->save();

    // Always insert a new history record
    DB::table('lead_histories')->insert([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action' => 'status_changed',
        'status' => 'future_lead',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" has been set future lead by the team lead.',
        'is_read'   => false,
    ]);

     // 🔔 Send notification with push notification
    NotificationHelper::sendLeadNotification(
        $lead->employee_id,
        $lead->id,
        'Lead Marked as Future Lead',
        'Your lead "' . $lead->name . '" has been set as future lead by the team lead.'
    );

    return response()->json(['status' => 'success', 'message' => 'Lead marked as future lead.']);
}


    // public function approveLead(Request $request, Lead $lead)
    // {
    //     $user = Auth::user();

    //     // Authorization: Only the assigned team lead can approve
    //     if ($lead->team_lead_id !== $user->id) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Unauthorized to approve this lead.'
    //         ], 403);
    //     }

    //     // Validate request
    //     $request->validate([
    //         'remarks' => 'nullable|string|max:1000',
    //         'forward_to_operations' => 'nullable|boolean'
    //     ]);

    //     $status = $request->forward_to_operations ? 'login' : 'approved';

    //     $lead->update([
    //         'status' => $status,
    //         'remarks' => $request->remarks ?? $lead->remarks
    //     ]);

    //     // Log history
    //     $lead->histories()->create([
    //         'user_id' => $user->id,
    //         'action' => $status === 'login' ? 'approved_and_forwarded' : 'approved',
    //         'remarks' => $request->remarks ?? ($status === 'login' ? 'Lead approved and forwarded to operations.' : 'Lead approved by team lead.')
    //     ]);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => $status === 'login' ? 'Lead approved and forwarded to operations successfully.' : 'Lead approved successfully.'
    //     ]);
    // }



 public function rejectLead($id, Request $request)
{
    $lead = Lead::findOrFail($id);
    $lead->status = 'rejected';
    $lead->reason = $request->input('remarks');
    $lead->save();

    // Always insert a new lead history
    DB::table('lead_histories')->insert([
        'lead_id' => $lead->id,
        'user_id' => auth()->id(),
        'action' => 'status_changed',
        'status' => 'rejected',
        'comments' => $request->input('remarks') ?? 'Lead rejected by team lead.',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 🔔 Create notification for the employee who owns the lead
    Notification::create([
        'user_id'   => $lead->employee_id, // <-- employee who owns this lead
        'lead_id'   => $lead->id,
        'message'   => 'Your lead "' . $lead->name . '" has been rejected by the team lead.',
        'is_read'   => false,
    ]);

       // 2️⃣ Send Push Notification using NotificationHelper
    NotificationHelper::sendLeadNotification(
        $lead->employee_id,           // user receiving the notification
        $lead->id,                    // lead ID
        'Lead Rejected',              // title
        'Your lead "' . $lead->name . '" has been rejected by the team lead.',  // message
        null                          // attachment (optional)
    );

    return response()->json(['status' => 'success', 'message' => 'Lead rejected successfully.']);
}






    public function forwardedToMe(Request $request)
{
    $userId = Auth::id();

    $forwardedLeadIds = LeadForwardedHistory::where('receiver_user_id', $userId)
        ->where('is_forwarded', 1)
        ->pluck('lead_id');

    $leads = Lead::whereIn('id', $forwardedLeadIds)
        ->with(['employee', 'teamLead'])
        ->whereNull('deleted_at')
        ->latest()
        ->get();

    $formattedLeads = $leads->map(function ($lead) {

        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email ?? '-',
            'phone' => $lead->phone,
            'dob'=>$lead->dob??'-',
            'district' => $lead->district ?? '-',
            'salary' => $lead->salary ?? '-',
            'bank_name' => $lead->bank_name ?? '-',
            'location' => $lead->city ? "{$lead->city}, {$lead->state}" : ($lead->state ?? '-'),
            'company' => $lead->company_name ?? '-',
            'position' => $lead->position ?? '-',
            'industry' => $lead->lead_type ?? '-',
            'website' => $lead->website ?? '-',
            'amount' => number_format($lead->lead_amount, 0, '.', ','),
            'status' => $lead->status,
            'source' => $lead->lead_source ?? '-',
            'expected_date' => $lead->expected_month ? Carbon::parse($lead->expected_month)->format('M d, Y') : '-',
            'notes' => $lead->remarks ?? '-',
            'created_at' => $lead->created_at->format('d M Y'),
            'assigned' => $lead->team_lead_id !== null,
            'team_lead_assigned' => $lead->team_lead_id ? true : false,
            'employee_name' => $lead->employee ? $lead->employee->name : '-',
            'team_lead_name' => $lead->teamLead ? $lead->teamLead->name : '-'
        ];
    });


    return view('TeamLead.leads.forwarded_to_me', [
        'leads' => $leads,
        'formattedLeads' => $formattedLeads
    ]);
}
public function forwardToAdmin(Request $request, $id)
{
    $lead = Lead::findOrFail($id);
    $admin = User::where('designation', 'admin')->first();

    if (!$admin) {
        return response()->json(['status' => 'error', 'message' => 'No admin found.']);
    }

    // ✅ Step 1: Set all previous forwards of this lead to false
    LeadForwardedHistory::where('lead_id', $lead->id)
        ->where('is_forwarded', true)
        ->update(['is_forwarded' => false]);

    // ✅ Step 2: Save remarks to lead
    $lead->remarks = $request->remarks;
    $lead->save();

    // ✅ Step 3: Create new forwarded row
    LeadForwardedHistory::create([
        'lead_id' => $lead->id,
        'sender_user_id' => Auth::id(),
        'receiver_user_id' => $admin->id,
        'is_forwarded' => true,
        'forwarded_at' => now()
    ]);
     //step 4
     // Check if a history already exists for this user and lead
    $existingHistory = DB::table('lead_histories')
        ->where('lead_id', $lead->id)
        ->where('user_id', auth()->id())
        ->first();

    if ($existingHistory) {
        // ✅ Update existing row
        DB::table('lead_histories')
            ->where('id', $existingHistory->id)
            ->update([
                'action'=>'forwarded',
                'forwarded_to'=>$admin->id,
                'comments'=>'Forwarded to Admin',
                'updated_at' => now()
            ]);
    } else {
        // ✅ Optionally insert if not exists
        DB::table('lead_histories')->insert([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'forwarded',
            'status' => $lead->status,
            'forwarded_to'=>$admin->id,
            'comments'=>'Forwarded to Admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return response()->json(['status' => 'success']);
}






public function getOperationsUsers()
{
    $users = User::where('designation', 'operations')
        ->select('id', 'name')
        ->get();

    return response()->json($users);
}


public function forwardToOperations(Request $request, $leadId)
{
    $request->validate([
        'remarks' => 'nullable|string'
    ]);

    $lead = Lead::findOrFail($leadId);
    $teamLead = Auth::user();

    // Find the operation user who created this team lead
    $operationUser = User::where('id', $teamLead->created_by)
        ->where('designation', 'operations') // Ensure the user is an operation user
        ->first();

    if (!$operationUser) {
        return response()->json([
            'status' => 'error',
            'message' => 'No operation user found who created this team lead.'
        ], 404);
    }

    $lead->remarks = $request->remarks;
    $lead->save();

    // Set all previous forwards of this lead to false
    LeadForwardedHistory::where('lead_id', $lead->id)
        ->where('is_forwarded', true)
        ->update(['is_forwarded' => false]);

    // Record forwarding history
    LeadForwardedHistory::create([
        'lead_id' => $leadId,
        'sender_user_id' => $teamLead->id,
        'receiver_user_id' => $operationUser->id, // Use the operation user who created the team lead
        'is_forwarded' => true,
        'forwarded_at' => now()
    ]);

    // Insert into lead_histories with current status of the lead
    DB::table('lead_histories')->insert([
        'lead_id' => $lead->id,
        'user_id' => $teamLead->id,
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
        'message'   => 'Your lead "' . $lead->name . '" has been forwarded to the Operation.',
        'is_read'   => false,
    ]);
        // 2️⃣ Send Push Notification using Helper
    NotificationHelper::sendLeadNotification(
        $lead->employee_id,
        $lead->id,
        'Lead Forwarded',
        'Your lead "' . $lead->name . '" has been forwarded to Operations.',
        null
    );
    return response()->json([
        'status' => 'success',
        'message' => 'Lead forwarded to operation team'
    ]);
}

public function forwardLeadToOperationTeamLead(Request $request, $leadId)
{
    $request->validate([
        'remarks' => 'nullable|string'
    ]);

    $lead = Lead::findOrFail($leadId);
    $teamLead = Auth::user();

    // Find the operation user who created this team lead
    $operationUser = User::where('id', $teamLead->created_by)
        ->where('designation', 'operations') // Ensure the user is an operation user
        ->first();

    if (!$operationUser) {
        return response()->json([
            'status' => 'error',
            'message' => 'No operation user found who created this team lead.'
        ], 404);
    }

    // ✅ Check if already forwarded
    $alreadyForwarded = LeadForwardedHistory::where('lead_id', $leadId)
        ->where('receiver_user_id', $operationUser->id)
        ->where('is_forwarded', true)
        ->exists();

    if ($alreadyForwarded) {
        return response()->json([
            'status' => 'error',
            'message' => 'This lead has already been forwarded to operations.'
        ], 400);
    }

    $lead->remarks = $request->remarks;
    $lead->save();


   // Set all previous forwards of this lead to false
LeadForwardedHistory::where('lead_id', $lead->id)
    ->where('is_forwarded', true)
    ->update(['is_forwarded' => false]);


    // Record forwarding history
    LeadForwardedHistory::create([
        'lead_id' => $leadId,
        'sender_user_id' => $teamLead->id,
        'receiver_user_id' => $operationUser->id, // Use the operation user who created the team lead
        'is_forwarded' => true,
        'forwarded_at' => now()
    ]);

    // Insert into lead_histories with current status of the lead
    DB::table('lead_histories')->insert([
        'lead_id' => $lead->id,
        'user_id' => $teamLead->id,
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
        'message'   => 'Your lead "' . $lead->name . '" has been forwarded to the Operation.',
        'is_read'   => false,
    ]);

        // 2️⃣ Send Push Notification
    NotificationHelper::sendLeadNotification(
        $lead->employee_id,
        $lead->id,
        'Lead Forwarded',
        'Your lead "' . $lead->name . '" has been forwarded to Operations.',
        null
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Lead forwarded to operation team'
    ]);
}



public function export()
{
    $leads = Lead::with('teamLead')->whereNull('deleted_at')->get();

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=leads_report.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['Name', 'Company', 'Location', 'Amount', 'Success %', 'Expected Month', 'Status', 'Team Lead'];

    $callback = function () use ($leads, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($leads as $lead) {
            fputcsv($file, [
                $lead->client_name,
                $lead->company,
                $lead->city ?? $lead->district ?? $lead->state ?? '',
                $lead->lead_amount,
                $lead->success_percentage,
                Carbon::parse($lead->expected_month)->format('F Y'),
                ucfirst($lead->status),
                $lead->teamLead->name ?? '',
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
   public function update(Request $request, $id)
{
     try {
        $request->validate([
             'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
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
            'company_name' => $request->company,
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

        Log::info('Lead updated', ['lead_id' => $id, 'user_id' => auth()->id()]);

        return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
    } catch (ValidationException $e) {
        Log::error("Validation error in update: " . $e->getMessage(), ['lead_id' => $id]);
        return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], 422);
    } catch (Throwable $e) {
        Log::error("Error in update: " . $e->getMessage(), ['lead_id' => $id]);
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
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

        // Handle file upload
        $filePath = $request->file('document_file')->store('lead_documents', 'public');

        // Create a new document record
        $document = Document::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        // Create lead_document record
        $leadDocument = LeadDocument::create([
            'lead_id' => $lead->id,
            'document_id' => $document->id,
            'upload_by' => Auth::id(),
            'filepath' => $filePath,
            'uploaded_at' => now(),
        ]);

        // $lead->touch();

        // Return the document data in the format expected by the frontend
        return response()->json([
            'status' => 'success',
            'document' => [
                'id' => $document->id,
                'name' => $document->name,
                'type' => $document->type,
                'description' => $document->description,
                'filepath' => $filePath,
                'uploaded_at' => $leadDocument->uploaded_at->format('Y-m-d H:i:s'),
            ]
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

public function deleteFile($leadId, $documentId)
{
    try {
        // Fetch the document record
      $record = LeadDocument::where('lead_id', $leadId)
    ->where('document_id', $documentId)
    ->first();

if (!$record) {
    return response()->json([
        'status' => 'error',
        'message' => 'Document not found for this lead.'
    ], 404);
}

        // Delete file from storage if it exists
        if ($record->filepath && Storage::exists($record->filepath)) {
            Storage::delete($record->filepath);
        }

        // Update the row to nullify the file path
        $record->update([
            'filepath' => null,
            'updated_at' => now()
        ]);

        // Return JSON success response
        return response()->json([
            'status' => 'success',
            'message' => 'Document removed. You can now upload it again.'
        ], 200);
    } catch (\Exception $e) {
        // Return JSON error response if an exception occurs
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete document: ' . $e->getMessage()
        ], 500);
    }
}

public function upload(Request $request, $leadId, $documentId)
{
    $request->validate([
        'document_file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:9999',
    ]);

    try {
        $file = $request->file('document_file');
        $path = $file->store('lead_documents', 'public');

        $lead = Lead::findOrFail($leadId);

        DB::table('lead_document')
            ->where('lead_id', $leadId)
            ->where('document_id', $documentId)
            ->update([
                'filepath'    => $path,
                'upload_by'   => auth()->id(),
                'uploaded_at' => now(),
                'updated_at'  => now(),
            ]);

        // $lead->touch();

        return response()->json([
            'status'  => 'success',
            'message' => 'Document uploaded successfully.',
            'filepath' => $path
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to upload document: ' . $e->getMessage()
        ], 500);
    }
}


public function getDocuments($leadId)
{
    try {
        $documents = DB::table('lead_document')
            ->join('documents', 'lead_document.document_id', '=', 'documents.id')
            ->where('lead_document.lead_id', $leadId)
            ->select(
                'documents.id as document_id',
                'documents.name as document_name',
                'documents.description',
                'documents.type',
                'lead_document.filepath',
                'lead_document.uploaded_at'
            )
            ->get();
        return response()->json([
            'status' => 'success',
            'documents' => $documents
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load documents: ' . $e->getMessage()
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
        Log::info("🔍 getDeletedLeads() called");

        $user = Auth::user();
        Log::info("👤 Authenticated User:", [
            'id' => $user->id ?? null,
            'name' => $user->name ?? null,
            'designation' => $user->designation ?? null
        ]);

        // Check permission
        if ($user->designation !== 'team_lead') {
            Log::warning("❌ Unauthorized access attempt to deleted leads", [
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to view deleted leads',
            ], 403);
        }

        // Fetch soft deleted leads
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

        Log::info("🗑 Total deleted leads fetched: " . $deletedLeads->count());

        // Log full data (optional)
        Log::info("📦 Deleted Leads Data:", $deletedLeads->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted leads retrieved successfully',
            'data' => $deletedLeads,
        ]);

    } catch (Exception $e) {

        Log::error("🔥 Error in getDeletedLeads(): " . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

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

    if ($user->designation !== 'team_lead') {
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
    return view('TeamLead.leads.deleted_leads');
}

}
