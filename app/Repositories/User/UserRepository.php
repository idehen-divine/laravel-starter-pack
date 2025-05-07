<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository
{

    public function findUserById($id);
    public function findUserByEmail($email);
    public function findUserByUsername($username);
    public function createUser($data);
    public function updateUser($id, $data);
    public function deleteUser($id);
    public function allUsers();
}
