<?php

namespace App\Services\OtpCode;

use LaravelEasyRepository\BaseService;

interface OtpCodeService extends BaseService{
    public function sendOtpCode($email, $otpCodeType);
}
