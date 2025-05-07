<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use LaravelEasyRepository\ServiceApi;
use App\Repositories\User\UserRepository;
use App\Http\Resources\UserPreferencesResource;

class UserServiceImplement extends ServiceApi implements UserService
{
	/**
	 * The main repository instance.
	 *
	 * @var UserRepository
	 */
	protected UserRepository $mainRepository;

	/**
	 * UserServiceImplement constructor.
	 *
	 * @param UserRepository $mainRepository
	 */
	public function __construct(UserRepository $mainRepository)
	{
		$this->mainRepository = $mainRepository;
	}

	/**
	 * Get the authenticated user's profile.
	 *
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
	public function userProfile()
	{
		try {
			$userId = Auth::id();
			$user = $this->mainRepository->find($userId);

			return $this->setCode(200)
				->setMessage("User profile retrieved successfully.")
				->setData([
					'user' => new UserResource($user)
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Error retrieving user profile.")
				->setError($e->getMessage());
		}
	}

	/**
	 * Update the authenticated user's profile.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
	public function updateUserDetails($request)
	{
		try {
			$userId = Auth::id();
			$validated = $request->validated();

			$this->mainRepository->update($userId, $validated);
			$user = $this->mainRepository->find($userId);

			return $this->setCode(200)
				->setMessage("User profile updated successfully.")
				->setData([
					'user' => new UserResource($user)
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Error updating user profile.")
				->setError($e->getMessage());
		}
	}

	/**
	 * Delete the authenticated user's account.
	 *
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
	public function deleteAccount()
	{
		try {
			$userId = Auth::id();
			$this->mainRepository->delete($userId);

			return $this->setCode(200)
				->setMessage("User account deleted successfully.");
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Error deleting user account.")
				->setError($e->getMessage());
		}
	}

	/**
	 * Search for a user by email.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse|mixed
	 */
	public function searchByEmail($request)
	{
		try {
			$validated = $request->validated();
			$user = $this->mainRepository->findUserByEmail($validated['email']);

			if (!$user) {
				return $this->setCode(404)
					->setMessage("User not found.");
			}

			return $this->setCode(200)
				->setMessage("User retrieved successfully.")
				->setData([
					'user' => new UserResource($user)
				]);
		} catch (\Exception $e) {
			return $this->setCode(500)
				->setMessage("Error searching for user.")
				->setError($e->getMessage());
		}
	}
}
