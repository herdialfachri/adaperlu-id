<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Api\PasswordResetController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json(['message' => 'Invalid verification link'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified']);
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return response()->json(['message' => 'Email verified successfully']);
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent!']);
})->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RatingController;

// Semua user login bisa GET
Route::middleware(['auth:sanctum'])->get('/categories', [CategoryController::class, 'index']);

// Hanya admin (role_id = 1) bisa CUD
Route::middleware(['auth:sanctum', 'role_id:1'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// services
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

// hanya role 1 (admin) dan 3 (tukang) bisa CUD
Route::middleware(['auth:sanctum', 'role_id:1,3'])->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});

// ratings
Route::get('/services/{id}/ratings', [RatingController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::put('/ratings/{id}', [RatingController::class, 'update']);
    Route::delete('/ratings/{id}', [RatingController::class, 'destroy']);
});