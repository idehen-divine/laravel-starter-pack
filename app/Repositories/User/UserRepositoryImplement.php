<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\User;

class UserRepositoryImplement extends Eloquent implements UserRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property User|mixed $model;
     */
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email The email address to search for
     * @return \App\Models\User|null The user model if found, otherwise null
     */
    public function findUserByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id The unique identifier of the user
     * @return \Illuminate\Database\Eloquent\Builder|null The user model if found, otherwise null
     */
    public function findUserById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find a user by their username.
     *
     * @param string $username The username to search for
     * @return \App\Models\User|null The user model if found, otherwise null
     */
    public function findUserByUsername($username)
    {
        return $this->model->where('user_name', $username)->first();
    }

    /**
     * Create a new user in the database.
     *
     * @param array $data The user data to be used for creating a new user
     * @return \App\Models\User The newly created user model
     */
    public function createUser($data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a user's information in the database.
     *
     * @param int $id The unique identifier of the user to update
     * @param array $data The updated user data
     * @return bool Indicates whether the update was successful
     */
    public function updateUser($id, $data)
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * Delete a user from the database.
     *
     * @param int $id The unique identifier of the user to delete
     * @return bool Indicates whether the deletion was successful
     */
    public function deleteUser($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Get all users from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of all users
     */
    public function allUsers()
    {
        return $this->model->all();
    }
}
