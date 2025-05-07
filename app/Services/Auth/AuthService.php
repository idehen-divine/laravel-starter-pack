<?php

namespace App\Services\Auth;

use LaravelEasyRepository\BaseService;

interface AuthService extends BaseService{
    public function login($request);
    public function adminLogin($request);
    public function logout($request);
    public function register($request);
    public function forgotPassword($request);
    public function resetPassword($request);
    public function verifyOtp($request);
    public function resendOTPVerification($request);
    public function updateUserEmail($request);
    public function updateUserPasswordRequest();
    public function updateUser2FAStatus($request);
}
