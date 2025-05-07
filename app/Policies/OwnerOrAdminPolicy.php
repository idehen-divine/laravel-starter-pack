<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;

class OwnerOrAdminPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determines if a user is authorized to perform an action based on ownership or admin role.
     *
     * @param User $user The user attempting to perform the action
     * @param mixed $model The model being checked for authorization
     * @param string $field The field to compare against user ID (defaults to 'user_id')
     * @return bool True if the user is authorized, false otherwise
     */
    public function authorize(User $user, $model, $field = 'user_id')
    {
        return $model->{$field} === $user->id
            || $user->hasRole([RoleEnum::ADMIN->name, RoleEnum::OWNER->name]);
    }
}
