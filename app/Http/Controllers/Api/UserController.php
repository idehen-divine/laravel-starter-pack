<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;
use App\Services\User\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\VerifyOtpRequest;
use App\Http\Requests\User\UserSearchRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UpdateUserEmailRequest;
use App\Http\Requests\User\PreferenceUpdateRequest;
use App\Http\Requests\User\UpdateUser2FAStatusRequest;

/**
 * @group User Management
 * @groupDescription Handles user-related operations including profile management, preferences, and account actions
 */
class UserController extends Controller
{
    public UserService $userService;

    public function __construct(UserService $userService, protected AuthService $authService)
    {
        $this->userService = $userService;
    }

    /**
     * Get User Profile
     * 
     * Retrieves the authenticated user's profile information including personal details and settings
     * 
     * @authenticated
     * 
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "User profile retrieved successfully.",
     *     "data": {
     *         "user": {
     *             "id": "67ecf2f9-1c8c-800a-8a31-458b59d9def7",
     *             "user_name": "john_doe",
     *             "email": "johndoe@email.com",
     *             "first_name": "John",
     *             "last_name": "doe",
     *             "other_name": null,
     *             "phone_no": null,
     *             "avatar": null,
     *             "status": true,
     *             "email_verified_at": null,
     *             "created_at": "2023-10-01T12:00:00Z",
     *             "updated_at": "2023-10-01T12:00:00Z",
     *         }
     *     }
     * }
     * 
     * @response 401 scenario=Unauthenticated {
     *     "message": "Unauthenticated"
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(): JsonResponse
    {
        return $this->userService->userProfile()->toJson();
    }

    /**
     * Update User Details
     * 
     * Updates the authenticated user's profile information including personal details
     * 
     * @authenticated
     * 
     * @bodyParam first_name string required The user's first name. Example: John
     * @bodyParam last_name string required The user's last name. Example: Doe
     * @bodyParam other_name string optional The user's other name. Example: Smith
     * @bodyParam phone_no string optional The user's phone number. Example: +1234567890
     * @bodyParam avatar file optional The user's profile picture (jpg,jpeg,png).
     * 
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "User profile updated successfully.",
     *     "data": {
     *         "user": {
     *             "id": "67ecf2f9-1c8c-800a-8a31-458b59d9def7",
     *             "user_name": "john_doe",
     *             "email": "johndoe@email.com",
     *             "first_name": "John",
     *             "last_name": "doe",
     *             "other_name": null,
     *             "phone_no": null,
     *             "avatar": null,
     *             "status": true,
     *             "email_verified_at": null,
     *             "created_at": "2023-10-01T12:00:00Z",
     *             "updated_at": "2023-10-01T12:00:00Z",
     *         }
     *     }
     * }
     * 
     * @response 422 scenario="Validation Error" {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "first_name": ["The first name field is required."],
     *         "last_name": ["The last name field is required."]
     *     }
     * }
     * 
     * @response 401 scenario="Unauthenticated" {
     *     "message": "Unauthenticated"
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserDetails(UserUpdateRequest $request): JsonResponse
    {
        return $this->userService->updateUserDetails($request)->toJson();
    }

    /**
     * Delete User Account
     * 
     * Permanently deletes the authenticated user's account and all associated data.
     * 
     * @authenticated
     * 
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "User account deleted successfully",
     *     "data": null
     * }
     * 
     * @response 401 scenario="Unauthenticated" {
     *     "message": "Unauthenticated"
     * }
     * 
     * @response 403 scenario="Forbidden" {
     *     "message": "You do not have permission to delete this account"
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        return $this->userService->deleteAccount()->toJson();
    }

    /**
     * Search Users by Email
     * 
     * Search for users in the system using their email address.
     * 
     * @authenticated
     * 
     * @bodyParam email string required The email address to search for. Example: john.doe@example.com
     * 
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "User retrieved successfully.",
     *     "data": {
     *         "user": {
     *             "id": "67ecf2f9-1c8c-800a-8a31-458b59d9def7",
     *             "user_name": "john_doe",
     *             "email": "johndoe@email.com",
     *             "first_name": "John",
     *             "last_name": "doe",
     *             "other_name": null,
     *             "phone_no": null,
     *             "avatar": null,
     *             "status": true,
     *             "email_verified_at": null,
     *             "created_at": "2023-10-01T12:00:00Z",
     *             "updated_at": "2023-10-01T12:00:00Z",
     *         }
     *     }
     * }
     * 
     * @response 422 scenario="Validation Error" {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "email": ["The email field is required."]
     *     }
     * }
     * 
     * @response 401 scenario="Unauthenticated" {
     *     "message": "Unauthenticated"
     * }
     * 
     * @response 404 scenario="Not Found" {
     *     "message": "User not found"
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByEmail(UserSearchRequest $request): JsonResponse
    {
        return $this->userService->searchByEmail($request)->toJson();
    }

    /**
     * Verify Email Reset OTP
     *
     * Verifies the One-Time Password (OTP) sent to the user's email or phone.
     *
     * @authenticated
     *
     * @bodyParam email string required The email address of the user. Example: jane.doe@example.com
     * @bodyParam otp_code string required The OTP code received by the user. Example: 123456
     * @bodyParam type string required The type of OTP.Example: RESET_EMAIL_OTP
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "OTP Code verified successfully.",
     *     "data": {
     *         "email": "jane.doe@example.com"
     *     }
     * }
     *
     * @response 422 scenario="Invalid Data" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email address is invalid"],
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
    public function verifyEmailResetOTP(VerifyOtpRequest $request): JsonResponse
    {
        return $this->authService->verifyOTP($request)->toJson();
    }

    /**
     * Update User Email
     *
     * Request a new email reset OTP mail to the user's new email address.
     *
     * @authenticated
     *
     * @bodyParam email string required The new email address to update to. Example: jane.doe@example.com
     *
     * @response 200 scenario="Success" {
     *     "message": "Email reset OTP sent.",
     *     "code": 200
     * }
     *
     * @response 422 scenario="Invalid Data" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email must be a valid email address"]
     *     },
     *     "code": 422
     * }
     *
     * @response 401 scenario="Unauthorized" {
     *     "message": "Unauthenticated",
     *     "code": 401
     * }
     *
     * @response 404 scenario="Not Found" {
     *     "message": "User not found",
     *     "code": 404
     * }
     */
    public function updateUserEmail(UpdateUserEmailRequest $request): JsonResponse
    {
        return $this->authService->updateUserEmail($request)->toJson();
    }

    /**
     * Request Password Reset
     *
     * Sends a password reset OTP to the authenticated user's email address.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *     "message": "Password reset OTP sent",
     *     "code": 200
     * }
     *
     * @response 401 scenario="Unauthorized" {
     *     "message": "Unauthenticated",
     *     "code": 401
     * }
     *
     * @response 404 scenario="Not Found" {
     *     "message": "User not found",
     *     "code": 404
     * }
     */
    public function updateUserPasswordRequest(): JsonResponse
    {
        return $this->authService->updateUserPasswordRequest()->toJson();
    }

    /**
     * Update 2FA Status
     *
     * Updates the two-factor authentication status for the authenticated user.
     *
     * @authenticated
     *
     * @bodyParam status boolean required The 2FA status to set. Example: true
     *
     * @response 200 scenario="Success" {
     *     "message": "2FA status updated successfully",
     *     "code": 200
     * }
     *
     * @response 422 scenario="Invalid Data" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "status": ["The status field must be a boolean value"]
     *     },
     *     "code": 422
     * }
     *
     * @response 401 scenario="Unauthorized" {
     *     "message": "Unauthenticated",
     *     "code": 401
     * }
     *
     * @response 404 scenario="Not Found" {
     *     "message": "User not found",
     *     "code": 404
     * }
     */
    public function updateUser2FAStatus(UpdateUser2FAStatusRequest $request): JsonResponse
    {
        return $this->authService->updateUser2FAStatus($request)->toJson();
    }
}
