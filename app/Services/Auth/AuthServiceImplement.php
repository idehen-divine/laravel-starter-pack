<?php

namespace App\Services\Auth;

use App\Enums\RoleEnum;
use App\Enums\OTPTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use LaravelEasyRepository\ServiceApi;
use App\Services\OtpCode\OtpCodeService;
use App\Repositories\User\UserRepository;
use App\Repositories\OtpCode\OtpCodeRepository;

class AuthServiceImplement extends ServiceApi implements AuthService
{
	/**
	 * don't change $this->mainRepository variable name
	 * because used in extends service class
	 */
	protected UserRepository $userRepository;
	protected OtpCodeService $otpCodeService;
	protected OtpCodeRepository $otpCodeRepository;

	public function __construct(UserRepository $userRepository, OtpCodeService $otpCodeService, OtpCodeRepository $otpCodeRepository)
	{
		$this->userRepository = $userRepository;
		$this->otpCodeService = $otpCodeService;
		$this->otpCodeRepository = $otpCodeRepository;
	}

	/**
	 * Authenticate a user and generate an authentication token.
	 *
	 * @param mixed $request The validated login request containing user credentials
	 * @return \Illuminate\Http\JsonResponse Authentication response with user details and token
	 */
	public function login($request): AuthServiceImplement
	{
		try {
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail($validated['email']);

			if (!$user || !Hash::check($validated['password'], $user->password)) {
				return $this->setCode(401)->setMessage("Invalid credentials");
			}

			if ($user->is_2fa_enabled) {
				$otpCode = $this->otpCodeService->sendOtpCode($user->email, OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name);
				return $this->setCode(200)
					->setMessage("OTP verification sent to email.")
					->setData([
						"email" => $validated['email']
					]);
			}

			$user->tokens()->delete();

			$token = $user->createToken('API Token')->plainTextToken;

			return $this->setCode(200)
				->setMessage("Login Success")
				->setData([
					'user' => new UserResource($user),
					'role' => $user->getRoleNames(),
					'permissions' => $user->getAllPermissionNames(),
					'token' => $token
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Login Failed")
				->setError($e->getMessage());
		}
	}

	/**
	 * Authenticate an admin user and generate an authentication token.
	 *
	 * @param mixed $request The validated admin login request containing user credentials
	 * @return \Illuminate\Http\JsonResponse Authentication response with admin user details and token
	 */
	public function adminLogin($request): AuthServiceImplement
	{

		try {
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail($validated['email']);

			if (!$user || !Hash::check($validated['password'], $user->password)) {
				return $this->setCode(401)->setMessage("Invalid credentials");
			}

			if (!$user->hasAnyRole([RoleEnum::OWNER->name, RoleEnum::ADMIN->name])) {
				return $this->setCode(403)->setMessage("Forbidden");
			}

			if ($user->is_2fa_enabled) {
				$otpCode = $this->otpCodeService->sendOtpCode($user->email, OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name);
				return $this->setCode(200)
					->setMessage("OTP verification sent to email.")
					->setData([
						"email" => $user->email,
					]);
			}

			// Revoke old tokens (optional)
			$user->tokens()->delete();

			// Generate Sanctum token
			$token = $user->createToken('API Token')->plainTextToken;

			return $this->setCode(200)
				->setMessage("Login Success")
				->setData([
					'user' => new UserResource($user),
					'role' => $user->getRoleNames(),
					'permissions' => $user->getAllPermissionNames(),
					'token' => $token
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Admin Login Failed")
				->setError($e->getMessage());
		}
	}

	/**
	 * Logout the currently authenticated user by deleting their current access token.
	 *
	 * @param mixed $request The current authentication request
	 * @return \Illuminate\Http\JsonResponse Response indicating the logout status
	 */
	public function logout($request): AuthServiceImplement
	{
		try {
			$request->user()->currentAccessToken()->delete();
			return $this->setCode(200)
				->setMessage("Logout Successfull");
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Logout Failed")
				->setError($e->getMessage());
		}
	}

	/**
	 * Register a new user and send email verification OTP.
	 *
	 * @param mixed $request The registration request containing validated user data
	 * @return \Illuminate\Http\JsonResponse Response with registration status and OTP details
	 */
	public function register($request): AuthServiceImplement
	{
		try {
			$validated = $request->validated();
			$user = $this->userRepository->createUser($validated);
			$user->setRole(RoleEnum::USER->name);

			// Send OTP Code For Email Authentication
			$otpCode = $this->otpCodeService->sendOtpCode($user->email, OTPTypeEnum::VERIFY_EMAIL_OTP->name);

			return $this->setCode(201)
				->setMessage("OTP verification sent to email.")
				->setData([
					"email" => $otpCode->email
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Registration Failed")
				->setError($e->getMessage());
		}
	}

	/**
	 * Initiate the password reset process by sending a reset OTP to the user's email.
	 * 
	 * @param mixed $request The request containing the user's email for password reset
	 * @return \Illuminate\Http\JsonResponse Response with OTP sending status
	 */
	public function forgotPassword($request): AuthServiceImplement
	{
		try {
			DB::beginTransaction();
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail($validated['email']);
			if (!$user) {
				DB::rollBack();
				return $this->setCode(404)->setMessage('No user found with this email');
			}

			$otpCode = $this->otpCodeService->sendOtpCode($user->email, OTPTypeEnum::RESET_PASSWORD_OTP->name);
			DB::commit();
			return $this->setCode(200)
				->setMessage('Password reset OTP sent.');
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->setCode(500)
				->setMessage('An error occurred while sending the reset OTP')
				->setError($e->getMessage());
		}
	}

	/**
	 * Update the two-factor authentication (2FA) status for the authenticated user.
	 * 
	 * @param mixed $request The request containing the 2FA status to be updated
	 * @return AuthServiceImplement Response with the updated 2FA status
	 */
	public function updateUser2FAStatus($request): AuthServiceImplement
	{
		try {
			DB::beginTransaction();
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail(Auth::user()->email);
			$status = $validated['status'] ? 'enabled' : 'disabled';
			$user->update([
				'is_2fa_enabled' => $validated['status']
			]);

			DB::commit();
			return $this->setCode(200)
				->setMessage("2FA status {$status} successfully.");
		} catch (\Exception $e) {
			DB::rollBack();

			return $this->setCode(500)
				->setMessage('An error occurred while updating the 2FA status')
				->setError($e->getMessage());
		}
	}

	/**
	 * Initiate the email reset process by sending a reset OTP to the user's email.
	 * 
	 * @param mixed $request The request containing the user's email for email reset
	 * @return \Illuminate\Http\JsonResponse Response with OTP sending status
	 */
	public function updateUserEmail($request): AuthServiceImplement
	{
		try {
			DB::beginTransaction();
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail($validated['email']);
			if ($user) {
				DB::rollBack();
				return $this->setCode(400)->setMessage('An account with this email address already exist.');
			}

			$otpCode = $this->otpCodeService->sendOtpCode($validated['email'], OTPTypeEnum::RESET_EMAIL_OTP->name);
			DB::commit();
			return $this->setCode(200)
				->setMessage('Email reset OTP sent.');
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->setCode(500)
				->setMessage('An error occurred while sending the reset OTP')
				->setError($e->getMessage());
		}
	}

	/**
	 * Initiate the password reset process by sending a reset OTP to the authenticated user's email.
	 * 
	 * @return \Illuminate\Http\JsonResponse Response with OTP sending status for password reset
	 */
	public function updateUserPasswordRequest(): AuthServiceImplement
	{
		try {
			DB::beginTransaction();
			$user = $this->userRepository->findUserByEmail(Auth::user()->email);
			if (!$user) {
				DB::rollBack();
				return $this->setCode(401)->setMessage('Unauthorized');
			}

			$otpCode = $this->otpCodeService->sendOtpCode($user->email, OTPTypeEnum::RESET_PASSWORD_OTP->name);
			DB::commit();
			return $this->setCode(200)
				->setMessage('Password reset OTP sent.');
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->setCode(500)
				->setMessage('An error occurred while sending the reset OTP')
				->setError($e->getMessage());
		}
	}

	/**
	 * Reset a user's password after OTP verification.
	 * 
	 * @param mixed $request The request containing the email, new password, and verified OTP
	 * @return \Illuminate\Http\JsonResponse Response with password reset status
	 */
	public function resetPassword($request): AuthServiceImplement
	{
		try {
			DB::beginTransaction();
			$validated = $request->validated();
			$otpCode = $this->otpCodeRepository->findCodeByEmailAndType($validated['email'], OTPTypeEnum::RESET_PASSWORD_OTP->name);
			if (!$otpCode || !$otpCode->is_verified || $otpCode->code !== "0" || $otpCode->expired_at < now()) {
				return $this->setCode(400)
					->setMessage("Invalid or Expired OTP Code")
					->setError("Invalid or Expired OTP Code");
			}

			$this->userRepository->findUserByEmail($validated['email'])->update([
				'password' => Hash::make($validated['password'])
			]);

			$this->otpCodeRepository->delete($otpCode->id);
			DB::commit();
			return $this->setCode(200)
				->setMessage("Password reset successfully.");
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->setCode(500)
				->setMessage("Reset Password Failed")
				->setError($e->getMessage());
		}
	}

	/**
	 * Verify an OTP (One-Time Password) code for a specific email and code type.
	 * 
	 * @param mixed $request The request containing the OTP code and email
	 * @return \Illuminate\Http\JsonResponse Returns the verified OTP code or an error response
	 */
	public function verifyOtp($request): AuthServiceImplement
	{
		try {
			DB::beginTransaction();
			$validated = $request->validated();
			$otpCode = $this->otpCodeRepository->findCodeByEmailAndType($validated['email'], $validated['type']);

			if (!$otpCode || $otpCode->is_verified || $otpCode->code !== $validated['otp_code'] || $otpCode->expired_at < now()) {
				return $this->setCode(400)
					->setMessage("Invalid or Expired OTP Code");
			}

			if (in_array($otpCode->type, [OTPTypeEnum::VERIFY_2FA_AUTHORIZATION_OTP->name, OTPTypeEnum::RESET_PASSWORD_OTP->name])) {
				$this->otpCodeRepository->update($otpCode->id, [
					'is_verified' => true,
					'code' => 0,
					'expired_at' => now()->addMinutes(($otpCode->type === OTPTypeEnum::VERIFY_2FA_AUTHORIZATION_OTP->name) ? 30 : 10),
				]);
			}

			if ($otpCode->type === OTPTypeEnum::RESET_EMAIL_OTP->name) {
				$this->userRepository->update(Auth::user()->id, [
					'email' => $validated['email']
				]);
				$this->otpCodeRepository->delete($otpCode->id);
			}

			if (in_array($otpCode->type, [OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name, OTPTypeEnum::VERIFY_EMAIL_OTP->name])) {
				$user = $this->userRepository->findUserByEmail($otpCode->email);
				$user->update(['email_verified_at' => now()]);
				$user->tokens()->delete();
				$token = $user->createToken('API Token')->plainTextToken;
				$this->otpCodeRepository->delete($otpCode->id);

				DB::commit();
				return $this->setCode(200)
					->setMessage("Login Success")
					->setData([
						'user' => new UserResource($user),
						'role' => $user->getRoleNames(),
						'permissions' => $user->getAllPermissionNames(),
						'token' => $token
					]);
			}
			DB::commit();
			return $this->setCode(200)
				->setMessage("OTP Code verified successfully.")
				->setData([
					'email' => $validated['email'],
				]);
		} catch (\Exception $e) {
			DB::rollBack();
			return $this->setCode(500)
				->setMessage("Verify OTP Code Failed")
				->setError($e->getMessage());
		}
	}

	/**
	 * Resend OTP verification code to the specified email.
	 *
	 * @param mixed $request The request containing email and OTP type
	 * @return \Illuminate\Http\JsonResponse Response with OTP verification status
	 */
	public function resendOTPVerification($request): AuthServiceImplement
	{
		try {
			$validated = $request->validated();

			$otpCode = $this->otpCodeService->sendOtpCode($validated['email'], $validated['type']);

			return $this->setCode(200)
				->setMessage("OTP verification sent to email.")
				->setData([
					"email" => $validated['email']
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Resend OTP Failed")
				->setError($e->getMessage());
		}
	}
}
