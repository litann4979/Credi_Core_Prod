<?php

use App\Http\Controllers\Api\Admin\UserCreateController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\SalarySlipController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TargetController;


Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('auth/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    Route::put('/profile', [AuthenticationController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthenticationController::class, 'updateProfilePhoto']);
    Route::post('/users', [UserCreateController::class, 'store'])->name('api.users.store');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/change-password', [AuthenticationController::class, 'changePassword']);
    Route::post('/forgot-password', [AuthenticationController::class, 'forgotPassword']);

    //Dashboard API
    Route::get('/dashboard', [DashboardController::class, 'index']);
    //Lead Api

    Route::get('/leads', [LeadController::class, 'index']);
    Route::post('/leads', [LeadController::class, 'store']);
     Route::get('/leads/deletedlead', [LeadController::class, 'getDeletedLeads']);
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit']);
    Route::get('/leads/{lead}', [LeadController::class, 'show']);
    Route::post('/leads/{lead}', [LeadController::class, 'update']);
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy']);
    Route::post('leads/{id}/restore', [LeadController::class, 'restore']);

    Route::delete('leads/{id}/force', [LeadController::class, 'forceDelete']);
    Route::post('/leads/{lead}/forward', [LeadController::class, 'forward']);
    Route::get('/leads/{lead}/forward-status', [LeadController::class, 'checkForwardStatus']);
    Route::get('/leads/future', [LeadController::class, 'futureLeads']);
    Route::post('/follow-ups', [LeadController::class, 'storeFollowup']);
      Route::get('/follow-ups', [LeadController::class, 'getFollowUps']);
    Route::get('/follow-ups/{id}', [LeadController::class, 'showFollowUp']);
    Route::put('/follow-ups/{id}', [LeadController::class, 'updateFollowUp']);
    Route::delete('/follow-ups/{id}', [LeadController::class, 'deleteFollowUp']);
    Route::get('/follow-ups/leads/{lead_id}', [LeadController::class, 'getFollowUpsByLead']);

    //Get Bank Name
    Route::get('/banks', [LeadController::class, 'getBankNames']);


    // Task API (Add this for full CRUD)
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    //attendance
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::post('/attendances', [AttendanceController::class, 'store']);
    Route::post('/attendances/{attendance}', [AttendanceController::class, 'update']);
    Route::get('/attendance/status', [AttendanceController::class, 'checkTodayStatus']);
    Route::post('/attendance/location', [AttendanceController::class, 'updateLocation']);
    Route::post('/live-location', [AttendanceController::class, 'updateLocation']);
    Route::get('/geofence-settings', [AttendanceController::class, 'getGeofenceSettings']);

    //save fcm token from authenticated user
    Route::post('/save-fcm-token', [FcmTokenController::class, 'store']);



    //Team view api
    Route::get('/teams', [TeamController::class, 'index']);

    // Target API (authenticated user's assigned targets)
    Route::get('/targets', [TargetController::class, 'index']);
    Route::get('/targets/{target}', [TargetController::class, 'show']);

    Route::get('/offers', [OfferController::class, 'index'])->name('api.offers.index');

    //leave api
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    //salary api
    Route::get('/salary-slips', [SalarySlipController::class, 'index']);
    Route::get('/salary-slips/{id}/download', [SalarySlipController::class, 'downloadPdf']);

    Route::get('/states', [LocationController::class, 'getStates']);
    Route::get('/districts/{state_id}', [LocationController::class, 'getDistricts']);
    Route::get('/cities/{district_id}', [LocationController::class, 'getCities']);


    //Notification API
    Route::get('/notifications', [NotificationController::class, 'getEmployeeNotifications']);
     Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
});
