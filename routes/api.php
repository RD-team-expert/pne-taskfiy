<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\WorkspacesController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User Authentication
Route::post('/users/signup', [SignUpController::class, 'create_account'])->middleware(['checkSignupEnabled', 'isApi']);
Route::post('/users/login', [UserController::class, 'authenticate'])->middleware(['customThrottle', 'isApi']);
Route::post('/password/reset-request', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware(['isApi']);
Route::post('/password/reset', [ForgotPasswordController::class, 'ResetPassword'])->middleware(['isApi']);
// Route::post('/users/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Roles
Route::get('/roles/{id?}', [RolesController::class, 'apiList']);

 // Settings
 Route::get('/settings/{variable}', [SettingsController::class, 'show'])->middleware(['multiguard', 'custom-verified']);

// Protected Routes
Route::middleware(['multiguard', 'custom-verified', 'has_workspace'])->group(function () {

    Route::patch('/user/fcm-token', [UserController::class, 'updateFcmToken']);

    // Profile Management
    Route::middleware('auth:sanctum')->get('/user', [ProfileController::class, 'profile']);
    Route::post('/users/photo', [ProfileController::class, 'update_photo'])->middleware(['demo_restriction', 'isApi']);
    Route::post('users/{id}/profile', [ProfileController::class, 'update'])->middleware(['demo_restriction', 'isApi']);
    Route::delete('/account/destroy', [ProfileController::class, 'destroy'])->middleware(['demo_restriction']);

    // User Management
    Route::get('/users/{id?}', [UserController::class, 'apiList']);
    Route::middleware(['customcan:manage_users'])->group(function () {
        Route::post('/users/store', [UserController::class, 'store'])->middleware(['customcan:create_users', 'log.activity', 'isApi']);
        Route::post('/users/update', [UserController::class, 'update_user'])->middleware(['customcan:edit_users', 'demo_restriction', 'log.activity', 'isApi']);
        Route::delete('/users/destroy/{id}', [UserController::class, 'delete_user'])->middleware(['customcan:delete_users', 'demo_restriction', 'log.activity']);
    });

    // Client Management
    Route::get('/clients/{id?}', [ClientController::class, 'apiList']);
    Route::middleware(['customcan:manage_clients'])->group(function () {
        Route::post('/clients/store', [ClientController::class, 'store'])->middleware(['customcan:create_clients', 'log.activity', 'isApi']);
        Route::post('/clients/update', [ClientController::class, 'update'])->middleware(['customcan:edit_clients', 'demo_restriction', 'log.activity', 'isApi']);
        Route::delete('/clients/destroy/{id}', [ClientController::class, 'destroy'])->middleware(['customcan:delete_clients', 'demo_restriction', 'log.activity']);
    });

    // Permissions
    Route::get('/permissions/{permission?}', [RolesController::class, 'checkPermissions']);

    // Dashboard
    Route::get('/upcoming-birthdays', [HomeController::class, 'upcomingBirthdaysApi']);
    Route::get('/upcoming-work-anniversaries', [HomeController::class, 'upcomingWorkAnniversariesApi']);
    Route::get('/members-on-leave', [HomeController::class, 'membersOnLeaveApi']);
    Route::get('/dashboard/statistics', [HomeController::class, 'getStatistics']);


    // Projects
    Route::get('/projects/{id?}', [ProjectsController::class, 'apiList']);
    Route::middleware(['customcan:manage_projects'])->group(function () {
        Route::post('/projects/store', [ProjectsController::class, 'store'])->middleware(['customcan:create_projects', 'log.activity', 'isApi']);
        Route::post('/projects/update', [ProjectsController::class, 'update'])->middleware(['customcan:edit_projects', 'log.activity', 'isApi']);
        Route::patch('projects/{id}/favorite', [ProjectsController::class, 'update_favorite'])->middleware(['isApi']);
        Route::patch('projects/{id}/pinned', [ProjectsController::class, 'update_pinned'])->middleware(['isApi']);
        Route::patch('projects/{id}/status', [ProjectsController::class, 'update_status'])->middleware(['customcan:edit_projects', 'log.activity', 'isApi']);
        Route::patch('projects/{id}/priority', [ProjectsController::class, 'update_priority'])->middleware(['customcan:edit_projects', 'log.activity', 'isApi']);
        Route::delete('/projects/destroy/{id}', [ProjectsController::class, 'destroy'])->middleware(['customcan:delete_projects', 'demo_restriction', 'checkAccess:App\Models\Project,projects,id,projects', 'log.activity']);
    });

    // Tasks
    Route::middleware(['customcan:manage_tasks'])->group(function () {
        Route::post('/tasks/store', [TasksController::class, 'store'])->middleware(['customcan:create_tasks', 'log.activity', 'isApi']);
        Route::get('/tasks/{id?}', [TasksController::class, 'apiList']);
        Route::post('/tasks/update', [TasksController::class, 'update'])->middleware(['customcan:edit_tasks', 'log.activity', 'isApi']);
        Route::patch('tasks/{id}/favorite', [TasksController::class, 'update_favorite'])->middleware(['isApi']);
        Route::patch('tasks/{id}/pinned', [TasksController::class, 'update_pinned'])->middleware(['isApi']);
        Route::patch('tasks/{id}/status', [TasksController::class, 'update_status'])->middleware(['customcan:edit_tasks', 'log.activity', 'isApi']);
        Route::patch('tasks/{id}/priority', [TasksController::class, 'update_priority'])->middleware(['customcan:edit_tasks', 'log.activity', 'isApi']);
        Route::delete('/tasks/destroy/{id}', [TasksController::class, 'destroy'])->middleware(['customcan:delete_tasks', 'demo_restriction', 'checkAccess:App\Models\Task,tasks,id,tasks', 'log.activity']);
    });

    // Statuses
    // Route::middleware(['customcan:manage_statuses'])->group(function () {
    Route::get('/statuses/{id?}', [StatusController::class, 'apiList']);
    // });

    // Priorities
    // Route::middleware(['customcan:manage_priorities'])->group(function () {
    Route::get('/priorities/{id?}', [PriorityController::class, 'apiList']);
    // });

    // Tags
    // Route::middleware(['customcan:manage_tags'])->group(function () {
    Route::get('/tags/{id?}', [TagsController::class, 'apiList']);
    // });

    // Workspaces
    Route::middleware(['customcan:manage_workspaces'])->group(function () {
        Route::post('/workspaces/store', [WorkspacesController::class, 'store'])->middleware(['customcan:create_workspaces', 'log.activity', 'isApi']);
        Route::get('/workspaces/{id?}', [WorkspacesController::class, 'apiList']);
        Route::post('/workspaces/update', [WorkspacesController::class, 'update'])->middleware(['customcan:edit_workspaces', 'demo_restriction', 'log.activity', 'isApi']);
        Route::delete('/workspaces/destroy/{id}', [WorkspacesController::class, 'destroy'])->middleware(['customcan:delete_workspaces', 'demo_restriction', 'checkAccess:App\Models\Workspace,workspaces,id,workspaces', 'log.activity']);
        // Route::patch('workspaces/{id}/switch', [WorkspacesController::class, 'switch'])->middleware(['checkAccess:App\Models\Workspace,workspaces,id,workspaces']);
    });
    Route::patch('workspaces/{id}/default', [WorkspacesController::class, 'setDefaultWorkspace'])->middleware(['isApi'])->middleware(['demo_restriction']);
    Route::delete('/workspaces/remove-participant', [WorkspacesController::class, 'remove_participant'])->middleware(['demo_restriction']);

    // Meetings
    Route::middleware(['customcan:manage_meetings'])->group(function () {
        Route::post('/meetings/store', [MeetingsController::class, 'store'])->middleware(['customcan:create_meetings', 'log.activity', 'isApi']);
        Route::get('/meetings/{id?}', [MeetingsController::class, 'apiList']);
        Route::post('/meetings/update', [MeetingsController::class, 'update'])->middleware(['customcan:edit_meetings', 'log.activity', 'isApi']);
        Route::delete('/meetings/destroy/{id}', [MeetingsController::class, 'destroy'])->middleware(['customcan:delete_meetings', 'demo_restriction', 'checkAccess:App\Models\Meeting,meetings,id,meetings', 'log.activity']);
    });

    // Todos
    Route::post('/todos/store', [TodosController::class, 'store'])->middleware(['log.activity', 'isApi']);
    Route::get('/todos/{id?}', [TodosController::class, 'apiList']);
    Route::post('/todos/update', [TodosController::class, 'update'])->middleware(['log.activity', 'isApi']);
    Route::patch('/todos/{id}/status', [TodosController::class, 'update_status'])->middleware(['log.activity', 'isApi']);
    Route::patch('/todos/{id}/priority', [TodosController::class, 'update_priority'])->middleware(['log.activity', 'isApi']);
    Route::delete('/todos/destroy/{id}', [TodosController::class, 'destroy'])->middleware(['demo_restriction', 'log.activity']);

    // Notes
    Route::post('/notes/store', [NotesController::class, 'store'])->middleware(['log.activity', 'isApi']);
    Route::get('/notes/{id?}', [NotesController::class, 'apiList']);
    Route::post('/notes/update', [NotesController::class, 'update'])->middleware(['log.activity', 'isApi']);
    Route::delete('/notes/destroy/{id}', [NotesController::class, 'destroy'])->middleware(['demo_restriction', 'log.activity']);

    // Notifications
    Route::middleware(['customcan:manage_system_notifications'])->group(function () {
        Route::get('/notifications/{id?}', [NotificationsController::class, 'apiList']);
        Route::delete('/notifications/destroy/{id}', [NotificationsController::class, 'destroy'])->middleware(['customcan:delete_system_notifications', 'demo_restriction']);
        Route::patch('/notifications/mark-as-read/{id?}', [NotificationsController::class, 'markAsReadAPI']);
    });

    // Leave Requests
    Route::middleware(['admin_or_user'])->group(function () {
        Route::post('/leave-requests/store', [LeaveRequestController::class, 'store'])->middleware(['log.activity', 'isApi']);
        Route::get('/leave-requests/{id?}', [LeaveRequestController::class, 'apiList']);
        Route::post('/leave-requests/update', [LeaveRequestController::class, 'update'])->middleware(['log.activity', 'isApi']);
        Route::delete('/leave-requests/destroy/{id}', [LeaveRequestController::class, 'destroy'])->middleware(['admin_or_leave_editor', 'demo_restriction', 'log.activity']);
    });

    // Activity Log
    Route::middleware(['customcan:manage_activity_log'])->group(function () {
        Route::get('/activity-log/{id?}', [ActivityLogController::class, 'list'])->middleware('isApi');
        Route::delete('/activity-log/destroy/{id}', [ActivityLogController::class, 'destroy'])->middleware(['demo_restriction', 'customcan:delete_activity_log']);
    });
});
