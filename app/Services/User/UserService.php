<?php

namespace App\Services\User;

use LaravelEasyRepository\BaseService;

interface UserService extends BaseService{
    public function userProfile();
    public function updateUserDetails($request);
    public function deleteAccount();
    public function searchByEmail($request);
}
