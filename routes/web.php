<?php

use App\Http\Controllers\AdminController\BankController;
use App\Http\Controllers\Admin\OfficeRuleController;
use App\Http\Controllers\AdminController\TargetController;
use App\Http\Controllers\AdminController\GeofenceController;
use App\Http\Controllers\AdminController\EmployeeMovementController;
use App\Http\Controllers\AdminController\AdminAttendanceController;
use App\Http\Controllers\AdminController\AdminController;
use App\Http\Controllers\AdminController\AdminEmployeeController;
use App\Http\Controllers\AdminController\AdminHRController;
use App\Http\Controllers\AdminController\AdminLeadsController;
use App\Http\Controllers\AdminController\AdminLeaveApprovalController;
use App\Http\Controllers\AdminController\AdminOperationController;
use App\Http\Controllers\AdminController\AdminPasswordController;
use App\Http\Controllers\AdminController\AdminReportController;
use App\Http\Controllers\AdminController\AdminTaskController;
use App\Http\Controllers\AdminController\AdminTLController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\HrController\HrAttendanceController;
use App\Http\Controllers\HrController\HrCompoffApprovalController;
use App\Http\Controllers\HrController\HrEmployeeController;
use App\Http\Controllers\HrController\HrHolidayController;
use App\Http\Controllers\HrController\HrLeaveApprovalController;
use App\Http\Controllers\HrController\HrLeaveController;
use App\Http\Controllers\HrController\HrNotificationController;
use App\Http\Controllers\HrController\HrOperationController;
use App\Http\Controllers\HrController\HrPasswordController;
use App\Http\Controllers\HrController\HrTLController;
use App\Http\Controllers\OpearationController\LeadsController;
use App\Http\Controllers\OpearationController\OperationCompOffController;
use App\Http\Controllers\OpearationController\OperationDashboardController;
use App\Http\Controllers\OpearationController\OperationEmployeeController;
use App\Http\Controllers\OpearationController\OperationLeaveController;
use App\Http\Controllers\OpearationController\OperationPasswordController;
use App\Http\Controllers\OpearationController\OperationsOfferController;
use App\Http\Controllers\OpearationController\OperationsTaskController;
use App\Http\Controllers\OpearationController\TasksController;
use App\Http\Controllers\OpearationController\TeamleadController;
use App\Http\Controllers\OpearationController\OperationsReportController;
use App\Http\Controllers\TLController\TeamLeadReportController;
use App\Http\Controllers\TLController\TLAttendanceController;
use App\Http\Controllers\TLController\TLCompOffController;
use App\Http\Controllers\TLController\TLDashboardController;
use App\Http\Controllers\AdminLiveDashboardController;
use App\Http\Controllers\EmployeeController\EmployeeDashboardController as EmployeeController;
use App\Http\Controllers\EmployeeController\WebLeadController;
use App\Http\Controllers\HrController\SalarySlipController;
use App\Http\Controllers\OpearationController\OperationAttendanceController;
use App\Http\Controllers\OpearationController\TaskController as OpearationControllerTaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TLController\LeadController;
use App\Http\Controllers\TLController\TaskController;
use App\Http\Controllers\TLController\TLEmployeeContoller;
use App\Http\Controllers\TLController\TLEmployeeController;
use App\Http\Controllers\TLController\TLLeaveController;
use App\Http\Controllers\TLController\TLPasswordController;
use App\Models\City;
use App\Models\District;
use App\Models\State;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login page
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Dashboard redirection based on designation
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();
    switch ($user->designation) {
        case 'employee':
            return redirect()->route('employee.dashboard');
        case 'team_lead':
            return redirect()->route('team_lead.dashboard');
        case 'operations':
            return redirect()->route('operations.dashboard');
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'hr':
            return redirect()->route('hr.leave.index');
        default:
            abort(403, 'Unauthorized action.');
    }
})->name('dashboard');

// Authenticated routes (provided by Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Employee routes (accessible only by users with designation 'employee')
Route::middleware(['auth', 'designation:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/leads', [WebLeadController::class, 'indexLeads'])->name('leads.index');
    Route::get('/leads/create', [EmployeeController::class, 'createLead'])->name('leads.create');
    Route::post('/leads', [EmployeeController::class, 'storeLead'])->name('leads.store');
    Route::get('/leads/{lead}/edit', [EmployeeController::class, 'editLead'])->name('leads.edit');
    Route::patch('/leads/{lead}', [EmployeeController::class, 'updateLead'])->name('leads.update');
    Route::get('/tasks', [EmployeeController::class, 'indexTasks'])->name('tasks.index');
    Route::get('/team', [EmployeeController::class, 'indexTeam'])->name('team.index');
    Route::get('/attendance', [EmployeeController::class, 'indexAttendance'])->name('attendance.index');
    Route::post('/attendance/check-in', [EmployeeController::class, 'checkIn'])->name('attendance.check_in');
    Route::post('/attendance/check-out', [EmployeeController::class, 'checkOut'])->name('attendance.check_out');
    Route::get('/notifications', [EmployeeController::class, 'indexNotifications'])->name('notifications.index');
    Route::get('/setting', [EmployeeController::class, 'indexSetting'])->name('settings.index');
    Route::patch('/setting', [EmployeeController::class, 'updateSetting'])->name('settings.update');
});

// Team Lead routes (accessible only by users with designation 'team_lead')
Route::middleware(['auth', 'designation:team_lead'])->prefix('team-lead')->name('team_lead.')->group(function () {
    Route::get('/dashboard', [TLDashboardController::class, 'dashboardStats'])->name('dashboard');
    Route::get('/leads/{status}', [TLDashboardController::class, 'leadsByStatus'])->name('dashboard.leads.byStatus');
    Route::get('/team-lead/lead-report', [TLDashboardController::class, 'filterLeadReport'])->name('report');
    Route::get('/teams', [TLDashboardController::class, 'indexTeams'])->name('teams.index');
    Route::get('/leads', [LeadController::class, 'indexLeads'])->name('leads.index');
    Route::post('/leads/{id}/authorize', [LeadController::class, 'authorizeLead'])->name('leads.authorize');
    Route::post('/leads/{id}/markpersonallead', [LeadController::class, 'markPersonalLead'])->name('leads.markPersonalLead');
    Route::post('/leads/{id}/reject', [LeadController::class, 'rejectLead'])->name('leads.reject');
    Route::post('/leads/{id}/future', [LeadController::class, 'markFutureLead'])->name('leads.future');
    Route::get('/leads/forwarded-to-me', [LeadController::class, 'forwardedToMe'])->name('leads.forwarded_to_me');
    Route::post('/leads/{id}/forward-admin', [LeadController::class, 'forwardToAdmin']);
    //this route is for Assigned lead
    Route::post('/leads/{id}/forward-operations', [LeadController::class, 'forwardToOperations']);
    //this route is for dashboard
    Route::post('/leads/{id}/forward-to-operations-by-teamlead', [LeadController::class, 'forwardLeadToOperationTeamLead']);

        // Soft delete lead
Route::delete('/leads/{lead}', [LeadController::class, 'destroy']);

 // Page route - returns HTML view
    Route::get('/deletedleads/show', [LeadController::class, 'showDeletedLeadsPage'])->name('deletedleads.show');

    // API route - returns JSON data
    Route::get('/lead/deleted', [LeadController::class, 'getDeletedLeads'])->name('leads.deleted.api');

    // Restore route
    Route::post('/leads/{id}/restore', [LeadController::class, 'restore']);



    Route::get('/operations-users', [LeadController::class, 'getOperationsUsers']);
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::post('/lead/{id}/update', [LeadController::class, 'update'])->name('leads.update');
    Route::post('/lead/{lead}/documents', [LeadController::class, 'storeDocument'])->name('lead.documents.store');
     Route::post('/lead/{lead}/documents/{document}/upload', [LeadController::class, 'upload']);
    Route::delete('/lead/{lead}/documents/{document}/delete', [LeadController::class, 'deleteFile'])->name('lead.documents.destroy');
    Route::post('/lead/{lead}/document', [LeadController::class, 'getDocuments'])->name('lead.documents.index');


    Route::get('/tasks', [TLDashboardController::class, 'indexTasks'])->name('tasks.index');
    Route::get('/tasks/create', [TLDashboardController::class, 'createTask'])->name('tasks.create');
    Route::post('/tasks', [TLDashboardController::class, 'storeTask'])->name('tasks.store');
    Route::post('/store', [TaskController::class, 'store'])->name('team.tasks.store');
    Route::get('/assigned-tasks', [TaskController::class, 'getAllTasksForTeamLead'])->name('teamlead.tasks');

    Route::get('/setting', [TLDashboardController::class, 'indexSetting'])->name('setting.index');
    Route::post('/tasks/bulk-assign', [TLDashboardController::class, 'bulkAssignTasks'])->name('tasks.bulk_assign');

    // Notifications
 Route::get('/notifications', [TLDashboardController::class, 'indexNotifications'])->name('notifications.index');
    Route::get('/notifications/fetch', [TLDashboardController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/{id}/mark-read', [TLDashboardController::class, 'markRead'])->name('notifications.mark_read');
    Route::get('/tasks/{taskId}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{taskId}/update-status', [TaskController::class, 'updateTaskStatus'])->name('tasks.update_status');
    // Optional: Uncomment if notification badge is needed
    Route::get('/notifications/count', [TLDashboardController::class, 'countUnread'])->name('notifications.count');

    // Employee Management
    Route::post('/employees', [TLEmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employees/{id}', [TLEmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/{id}/deactivate', [TLEmployeeController::class, 'deactivate'])->name('employees.deactivate');
    Route::post('/employees/{id}/activate', [TLEmployeeController::class, 'activate'])->name('employees.activate');

    // Reports
    Route::get('/export-report/{type}', [TeamLeadReportController::class, 'export'])->name('teamlead.export');
    Route::get('/reports', [TeamLeadReportController::class, 'indexReports'])->name('reports.index');
    Route::get('/employee/details/{id}', [TeamLeadReportController::class, 'show']);

    // Attendance Management
    Route::get('/attendance', [TLAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{id}', [TLAttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/check-in', [TLAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [TLAttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/complaint', [TLAttendanceController::class, 'submitComplaint'])->name('attendance.complaint');

    //state.district,city dropdowns
    Route::get('/states', [LocationController::class, 'getStates']);
    Route::get('/districts/{state_id}', [LocationController::class, 'getDistricts']);
    Route::get('/cities/{district_id}', [LocationController::class, 'getCities']);

    Route::get('/password', [TLPasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [TLPasswordController::class, 'update'])->name('password.update');

    //salary slip download
    Route::post('/salary-slips/{id}/download', [TLDashboardController::class, 'download'])
        ->name('salary_slips.download');


        //leave management

    Route::get('/leave/apply', [TLLeaveController::class, 'create'])->name('leave.index');
    Route::post('/leave/apply', [TLLeaveController::class, 'store'])->name('leave.store');

    // Comp Off Management
    Route::get('/comp-off/apply', [TLCompOffController::class, 'create'])->name('comp-off.index');
    Route::post('/comp-off/apply', [TLCompOffController::class, 'store'])->name('comp-off.store');


});

// Operations routes (accessible only by users with designation 'operations')
Route::middleware(['auth', 'designation:operations'])->prefix('operations')->name('operations.')->group(function () {
   // Route::get('/dashboard', [OperationDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/leads', [OperationDashboardController::class, 'indexLeads'])->name('leads.index');
    Route::post('/leads/{lead}/complete', [OperationDashboardController::class, 'completeLead'])->name('leads.complete');
    Route::get('/notifications', [OperationDashboardController::class, 'indexNotifications'])->name('notifications.index');
  // For dashboard
    Route::get('/dashboard', [OperationDashboardController::class, 'dashboardStats'])->name('dashboard');
    Route::get('/leads/{status}', [OperationDashboardController::class, 'leadsByStatus'])->name('dashboard.leads.byStatus');
    Route::get('/operations/report', [OperationDashboardController::class, 'filterLeadReport'])->name('report');
    Route::get('/operations/leads/export', [OperationDashboardController::class, 'exportLeads'])->name('leads.export');

    Route::get('/employee', [OperationEmployeeController::class, 'indexTeams'])->name('employees.index');
     // Store new employee
    Route::post('/employees', [OperationEmployeeController::class, 'store'])->name('employees.store');

    // Update existing employee
    Route::post('/employees/{id}', [OperationEmployeeController::class, 'update'])->name('employees.update');

Route::post('/employees/{id}/deactivate', [OperationEmployeeController::class, 'deactivate'])->name('employees.deactivate');
Route::post('/employees/{id}/activate', [OperationEmployeeController::class, 'activate'])->name('employees.activate');


  //Teamlead management
    Route::get('/teamlead', [TeamleadController::class, 'indexTeamlead'])->name('teamlead.index');
    Route::post('/teamlead/store',[TeamleadController::class,'storeTeamlead'])->name('teamlead.store');
    Route::post('/teamlead/{id}/activate', [TeamleadController::class, 'activate'])->name('teamlead.activate'); // Activate
    Route::post('/teamlead/{id}/deactivate', [TeamleadController::class, 'deactivate'])->name('teamlead.deactivate'); // Deactivate
    Route::get('/teamlead/{id}/edit', [TeamleadController::class, 'edit'])->name('teamlead.edit'); // Optional: Edit
    Route::match(['PUT', 'POST'], '/teamlead/{id}/update', [TeamleadController::class, 'update'])->name('teamlead.update');


    //Lead Management
    Route::get('/leads', [LeadsController::class, 'indexLeads'])->name('leads.index');
    Route::get('/leads/{lead}/details', [LeadsController::class, 'getLeadDetails']);
    Route::post('/leads/{lead}/documents/{document}/upload', [LeadsController::class, 'upload']);
    Route::delete('/leads/{lead}/documents/{document}/delete', [LeadsController::class, 'deleteFile'])->name('leads.documents.destroy');
    Route::post('/leads/{id}/update-status', [LeadsController::class, 'updateStatus']);
Route::post('/leads/{id}/forward-to-admin', [LeadsController::class, 'forwardToAdmin']);
Route::post('/leads/{lead}/documents', [LeadsController::class, 'storeDocument'])->name('leads.documents.store');
Route::post('/leads/{id}/update', [LeadsController::class, 'update'])->name('leads.update');

//credit card
 Route::get('/operations/creditcardlead-details', [OperationDashboardController::class, 'show'])->name('creditcardlead-details');
 Route::put('/creditcardleads/{id}/update', [LeadsController::class, 'creditcardUpdate'])->name('creditcardleads.update');
   Route::put('/leads/{id}/creditcardstatus', [LeadsController::class, 'updatereditcardStatus'])->name('leads.creditcardstatus');
   Route::post('/dashboard/credit-card', [OperationDashboardController::class, 'filterCreditCard'])->name('dashboard.credit-card');


//Task Management
  Route::get('/tasks',[OperationsTaskController::class,'index'])->name('tasks.index');
  Route::post('/tasks/store', [OperationsTaskController::class, 'store'])->name('operations.tasks.store');
    //Route::get('/tasks/list', [OperationsTaskController::class, 'list'])->name('operations.tasks.list');
    Route::get('/tasks/all', [OperationsTaskController::class, 'getAllTasksForOperations'])->name('operations.tasks.all');



     // Attendance Management
    Route::get('/attendance', [OperationAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{id}', [OperationAttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/check-in', [OperationAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [OperationAttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/complaint', [OperationAttendanceController::class, 'submitComplaint'])->name('attendance.complaint');


    //Report Management
    Route::get('/reports', [OperationsReportController::class, 'indexReports'])->name('reports.index');
    Route::get('/export-report/{type}', [OperationsReportController::class, 'export'])->name('export');

    // Notifications
 Route::get('/notifications', [OperationDashboardController::class, 'indexNotifications'])->name('notifications.index');
    Route::get('/notifications/fetch', [OperationDashboardController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/{id}/mark-read', [OperationDashboardController::class, 'markRead'])->name('notifications.mark_read');
    Route::get('/tasks/{taskId}', [OperationsTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{taskId}/update-status', [OperationsTaskController::class, 'updateTaskStatus'])->name('tasks.update_status');
    // Optional: Uncomment if notification badge is needed
    Route::get('/notifications/count', [OperationDashboardController::class, 'countUnread'])->name('notifications.count');
   //leave management

    Route::get('/leave/apply', [OperationLeaveController::class, 'create'])->name('leave.index');
    Route::post('/leave/apply', [OperationLeaveController::class, 'store'])->name('leave.store');

    // Comp Off Management
    Route::get('/comp-off/apply', [OperationCompOffController::class, 'create'])->name('comp-off.index');
    Route::post('/comp-off/apply', [OperationCompOffController::class, 'store'])->name('comp-off.store');

    //offers
     Route::get('/offers', [OperationsOfferController::class, 'create'])->name('offers.create');
    Route::post('/offers', [OperationsOfferController::class, 'store'])->name('offers.store');

    Route::get('/locations/states', [LocationController::class, 'getStates']);
    Route::get('/locations/districts/{state_id}', [LocationController::class, 'getDistricts']);
    Route::get('/locations/cities/{district_id}', [LocationController::class, 'getCities']);

    Route::get('/password', [OperationPasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [OperationPasswordController::class, 'update'])->name('password.update');

    //salary slip download

Route::post('/salary-slips/{id}/download', [OperationDashboardController::class, 'download'])
    ->name('salary_slips.download');
});

// Admin routes (accessible only by users with designation 'admin')
Route::middleware(['auth', 'designation:admin'])->prefix('admin')->name('admin.')->group(function () {

 // Dashboard
   Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
   Route::post('/dashboard/leads', [AdminController::class, 'filterLeads'])->name('dashboard.leads');
   Route::get('/leads-details', [AdminController::class, 'leadsDetails'])->name('leads-details');
   Route::get('/admin/creditcardlead-details', [AdminController::class, 'show'])->name('creditcardlead-details');
  Route::get('/today-leads', [AdminController::class, 'todayLeads'])->name('today-leads');
   Route::get('/leads', [AdminController::class, 'showLeads'])->name('leads');
Route::post('/dashboard/credit-card', [AdminController::class, 'filterCreditCard'])->name('dashboard.credit-card');
Route::post('/dashboard/charts', [AdminController::class, 'filterCharts'])->name('dashboard.charts');
Route::post('/dashboard/tasks', [AdminController::class, 'filterTasks'])->name('dashboard.tasks');
Route::post('/dashboard/attendance', [AdminController::class, 'filterAttendance'])->name('dashboard.attendance');
Route::get('/live-dashboard', [AdminLiveDashboardController::class, 'index'])->name('live-dashboard');
Route::get('/live-dashboard/userindex', [AdminLiveDashboardController::class, 'userindex'])->name('live-dashboard.userindex');

// New route for lead analytics page
Route::get('/lead-analytics', [AdminController::class, 'leadAnalytics'])->name('lead-analytics');
Route::get('/lead-analytics/chart', [AdminController::class, 'getChartData'])->name('getChartsData');
    // Dynamic filtering routes
    Route::get('/leads-analytics/team-leads', [AdminController::class, 'getTeamLeads'])->name('leads-analytics.team-leads');
    Route::get('/leads-analytics/employees', [AdminController::class, 'getEmployees'])->name('leads-analytics.employees');
   //forward to operation
    // Route::post('/leads/{id}/forward-to-operations', [AdminLeadsController::class, 'forwardLeadToOperation']);
     Route::post('/leads/{id}/forward-to-operations-by-admin', [AdminLeadsController::class, 'forwardLeadToOperationByAdmin']);


   Route::get('/leads-analytics/districts', [AdminController::class, 'getDistricts'])->name('leads-analytics.districts');
Route::get('/leads-analytics/cities', [AdminController::class, 'getCities'])->name('leads-analytics.cities');
Route::get('/leads-analytics/lead-history/{leadId}', [AdminController::class, 'getLeadHistory'])->name('leads-analytics.lead-history');

//task
Route::get('/dashboard/task-users/{taskId}', [AdminController::class, 'getTaskUsers'])->name('task-users');

//Bank

Route::patch('banks/{id}/toggle-status', [BankController::class, 'toggleStatus'])->name('banks.toggle');
Route::resource('banks', BankController::class);

Route::resource('targets', TargetController::class)->except(['show']);
Route::resource('geofence', GeofenceController::class)->except(['show']);
Route::resource('employee-movements', EmployeeMovementController::class);
Route::get('/office-rules', [OfficeRuleController::class, 'edit'])->name('office_rules.edit');
Route::post('/office-rules', [OfficeRuleController::class, 'update'])->name('office_rules.update');


//Report Management
Route::get('/report', [AdminReportController::class, 'report'])->name('report');
    Route::get('/export-lead/{status}', [AdminReportController::class, 'exportLead'])->name('export.lead');
    Route::get('/export-lead-info', [AdminReportController::class, 'exportLeadInfo'])->name('export.lead.info');
    Route::get('/export-task/{taskId}', [AdminReportController::class, 'exportTask'])->name('export.task');
    // routes/web.php
Route::post('/leads/update-and-export-csv', [AdminController::class, 'updateAndExportMonths'])
    ->name('leads.update.export.csv');

    //Team Lead Management
    Route::get('/teamlead', [AdminTLController::class, 'indexTeamlead'])->name('teamlead.index');
    Route::post('/store', [AdminTLController::class, 'storeTeamlead'])->name('teamlead.store');
    Route::get('/{id}/edit', [AdminTLController::class, 'edit'])->name('teamlead.edit');
    Route::put('/{id}', [AdminTLController::class, 'update'])->name('teamlead.update');
    Route::post('/{id}/activate', [AdminTLController::class, 'activate'])->name('teamlead.activate');
    Route::post('/{id}/deactivate', [AdminTLController::class, 'deactivate'])->name('teamlead.deactivate');


    //Employee Management
     Route::get('/employee', [AdminEmployeeController::class, 'indexTeams'])->name('employees.index');
     // Store new employee
    Route::post('/employees', [AdminEmployeeController::class, 'store'])->name('employees.store');

    // Update existing employee
    Route::post('/employees/{id}', [AdminEmployeeController::class, 'update'])->name('employees.update');

Route::post('/employees/{id}/deactivate', [AdminEmployeeController::class, 'deactivate'])->name('employees.deactivate');
Route::post('/employees/{id}/activate', [AdminEmployeeController::class, 'activate'])->name('employees.activate');


//operation management
    Route::get('/operations', [AdminOperationController::class, 'indexOperation'])->name('operations.index');
    Route::post('/operations/store', [AdminOperationController::class, 'storeOperation'])->name('operations.store');
    Route::get('/operations/{id}/edit', [AdminOperationController::class, 'edit'])->name('operations.edit');
    Route::put('/operations/{id}', [AdminOperationController::class, 'update'])->name('operations.update');
    Route::post('/operations/{id}/activate', [AdminOperationController::class, 'activate'])->name('operations.activate');
    Route::post('/operations/{id}/deactivate', [AdminOperationController::class, 'deactivate'])->name('operations.deactivate');


    //hr management
    Route::get('/hr', [AdminHRController::class, 'indexHR'])->name('hr.index');
    Route::post('/hr/store', [AdminHRController::class, 'storeHR'])->name('hr.store');
    Route::get('/hr/{id}/edit', [AdminHRController::class, 'edit'])->name('hr.edit');
    Route::put('/hr/{id}', [AdminHRController::class, 'update'])->name('hr.update');
    Route::post('/hr/{id}/activate', [AdminHRController::class, 'activate'])->name('hr.activate');
    Route::post('/hr/{id}/deactivate', [AdminHRController::class, 'deactivate'])->name('hr.deactivate');


    //Task Management
  Route::get('/tasks',[AdminTaskController::class,'index'])->name('tasks.index');
  Route::post('/tasks/store', [AdminTaskController::class, 'store'])->name('operations.tasks.store');
    Route::get('/tasks/all', [AdminTaskController::class, 'getAllTasksForAdmin'])->name('operations.tasks.all');


    //Lead Management
    // List all forwarded leads
    Route::get('/leads', [AdminLeadsController::class, 'indexLeads'])->name('leads.index');
    // Show deleted leads page (move this BEFORE /leads/{id})
Route::get('/leads/deleted', [AdminLeadsController::class, 'showDeletedLeadsPage'])->name('leads.deleted');
    // Get lead details
    Route::get('/leads/{id}', [AdminLeadsController::class, 'getLeadDetails'])->name('leads.show');
    // Update lead status
    Route::put('/leads/{id}/status', [AdminLeadsController::class, 'updateStatus'])->name('leads.status');
      Route::put('/leads/{id}/creditcardstatus', [AdminLeadsController::class, 'updatereditcardStatus'])->name('leads.creditcardstatus');


    // Store a new document
    Route::post('/leads/{lead}/documents', [AdminLeadsController::class, 'storeDocument'])->name('leads.documents.store');
    // Delete a document
    Route::delete('/leads/{leadId}/documents/{documentId}', [AdminLeadsController::class, 'deleteFile'])->name('leads.documents.delete');
    // Update lead
    Route::put('/leads/{id}/update', [AdminLeadsController::class, 'update'])->name('leads.update');
        Route::put('/creditcardleads/{id}/update', [AdminLeadsController::class, 'creditcardUpdate'])->name('creditcardleads.update');
    // Upload document for existing lead_document record
    Route::post('/leads/{leadId}/documents/{documentId}/upload', [AdminLeadsController::class, 'upload'])->name('leads.documents.upload');


    // Fetch filter options
    Route::get('/filters', [AdminLeadsController::class, 'getFilters'])->name('filters');

    // Soft delete lead
Route::delete('/leads/{lead}', [AdminLeadsController::class, 'destroy']);

// Get deleted leads (admin only)
Route::get('/lead/deleted', [AdminLeadsController::class, 'getDeletedLeads']);

// Restore soft-deleted lead
Route::post('/leads/{id}/restore', [AdminLeadsController::class, 'restore']);





    // Attendance
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
//state.district,city dropdowns
Route::get('/location/states', [LocationController::class, 'getStates']);
    Route::get('/location/districts/{state_id}', [LocationController::class, 'getDistricts']);
    Route::get('/location/cities/{district_id}', [LocationController::class, 'getCities']);

    //password management
Route::get('/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [AdminPasswordController::class, 'update'])->name('password.update');

 //Leave Approve/reject
    Route::get('/leave/approvals', [AdminLeaveApprovalController::class, 'create'])->name('leave.approvals');
Route::post('/leave/approvals/{leave}', [AdminLeaveApprovalController::class, 'update'])->name('leave.approvals.update');


});

Route::middleware(['auth', 'designation:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/leave', [HrLeaveController::class, 'create'])->name('leave.index');
    Route::post('/leave', [HrLeaveController::class, 'store'])->name('leave.store');

    //Leave Approve/reject
    Route::get('/leave/approvals', [HrLeaveApprovalController::class, 'create'])->name('leave.approvals');
Route::post('/leave/approvals/{leave}', [HrLeaveApprovalController::class, 'update'])->name('leave.approvals.update');

//comp-off approve/reject
Route::get('/compoff/approvals', [HrCompoffApprovalController::class, 'index'])->name('compoff.approvals');
    Route::patch('/compoff/approvals/{compOff}', [HrCompOffApprovalController::class, 'update'])->name('compoff.approvals.update');


        //Team Lead Management
    Route::get('/teamlead', [HrTLController::class, 'indexTeamlead'])->name('teamlead.index');
    Route::post('/store', [HrTLController::class, 'storeTeamlead'])->name('teamlead.store');
    Route::get('/teamlead/{id}/details', [HrTLController::class, 'show'])->name('teamlead.show');
    Route::get('/{id}/edit', [HrTLController::class, 'edit'])->name('teamlead.edit');
   Route::post('/teamlead/update/{id}', [HrTLController::class, 'update'])->name('teamlead.update');
    Route::post('/{id}/activate', [HrTLController::class, 'activate'])->name('teamlead.activate');
    Route::post('/{id}/deactivate', [HrTLController::class, 'deactivate'])->name('teamlead.deactivate');

    // Salary Structure Routes for Team Lead (Mapped to HrTLController methods)
Route::get('/teamlead/{id}/salary', [HrTLController::class, 'getSalary'])->name('teamlead.salary.get');
Route::post('/teamlead/{id}/salary', [HrTLController::class, 'saveSalary'])->name('teamlead.salary.save');


    //Employee Management
     Route::get('/employee', [HrEmployeeController::class, 'indexTeams'])->name('employees.index');
     // Store new employee
    Route::post('/employees', [HrEmployeeController::class, 'store'])->name('employees.store');

    // Update existing employee
    Route::post('/employees/{id}', [HrEmployeeController::class, 'update'])->name('employees.update');

Route::post('/employees/{id}/deactivate', [HrEmployeeController::class, 'deactivate'])->name('employees.deactivate');
Route::post('/employees/{id}/activate', [HrEmployeeController::class, 'activate'])->name('employees.activate');
Route::get('/employees/{id}/details', [HrEmployeeController::class, 'show'])->name('employees.show');
   // Salary Structure Management routes
Route::get('/employees/{id}/salary', [HrEmployeeController::class, 'getSalary'])->name('employees.salary.get');
Route::post('/employees/{id}/salary', [HrEmployeeController::class, 'saveSalary'])->name('employees.salary.save');


//operation management
    Route::get('/operations', [HrOperationController::class, 'indexOperation'])->name('operations.index');
    Route::post('/operations/store', [HrOperationController::class, 'storeOperation'])->name('operations.store');
    Route::get('/operations/{id}/details', [HrOperationController::class, 'show'])->name('operations.show');
    Route::get('/operations/{id}/edit', [HrOperationController::class, 'edit'])->name('operations.edit');
    Route::post('/operations/{id}/update', [HrOperationController::class, 'update'])->name('operations.update');
    Route::post('/operations/{id}/activate', [HrOperationController::class, 'activate'])->name('operations.activate');
    Route::post('/operations/{id}/deactivate', [HrOperationController::class, 'deactivate'])->name('operations.deactivate');

    // NEW: Salary Structure Routes for Operations (in HrOperationController)
Route::get('/operations/{id}/salary', [HrOperationController::class, 'getSalary'])->name('operations.salary.get');
Route::post('/operations/{id}/salary', [HrOperationController::class, 'saveSalary'])->name('operations.salary.save');

    //Holidays Management
       Route::get('/holidays', [HrHolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays/store', [HrHolidayController::class, 'store'])->name('holidays.store');
    Route::get('/holidays/{id}/edit', [HrHolidayController::class, 'edit'])->name('holidays.edit');
    Route::post('/holidays/{id}/update', [HrHolidayController::class, 'update'])->name('holidays.update');
    Route::post('/holidays/{id}/toggle', [HrHolidayController::class, 'toggle'])->name('holidays.toggle');
    Route::get('/holiday-notification', [HrNotificationController::class, 'index'])->name('holiday.notification.index');
Route::post('/holiday-notification/fetch', [HrNotificationController::class, 'fetchHoliday'])->name('holiday.notification.fetch');
Route::post('/holiday-notification/send', [HrNotificationController::class, 'sendNotification'])->name('holiday.notification.send');



//Birthday Management
Route::post('/birthday/fetch', [HrNotificationController::class, 'fetchBirthday'])->name('birthday.notification.fetch');
Route::post('/birthday/send', [HrNotificationController::class, 'sendBirthdayNotification'])->name('birthday.notification.send');

   // Attendance
    Route::get('/attendance', [HrAttendanceController::class, 'index'])->name('attendance.index');

    //  // Attendance Management
    Route::get('/getattendance', [HrAttendanceController::class, 'fetchAttendance'])->name('attendance.attendance');
    Route::get('/attendance/{id}', [HrAttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/check-in', [HrAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [HrAttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/complaint', [HrAttendanceController::class, 'submitComplaint'])->name('attendance.complaint');


// Salary Slip Management
    Route::get('/salary-slips', [SalarySlipController::class, 'index'])->name('salary_slips.index');
    Route::get('/salary-slips/create', [SalarySlipController::class, 'create'])->name('salary_slips.create');
    Route::post('/salary-slips/store', [SalarySlipController::class, 'store'])->name('salary_slips.store');
    Route::post('/salary-slips/fetch-data', [SalarySlipController::class, 'fetchEmployeeData'])->name('salary_slips.fetch_data');
    // Route::get('/salary-slips/{id}/download', [SalarySlipController::class, 'download'])->name('salary_slips.download');
    Route::delete('/salary-slips/{id}', [SalarySlipController::class, 'destroy'])->name('salary_slips.destroy');



     // Notifications
 Route::get('/notifications', [HrNotificationController::class, 'indexNotifications'])->name('notifications.index');
    Route::get('/notifications/fetch', [HrNotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/{id}/mark-read', [HrNotificationController::class, 'markRead'])->name('notifications.mark_read');
     Route::get('/notifications/count', [HrNotificationController::class, 'countUnread'])->name('notifications.count');
    //salary slip download
    Route::post('/salary-slips/{id}/download', [HrNotificationController::class, 'download'])
        ->name('salary_slips.download');


         //password management
Route::get('/password', [HrPasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password', [HrPasswordController::class, 'update'])->name('password.update');

});






require __DIR__.'/auth.php';
