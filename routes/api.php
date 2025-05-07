<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\PassengerController;

Route::group(['prefix' => 'v1'], function () {
    Route::get('/', function () {
        return redirect(url('/docs'));
    });

    /**********************    Auth Routes    ***********************/
    Route::prefix('auth')->middleware('guest')->name('auth.')->group(function () {
        Route::post('/sign-in', [AuthController::class, 'login'])->name('login');
        Route::post('/sign-up', [AuthController::class, 'register'])->name('register');
        Route::post('/sign-out', [AuthController::class, 'logout'])->withoutMiddleware('guest')->middleware('auth:sanctum')->name('logout');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
        Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify-otp');
        Route::post('/resend-otp-verification', [AuthController::class, 'resendOTPVerification'])->name('resend-otp-verification');
        // Route::post('/social-signin', [AuthController::class, 'socialSignIn']);

        Route::post('/admin/sign-in', [AuthController::class, 'adminLogin'])->name('admin.login');
    });

    Route::prefix('users')->name('user')->group(function () {
        Route::get('/search', [UserController::class, 'searchByEmail']);
        Route::get('/', [UserController::class, 'userProfile'])->name('.profile');
        Route::put('/', [UserController::class, 'updateUserDetails'])->name('.profile.update');
        Route::delete('/', [UserController::class, 'deleteAccount'])->name('.account.delete');
        Route::patch('/update-email', [UserController::class, 'updateUserEmail'])->name('.update-email');
        Route::post('/verify-email-reset-otp', [UserController::class, 'verifyEmailResetOTP'])->name('.verify-email-reset-otp');
        Route::post('/update-password-request', [UserController::class, 'updateUserPasswordRequest'])->name('.update-password-request');
        Route::patch('/update-2fa-status', [UserController::class, 'updateUser2FAStatus'])->name('.update-2fa-status');
        Route::prefix('notification')->name('notification')->group(function () {
            // Route::get('/', [UserController::class, 'userNotification'])->name('.index');
        });
    });
});