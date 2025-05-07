<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResendOTPVerificationRequest;

/**
 * @group Authentication
 * @groupDescription Handles user authentication operations including login, registration, password management and email verification
 */
class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * User Login
     *
     * Authenticates a user and returns an access token.
     * Acess tokens are long lived, they do not expire except revoked.
     *
     * @bodyParam email string required User's email address. Example: john.doe@example.com
     * @bodyParam password string required User's password. Example: SecurePass123
     *
     * @responseField token string The JWT access token
     * @responseField user object The authenticated user's details
     * @responseField permissions array The user's permissions
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "Login Success",
     *     "data": {
     *         "user": {
     *             "id": "0196760b-1c1d-73a7-902a-1fc18c6dd8df",
     *             "user_name": "architecto",
     *             "email": "john.doe@example.com",
     *             "first_name": "John",
     *             "last_name": "Doe",
     *             "other_name": "Doe",
     *             "phone_no": "2348012345678",
     *             "avatar": "/storage/",
     *             "status": "Active",
     *             "email_verified_at": null,
     *             "created_at": "2025-04-27T06:59:21.000000Z",
     *             "updated_at": "2025-04-27T07:07:02.000000Z"
     *         },
     *         "role": [
     *             "USER"
     *         ],
     *         "permissions": [
     *             "ACCESS_USER"
     *         ],
     *         "token": "2|9uJVn98BeTodLuLl0fNs73lDw5e389l9h0anyezqd7821866"
     *     }
     * }
     *
     * @response 401 scenario="Invalid Credentials" {
     *     "code": 401,
     *     "message": "Invalid credentials",
     *     "data": null
     * }
     *
     * @response 422 scenario="Validation Error" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email field is required"],
     *         "password": ["The password field is required"]
     *     }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request)->toJson();
    }

    /**
     * Admin Login
     *
     * Authenticates an admin user and returns an access token.
     * Acess tokens are long lived, they do not expire except revoked.
     *
     * @bodyParam email string required Admin's email address. Example: admin@example.com
     * @bodyParam password string required Admin's password. Example: adminpass123
     *
     * @responseField token string The access token
     * @responseField user object The authenticated admin user's details
     * @responseField permissions array The admin user's permissions
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "Admin login successful",
     *     "data": {
     *         "user": {
     *             "id": "67ecf2f9-1c8c-800a-8a31-458b59d9def7",
     *             "user_name": "admin_user",
     *             "email": "admin@example.com",
     *             "first_name": "Admin",
     *             "last_name": "User",
     *             "other_name": null,
     *             "phone_no": null,
     *             "avatar": null,
     *             "status": true,
     *             "email_verified_at": "2023-10-01T12:00:00Z",
     *             "created_at": "2023-10-01T12:00:00Z",
     *             "updated_at": "2023-10-01T12:00:00Z"
     *         },
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *         "permissions": ["manage_users", "manage_roles", "manage_settings"]
     *     }
     * }
     *
     * @response 401 scenario="Unauthorized" {
     *     "code": 401,
     *     "message": "Invalid admin credentials",
     *     "data": null
     * }
     *
     * @response 422 scenario="Validation Error" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email field is required"],
     *         "password": ["The password field is required"]
     *     }
     * }
     */
    public function adminLogin(LoginRequest $request): JsonResponse
    {
        return $this->authService->adminLogin($request)->toJson();
    }

    /**
     * User Logout
     *
     * Invalidates the user's authentication token and logs them out of the system.
     *
     * @authenticated
     *
     * @responseField message string The success message
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "Successfully logged out",
     *     "data": null
     * }
     *
     * @response 401 scenario="Unauthorized" {
     *     "code": 401,
     *     "message": "Unauthenticated",
     *     "data": null
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request)->toJson();
    }

    /**
     * User Registration
     *
     * Registers a new user in the system with the provided information.
     *
     * @bodyParam first_name string required The user's first name. Example: John
     * @bodyParam last_name string required The user's last name. Example: Doe
     * @bodyParam other_name string required The user's other name. Example: Doe
     * @bodyParam email string required The user's email address. Example: john.doe@example.com
     * @bodyParam user_name string required The user's username. Example: firstuser
     * @bodyParam password string required The user's password (min: 8 characters). Example: SecurePass123
     * @bodyParam password_confirmation string required Password confirmation. Example: SecurePass123
     * @bodyParam phone_no string required The user's phone number. Example: 2348012345678
     *
     * @responseField user object The registered user's information
     * @responseField token string The authentication token for the registered user
     *
     * @response 201 scenario="Success" {
     *     "code": 201,
     *     "message": "OTP verification sent to email.",
     *     "data": {
     *         "email": "john.doe@example.com"
     *     }
     * }
     *
     * @response 422 scenario="Validation Error" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email has already been taken"],
     *         "password": ["The password must be at least 8 characters"],
     *     }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request)->toJson();
    }

    /**
     * Forgot Password
     *
     * Initiates the password reset process by sending a otp to the user's email.
     * for development environment, use `111111` as your otp.go to the verify otp endpoint to verify the otp.
     * Then password can be changed in the reset password endpoint within 10mins.
     *
     * @bodyParam email string required The email address of the user. Example: john.doe@example.com
     *
     * @responseField message string The status message of the operation
     * @responseField code integer The HTTP status code
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "Password reset OTP sent",
     * }
     *
     * @response 422 scenario="Invalid Email" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": [
     *             "We can't find a user with that email address"
     *         ]
     *     }
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        return $this->authService->forgotPassword($request)->toJson();
    }

    /**
     * Reset Password
     *
     * Resets the user's password using the provided OTP and new password.
     *
     * @bodyParam email string required The email address of the user. Example: john.doe@example.com
     * @bodyParam password string required The new password (min 8 characters). Example: newPassword123
     * @bodyParam password_confirmation string required Confirmation of the new password. Example: newPassword123
     *
     * @responseField message string The status message of the operation
     * @responseField code integer The HTTP status code
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "Password reset successful"
     * }
     *
     * @response 422 scenario="Invalid Data" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email address is invalid"],
     *         "otp": ["The OTP is invalid or has expired"],
     *         "password": ["The password must be at least 8 characters"]
     *     }
     * }
     *
     * @response 404 scenario="Not Found" {
     *     "message": "User not found",
     *     "code": 404
     * }
     *
     * @response 400 scenario="Invalid OTP" {
     *     "message": "Invalid or expired OTP code",
     *     "code": 400
     * }
     *
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->authService->resetPassword($request)->toJson();
    }

    /**
     * Verify OTP
     *
     * Verifies the One-Time Password (OTP) sent to the user's email or phone.
     *
     * @bodyParam email string required The email address of the user. Example: john.doe@example.com
     * @bodyParam otp_code string required The OTP code received by the user. Example: 123456
     * @responseField message string The status message of the operation
     * @responseField code integer The HTTP status code
     *
     * @response 200 scenario="Success type=RESET_PASSWORD_OTP" {
     *     "code": 200,
     *     "message": "OTP verified successfully"
     *     "data": {
     *        "email": "john.doe@example.com"
     *      }
     * }
     *
     * @response 200 scenario="Success type=VERIFY_EMAIL_OTP" {
     *     "code": 200,
     *     "message": "Login Success",
     *     "data": {
     *         "user": {
     *             "id": "0196760b-1c1d-73a7-902a-1fc18c6dd8df",
     *             "user_name": "architecto",
     *             "email": "john.doe@example.com",
     *             "first_name": "John",
     *             "last_name": "Doe",
     *             "other_name": "Doe",
     *             "phone_no": "2348012345678",
     *             "avatar": "/storage/",
     *             "status": "Active",
     *             "email_verified_at": null,
     *             "created_at": "2025-04-27T06:59:21.000000Z",
     *             "updated_at": "2025-04-27T06:59:21.000000Z"
     *         },
     *         "role": [
     *             "USER"
     *         ],
     *         "permissions": [
     *             "ACCESS_USER"
     *         ],
     *         "token": "1|VLuBcYDyMCoDdPCox5BbQbCrsiirt2XAa9PZwaVAc7442bb8"
     *     }
     * }
     *
     * @response 422 scenario="Invalid Data" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email address is does not exist. Please check your email address and try again."],
     *         "otp": ["The OTP code is required"]
     *     }
     * }
     *
     * @response 400 scenario="Invalid OTP" {
     *     "message": "Invalid or expired OTP code",
     *     "code": 400
     * }
     *
     * @response 404 scenario="Not Found" {
     *     "message": "User not found",
     *     "code": 404
     * }
     */
    public function verifyOTP(VerifyOtpRequest $request): JsonResponse
    {
        return $this->authService->verifyOTP($request)->toJson();
    }

    /**
     * Resend OTP Verification
     *
     * Resends a new OTP code to the user's email or phone number for verification purposes.
     *
     * @bodyParam email string required The email address to send the OTP to. Example: john.doe@example.com
     * @bodyParam type string required The type of OTP verification. Must be one of: `RESET_PASSWORD_OTP`, `VERIFY_EMAIL_OTP`, `RESET_EMAIL_OTP`, `VERIFY_2FA_OTP`. Example: RESET_PASSWORD_OTP
     *
     * @responseField message string The status message of the operation
     * @responseField code integer The HTTP status code
     *
     * @response 200 scenario="Success" {
     *     "message": "OTP code has been resent successfully",
     *     "code": 200,
     *     "data": {
     *         "email": "john.doe@example.com"
     *     }
     * }
     *
     * @response 422 scenario="Invalid Data" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email field is required"],
     *         "type": ["The type field is required"]
     *     },
     *     "code": 422
     * }
     *
     * @response 404 scenario="Not Found" {
     *     "message": "User not found",
     *     "code": 404
     * }
     *
     * @response 429 scenario="Too Many" {
     *     "message": "Too many OTP requests. Please try again later",
     *     "code": 429
     * }
     */
    public function resendOTPVerification(ResendOTPVerificationRequest $request): JsonResponse
    {
        return $this->authService->resendOTPVerification($request)->toJson();
    }
}
