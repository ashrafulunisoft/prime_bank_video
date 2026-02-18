<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoCallController;

/*
|--------------------------------------------------------------------------
| Video Call Routes
|--------------------------------------------------------------------------
*/

// Customer routes (authenticated)
Route::middleware(['auth'])->group(function () {
    // OTP verification for video call
    Route::get('/video/verify-otp', [VideoCallController::class, 'showOtpVerification'])->name('video.verify.otp');
    Route::post('/video/send-otp', [VideoCallController::class, 'sendOtp'])->name('video.send.otp');
    Route::post('/video/verify-otp', [VideoCallController::class, 'verifyOtp'])->name('video.verify.otp.submit');
    
    // Customer video call page
    Route::get('/video/call', [VideoCallController::class, 'customerCall'])->name('video.call');
    
    // Request video call
    Route::post('/video/request-call', [VideoCallController::class, 'customerRequestCall'])->name('video.request.call');
    
    // Check queue status
    Route::get('/video/queue-status', [VideoCallController::class, 'queueStatus'])->name('video.queue.status');
    
    // Cancel queue
    Route::post('/video/cancel-queue', [VideoCallController::class, 'cancelQueue'])->name('video.cancel.queue');
    
    // End call
    Route::post('/video/end-call', [VideoCallController::class, 'endCall'])->name('video.end.call');
    
    // Feedback
    Route::get('/video/feedback', [VideoCallController::class, 'feedback'])->name('video.feedback');
    Route::post('/video/feedback', [VideoCallController::class, 'submitFeedback'])->name('video.feedback.submit');
});

// Agent routes
Route::middleware(['auth'])->group(function () {
    Route::get('/video/agent/dashboard', [VideoCallController::class, 'agentDashboard'])->name('video.agent.dashboard');
    Route::post('/video/agent/status', [VideoCallController::class, 'agentStatus'])->name('video.agent.status');
    Route::post('/video/agent/start-call', [VideoCallController::class, 'agentStartCall'])->name('video.agent.start.call');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/video/admin/dashboard', [VideoCallController::class, 'adminDashboard'])->name('video.admin.dashboard');
    Route::get('/video/admin/stats', [VideoCallController::class, 'apiStats'])->name('video.admin.stats');
});
