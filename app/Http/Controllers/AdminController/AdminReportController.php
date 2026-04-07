<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
   public function report(Request $request)
    {
        // --- Lead Valuation Report Logic (No changes needed here) ---
        $tableDateFilter = $request->input('table_date_filter', '30_days');
        $tableStartDate = $request->input('table_start_date');
        $tableEndDate = $request->input('table_end_date');

        $leadsQuery = Lead::query()
            ->with('employee')
            ->where('lead_type', '!=', 'creditcard_loan');

        if ($tableDateFilter === 'custom' && $tableStartDate && $tableEndDate) {
            $leadsQuery->where(function ($query) use ($tableStartDate, $tableEndDate) {
                $query->whereBetween('created_at', [$tableStartDate, $tableEndDate])
                      ->orWhereBetween('updated_at', [$tableStartDate, $tableEndDate]);
            });
        } elseif ($tableDateFilter === '30_days') {
            $leadsQuery->where(function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay())
                      ->orWhere('updated_at', '>=', Carbon::now()->subDays(30)->startOfDay());
            });
        } elseif ($tableDateFilter === '15_days') {
            $leadsQuery->where(function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(15)->startOfDay())
                      ->orWhere('updated_at', '>=', Carbon::now()->subDays(15)->startOfDay());
            });
        } elseif ($tableDateFilter === '7_days') {
            $leadsQuery->where(function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay())
                      ->orWhere('updated_at', '>=', Carbon::now()->subDays(7)->startOfDay());
            });
        }

        $leadsByStatus = $leadsQuery->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(lead_amount) as total_valuation'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => ['count' => $item->count, 'total_valuation' => $item->total_valuation ?? 0]];
            })->toArray();

        // --- Lead Information Report Logic ---
        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $state = $request->input('state', '');
        $district = $request->input('district', '');
        $city = $request->input('city', '');
        $leadType = $request->input('lead_type', '');
        $minAmount = $request->input('min_amount', '');
        $maxAmount = $request->input('max_amount', '');
        $leadDateFilter = $request->input('lead_date_filter', '');
        $leadStartDate = $request->input('lead_start_date', '');
        $leadEndDate = $request->input('lead_end_date', '');
        $operationId = $request->input('operation_id', '');
        $teamLeadId = $request->input('team_lead_id', '');
        $employeeId = $request->input('employee_id', '');

        $leadsQuery = Lead::query()
            ->select('id', 'name', 'phone', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id', 'created_at')
            ->with('employee')
            ->where('lead_type', '!=', 'creditcard_loan'); 

        // SEARCH FILTER
        if ($search) {
            $leadsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // STANDARD FILTERS
        if ($status) $leadsQuery->where('status', $status);
        if ($state) $leadsQuery->where('state', $state);
        if ($district) $leadsQuery->where('district', $district);
        if ($city) $leadsQuery->where('city', $city);
        if ($leadType) $leadsQuery->where('lead_type', $leadType);
        if ($minAmount) $leadsQuery->where('lead_amount', '>=', $minAmount);
        if ($maxAmount) $leadsQuery->where('lead_amount', '<=', $maxAmount);

        // DATE FILTERS (Corrected startOfDay)
        if ($leadDateFilter === 'custom' && $leadStartDate && $leadEndDate) {
            $leadsQuery->whereBetween('created_at', [$leadStartDate, $leadEndDate]);
        } elseif ($leadDateFilter === '60_days') {
            $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(60)->startOfDay());
        } elseif ($leadDateFilter === '30_days') {
            $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay());
        } elseif ($leadDateFilter === '15_days') {
            $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(15)->startOfDay());
        } elseif ($leadDateFilter === '7_days') {
            $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        }

        // HIERARCHY FILTERS
        if ($operationId) {
            $leadsQuery->whereHas('employee', function ($q) use ($operationId) {
                $q->whereIn('team_lead_id', User::where('created_by', $operationId)->where('designation', 'team_lead')->pluck('id'));
            });
        }
        if ($teamLeadId) {
            $employeeIds = User::where('team_lead_id', $teamLeadId)->where('designation', 'employee')->pluck('id')->toArray();
            $leadsQuery->whereIn('employee_id', $employeeIds);
        }
        if ($employeeId) $leadsQuery->where('employee_id', $employeeId);

        $leads = $leadsQuery->get(); // No pagination

        // --- Dropdown Data ---
        $statuses = Lead::select('status')->where('lead_type', '!=', 'creditcard_loan')->distinct()->pluck('status')->toArray();
        $states = State::where('status', 'Active')->pluck('state_title')->toArray();
        $districts = District::where('district_status', 'Active')->pluck('district_title')->toArray();
        $cities = City::where('status', 'Active')->pluck('name')->toArray();
        $leadTypes = Lead::select('lead_type')->where('lead_type', '!=', 'creditcard_loan')->distinct()->pluck('lead_type')->filter()->toArray();
        $operations = User::where('designation', 'operations')->get(['id', 'name']);
        $teamLeads = User::where('designation', 'team_lead')->when($operationId, fn($q) => $q->where('created_by', $operationId))->get(['id', 'name']);
        $employees = User::where('designation', 'employee')->when($teamLeadId, fn($q) => $q->where('team_lead_id', $teamLeadId))->get(['id', 'name']);

        // --- Task Report Logic (No changes needed) ---
        $taskDateFilter = $request->input('task_date_filter', '30_days');
        $taskStartDate = $request->input('task_start_date');
        $taskEndDate = $request->input('task_end_date');
        $taskTargetType = $request->input('task_target_type', '');
        $taskOperationId = $request->input('task_operation_id', '');
        $taskTeamLeadId = $request->input('task_team_lead_id', '');
        $taskEmployeeId = $request->input('task_employee_id', '');

        $tasksQuery = Task::where('admin_id', auth()->id())
            ->with(['teamLead', 'assignedUsers']);

        if ($taskDateFilter === 'custom' && $taskStartDate && $taskEndDate) {
            $tasksQuery->whereBetween('assigned_date', [$taskStartDate, $taskEndDate]);
        } elseif ($taskDateFilter === '30_days') {
            $tasksQuery->where('assigned_date', '>=', Carbon::now()->subDays(30));
        } elseif ($taskDateFilter === '15_days') {
            $tasksQuery->where('assigned_date', '>=', Carbon::now()->subDays(15));
        } elseif ($taskDateFilter === '7_days') {
            $tasksQuery->where('assigned_date', '>=', Carbon::now()->subDays(7));
        }
        if ($taskTargetType) $tasksQuery->where('target_type', $taskTargetType);
        if ($taskOperationId) $tasksQuery->where('operations_id', $taskOperationId);
        if ($taskTeamLeadId) $tasksQuery->where('team_lead_id', $taskTeamLeadId);
        if ($taskEmployeeId) $tasksQuery->whereHas('assignedUsers', fn($q) => $q->where('users.id', $taskEmployeeId));

        $tasks = $tasksQuery->get();
        $taskTargetTypes = ['all', 'individual', 'individual_teamlead', 'individual_operation'];

        return view('admin.report', compact(
            'leadsByStatus', 'leads', 'tasks', 'tableDateFilter', 'tableStartDate', 'tableEndDate',
            'search', 'status', 'state', 'district', 'city', 'leadType', 'minAmount', 'maxAmount',
            'leadDateFilter', 'leadStartDate', 'leadEndDate', 'operationId', 'teamLeadId', 'employeeId',
            'taskDateFilter', 'taskStartDate', 'taskEndDate', 'taskTargetType', 'taskOperationId',
            'taskTeamLeadId', 'taskEmployeeId', 'taskTargetTypes', 'statuses', 'states', 'districts',
            'cities', 'leadTypes', 'operations', 'teamLeads', 'employees'
        ));
    }

  public function exportLead($status)
{
    $tableDateFilter = request()->input('table_date_filter', '30_days');
    $tableStartDate = request()->input('table_start_date');
    $tableEndDate = request()->input('table_end_date');

    $leadsQuery = Lead::query()
        ->with('employee')
        ->where('lead_type', '!=', 'creditcard_loan'); // Exclude creditcard_loan

    // UPDATED: Added ->startOfDay() to ensure full day coverage
    if ($tableDateFilter === 'custom' && $tableStartDate && $tableEndDate) {
        $leadsQuery->where(function ($query) use ($tableStartDate, $tableEndDate) {
            $query->whereBetween('created_at', [$tableStartDate, $tableEndDate])
                  ->orWhereBetween('updated_at', [$tableStartDate, $tableEndDate]);
        });
    } elseif ($tableDateFilter === '30_days') {
        $leadsQuery->where(function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay())
                  ->orWhere('updated_at', '>=', Carbon::now()->subDays(30)->startOfDay());
        });
    } elseif ($tableDateFilter === '15_days') {
        $leadsQuery->where(function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(15)->startOfDay())
                  ->orWhere('updated_at', '>=', Carbon::now()->subDays(15)->startOfDay());
        });
    } elseif ($tableDateFilter === '7_days') {
        $leadsQuery->where(function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay())
                  ->orWhere('updated_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        });
    }

    $leadsQuery->where('status', $status);

    $leads = $leadsQuery->get();

    $headers = ['Content-Type' => 'text/csv'];
    $filename = "lead_valuation_{$status}_export_" . Carbon::now()->format('YmdHis') . '.csv';
    
    $callback = function () use ($leads) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID', 'Name', 'Email', 'DOB', 'City', 'District', 'State', 'Company', 'Lead Amount', 'Salary', 'Status', 'Lead Type', 'Turnover', 'Bank', 'Employee', 'Created At']);
        
        foreach ($leads as $lead) {
            fputcsv($file, [
                $lead->id,
                $lead->name,
                $lead->email ?? 'N/A',
                $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A',
                $lead->city ?? 'N/A',
                $lead->district ?? 'N/A',
                $lead->state ?? 'N/A',
                $lead->company_name ?? 'N/A',
                '₹' . number_format($lead->lead_amount ?? 0, 2),
                '₹' . number_format($lead->salary ?? 0, 2),
                ucfirst(str_replace('_', ' ', $lead->status)),
                $lead->lead_type ?? 'N/A',
                '₹' . number_format($lead->turnover_amount ?? 0, 2),
                $lead->bank_name ?? 'N/A',
                $lead->employee ? $lead->employee->name : 'N/A',
                $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : 'N/A',
            ]);
        }
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers, $filename);
}

  public function exportLeadInfo(Request $request)
    {
        $id = $request->input('id'); 

        $leadsQuery = Lead::query()
            ->select('id', 'name', 'phone', 'email', 'dob', 'city', 'district', 'state', 'company_name', 'lead_amount', 'salary', 'status', 'lead_type', 'turnover_amount', 'bank_name', 'employee_id', 'created_at')
            ->with('employee')
            ->where('lead_type', '!=', 'creditcard_loan'); 

        // If ID is present, just export that one row
        if ($id) {
            $leadsQuery->where('id', $id);
        } else {
            // Apply ALL the filters from report() here so the export matches the table view
            
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $state = $request->input('state', '');
            $district = $request->input('district', '');
            $city = $request->input('city', '');
            $leadType = $request->input('lead_type', '');
            $minAmount = $request->input('min_amount', '');
            $maxAmount = $request->input('max_amount', '');
            $leadDateFilter = $request->input('lead_date_filter', '');
            $leadStartDate = $request->input('lead_start_date', '');
            $leadEndDate = $request->input('lead_end_date', '');
            $operationId = $request->input('operation_id', '');
            $teamLeadId = $request->input('team_lead_id', '');
            $employeeId = $request->input('employee_id', '');

            if ($search) {
                $leadsQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            if ($status) $leadsQuery->where('status', $status);
            if ($state) $leadsQuery->where('state', $state);
            if ($district) $leadsQuery->where('district', $district);
            if ($city) $leadsQuery->where('city', $city);
            if ($leadType) $leadsQuery->where('lead_type', $leadType);
            if ($minAmount) $leadsQuery->where('lead_amount', '>=', $minAmount);
            if ($maxAmount) $leadsQuery->where('lead_amount', '<=', $maxAmount);

            if ($leadDateFilter === 'custom' && $leadStartDate && $leadEndDate) {
                $leadsQuery->whereBetween('created_at', [$leadStartDate, $leadEndDate]);
            } elseif ($leadDateFilter === '60_days') {
                $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(60)->startOfDay());
            } elseif ($leadDateFilter === '30_days') {
                $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay());
            } elseif ($leadDateFilter === '15_days') {
                $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(15)->startOfDay());
            } elseif ($leadDateFilter === '7_days') {
                $leadsQuery->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
            }

            if ($operationId) {
                $leadsQuery->whereHas('employee', function ($q) use ($operationId) {
                    $q->whereIn('team_lead_id', User::where('created_by', $operationId)->where('designation', 'team_lead')->pluck('id'));
                });
            }
            if ($teamLeadId) {
                $employeeIds = User::where('team_lead_id', $teamLeadId)->where('designation', 'employee')->pluck('id')->toArray();
                $leadsQuery->whereIn('employee_id', $employeeIds);
            }
            if ($employeeId) $leadsQuery->where('employee_id', $employeeId);
        }

        $leads = $leadsQuery->get();

        $headers = ['Content-Type' => 'text/csv'];
        $filename = $id ? "lead_${id}_export_" . Carbon::now()->format('YmdHis') . '.csv' : 'lead_info_export_' . Carbon::now()->format('YmdHis') . '.csv';
        
        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Phone', 'Email', 'DOB', 'City', 'District', 'State', 'Company', 'Lead Amount', 'Salary', 'Status', 'Lead Type', 'Turnover', 'Bank', 'Employee', 'Created At']);
            
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->phone,
                    $lead->email ?? 'N/A',
                    $lead->dob ? $lead->dob->format('Y-m-d') : 'N/A',
                    $lead->city ?? 'N/A',
                    $lead->district ?? 'N/A',
                    $lead->state ?? 'N/A',
                    $lead->company_name ?? 'N/A',
                    '₹' . number_format($lead->lead_amount ?? 0, 2),
                    '₹' . number_format($lead->salary ?? 0, 2),
                    ucfirst(str_replace('_', ' ', $lead->status)),
                    $lead->lead_type ?? 'N/A',
                    '₹' . number_format($lead->turnover_amount ?? 0, 2),
                    $lead->bank_name ?? 'N/A',
                    $lead->employee ? $lead->employee->name : 'N/A',
                    $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers, $filename);
    }

    public function exportTask($taskId)
    {
        $task = Task::with(['teamLead', 'assignedUsers'])->findOrFail($taskId);
        $headers = ['Content-Type' => 'text/csv'];
        $filename = "task_${taskId}_export_" . Carbon::now()->format('YmdHis') . '.csv';
        $callback = function () use ($task) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Field', 'Value']);
            $data = [
                'ID' => $task->id,
                'Title' => $task->title,
                'Target Type' => ucfirst(str_replace('_', ' ', $task->target_type)),
                'Priority' => ucfirst($task->priority),
                'Status' => ucfirst($task->status),
                'Progress' => $task->progress . '%',
                'Assigned Date' => $task->assigned_date ? $task->assigned_date->format('Y-m-d') : 'N/A',
                'Due Date' => $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A',
                'Team Lead' => $task->teamLead ? $task->teamLead->name : 'N/A',
                'Assigned Users' => implode(', ', $task->assignedUsers->map(function ($user) {
                    return $user->name . ' (Status: ' . ($user->pivot->status ?? 'N/A') . ', Progress: ' . ($user->pivot->progress ?? 0) . '%, Completed At: ' . ($user->pivot->completed_at ? $user->pivot->completed_at->format('Y-m-d H:i:s') : 'N/A') . ')';
                })->toArray()),
            ];
            foreach ($data as $key => $value) {
                fputcsv($file, [$key, $value]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers, $filename);
    }
}
