<?php

use Illuminate\Support\Facades\Route;

// Auth Routes (Public)
Route::post('/auth/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/auth/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile & Password
    Route::get('/auth/user', [\App\Http\Controllers\Auth\LoginController::class, 'user']);
    Route::post('/auth/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    Route::put('/auth/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update']);
    Route::put('/auth/first-login', [\App\Http\Controllers\Auth\PasswordController::class, 'firstLogin']);

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);

        // Guardians
        Route::apiResource('guardians', \App\Http\Controllers\Admin\GuardianController::class);
        Route::post('/guardians/{guardian}/verify', [\App\Http\Controllers\Admin\GuardianController::class, 'verify']);
        Route::post('/guardians/{guardian}/toggle-status', [\App\Http\Controllers\Admin\GuardianController::class, 'toggleStatus']);

        // Students
        Route::apiResource('students', \App\Http\Controllers\Admin\StudentController::class);
        Route::post('/students/{student}/toggle-status', [\App\Http\Controllers\Admin\StudentController::class, 'toggleStatus']);

        // Teachers
        Route::apiResource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
        Route::post('/teachers/{teacher}/toggle-status', [\App\Http\Controllers\Admin\TeacherController::class, 'toggleStatus']);

        // Classes
        Route::apiResource('classes', \App\Http\Controllers\Admin\ClassController::class);
        Route::post('/classes/{class}/assign-teacher', [\App\Http\Controllers\Admin\ClassController::class, 'assignTeacher']);
        Route::delete('/classes/{class}/remove-teacher/{teacher}', [\App\Http\Controllers\Admin\ClassController::class, 'removeTeacher']);

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index']);
        Route::post('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store']);
        Route::get('/attendance/report', [\App\Http\Controllers\Admin\AttendanceController::class, 'report']);

        // Evaluations
        Route::apiResource('evaluations', \App\Http\Controllers\Admin\EvaluationController::class);
        Route::get('/evaluations/student/{student}', [\App\Http\Controllers\Admin\EvaluationController::class, 'studentEvaluations']);

        // Report Cards
        Route::apiResource('report-cards', \App\Http\Controllers\Admin\ReportCardController::class);
        Route::post('/report-cards/{reportCard}/publish', [\App\Http\Controllers\Admin\ReportCardController::class, 'publish']);

        // Fee Management
        Route::apiResource('fee-structures', \App\Http\Controllers\Admin\FeeStructureController::class);
        Route::get('/fees/student/{student}', [\App\Http\Controllers\Admin\FeeController::class, 'studentFees']);
        Route::post('/fees/{feeRecord}/pay', [\App\Http\Controllers\Admin\FeeController::class, 'pay']);

        // Announcements
        Route::apiResource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
        Route::post('/announcements/{announcement}/publish', [\App\Http\Controllers\Admin\AnnouncementController::class, 'publish']);

        // Events
        Route::apiResource('events', \App\Http\Controllers\Admin\EventController::class);

        // Routine
        Route::apiResource('routines', \App\Http\Controllers\Admin\RoutineController::class);
        Route::get('/routines/class/{class}', [\App\Http\Controllers\Admin\RoutineController::class, 'classRoutine']);
        Route::get('/routines/teacher/{teacher}', [\App\Http\Controllers\Admin\RoutineController::class, 'teacherRoutine']);

        // Exam Routines
        Route::apiResource('exam-routines', \App\Http\Controllers\Admin\ExamRoutineController::class);

        // Profile Change Requests
        Route::get('/profile-change-requests', [\App\Http\Controllers\Admin\ProfileChangeRequestController::class, 'index']);
        Route::post('/profile-change-requests/{profileChangeRequest}/approve', [\App\Http\Controllers\Admin\ProfileChangeRequestController::class, 'approve']);
        Route::post('/profile-change-requests/{profileChangeRequest}/reject', [\App\Http\Controllers\Admin\ProfileChangeRequestController::class, 'reject']);

        // Applications
        Route::get('/applications', [\App\Http\Controllers\Admin\ApplicationController::class, 'index']);
        Route::post('/applications/{application}/approve', [\App\Http\Controllers\Admin\ApplicationController::class, 'approve']);
        Route::post('/applications/{application}/reject', [\App\Http\Controllers\Admin\ApplicationController::class, 'reject']);

        // Messages
        Route::get('/messages/unread-count', [\App\Http\Controllers\Admin\MessageController::class, 'unreadCount']);
        Route::apiResource('messages', \App\Http\Controllers\Admin\MessageController::class);
        Route::post('/messages/{message}/read', [\App\Http\Controllers\Admin\MessageController::class, 'markAsRead']);

        // Activity Log
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index']);
    });

    // Teacher Routes
    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index']);

        // My Classes
        Route::get('/classes', [\App\Http\Controllers\Teacher\ClassController::class, 'index']);
        Route::get('/classes/{class}/students', [\App\Http\Controllers\Teacher\ClassController::class, 'students']);

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'index']);
        Route::post('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store']);
        Route::get('/attendance/report', [\App\Http\Controllers\Teacher\AttendanceController::class, 'report']);

        // Diary
        Route::apiResource('diary', \App\Http\Controllers\Teacher\DiaryController::class);
        Route::get('/diary/class/{class}', [\App\Http\Controllers\Teacher\DiaryController::class, 'classDiary']);

        // Evaluations
        Route::apiResource('evaluations', \App\Http\Controllers\Teacher\EvaluationController::class);

        // Routine
        Route::get('/routine', [\App\Http\Controllers\Teacher\RoutineController::class, 'index']);

        // Messages
        Route::apiResource('messages', \App\Http\Controllers\Teacher\MessageController::class);
        Route::post('/messages/{message}/read', [\App\Http\Controllers\Teacher\MessageController::class, 'markAsRead']);

        // Leave Notifications
        Route::get('/leave-notifications', [\App\Http\Controllers\Teacher\LeaveNotificationController::class, 'index']);
        Route::post('/leave-notifications/{leaveNotification}/approve', [\App\Http\Controllers\Teacher\LeaveNotificationController::class, 'approve']);
        Route::post('/leave-notifications/{leaveNotification}/reject', [\App\Http\Controllers\Teacher\LeaveNotificationController::class, 'reject']);
    });

    // Guardian Routes
    Route::middleware('role:guardian')->prefix('guardian')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Guardian\DashboardController::class, 'index']);

        // My Children
        Route::get('/children', [\App\Http\Controllers\Guardian\ChildController::class, 'index']);
        Route::get('/children/{student}', [\App\Http\Controllers\Guardian\ChildController::class, 'show']);

        // Attendance
        Route::get('/attendance/{student}', [\App\Http\Controllers\Guardian\AttendanceController::class, 'index']);

        // Diary
        Route::get('/diary/{student}', [\App\Http\Controllers\Guardian\DiaryController::class, 'index']);
        Route::post('/diary/{diaryEntry}/comment', [\App\Http\Controllers\Guardian\DiaryController::class, 'comment']);

        // Evaluations
        Route::get('/evaluations/{student}', [\App\Http\Controllers\Guardian\EvaluationController::class, 'index']);

        // Report Cards
        Route::get('/report-cards/{student}', [\App\Http\Controllers\Guardian\ReportCardController::class, 'index']);

        // Fees
        Route::get('/fees/{student}', [\App\Http\Controllers\Guardian\FeeController::class, 'index']);

        // Profile Change Request
        Route::get('/profile-change-request', [\App\Http\Controllers\Guardian\ProfileChangeRequestController::class, 'index']);
        Route::post('/profile-change-request', [\App\Http\Controllers\Guardian\ProfileChangeRequestController::class, 'store']);

        // Messages
        Route::apiResource('messages', \App\Http\Controllers\Guardian\MessageController::class);
        Route::post('/messages/{message}/read', [\App\Http\Controllers\Guardian\MessageController::class, 'markAsRead']);

        // Leave Notifications
        Route::get('/leave-notifications', [\App\Http\Controllers\Guardian\LeaveNotificationController::class, 'index']);
        Route::post('/leave-notifications', [\App\Http\Controllers\Guardian\LeaveNotificationController::class, 'store']);
    });
});
