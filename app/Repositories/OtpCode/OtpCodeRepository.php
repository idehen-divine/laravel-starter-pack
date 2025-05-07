<?php

namespace App\Repositories\OtpCode;

use LaravelEasyRepository\Repository;

interface OtpCodeRepository extends Repository{
    public function findCodeById($id);
    public function findCodeByEmail($email);
    public function findCodeByEmailAndType($email, $otpCodeType);
}
