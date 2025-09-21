<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderHistoryController;


// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified']);
    }

    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully']);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent!']);
})->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);


// Category routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role_id:1'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// Service routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role_id:1,3'])->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});


// Rating routes
Route::get('/services/{id}/ratings', [RatingController::class, 'index']);
Route::middleware(['auth:sanctum', 'role_id:2'])->group(function () {
    Route::post('/ratings', [RatingController::class, 'store']);
});


// Order routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::middleware('role_id:2')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
        // Route::post('/orders/{id}/complete', [OrderController::class, 'complete']);
    });

    Route::middleware('role_id:3')->group(function () {
        Route::post('/orders/{id}/action', [OrderController::class, 'workerAction']);
    });

    Route::middleware('role_id:1')->group(function () {
        Route::put('/orders/{id}', [OrderController::class, 'update']);
    });

    Route::middleware(['auth:sanctum', 'role_id:2'])->group(function () {
        Route::post('/orders/{id}/complete', [OrderController::class, 'markCompleted']);
    });

    // Order history routes
    Route::get('/orders/{orderId}/histories', [OrderHistoryController::class, 'index']);
    Route::get('/histories/{id}', [OrderHistoryController::class, 'show']);
});
