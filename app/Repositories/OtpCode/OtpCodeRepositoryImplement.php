<?php

namespace App\Repositories\OtpCode;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\OtpCode;

class OtpCodeRepositoryImplement extends Eloquent implements OtpCodeRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property OtpCode|mixed $model;
     */
    protected OtpCode $model;

    public function __construct(OtpCode $model)
    {
        $this->model = $model;
    }

    /**
     * Find an OTP code by its ID.
     *
     * @param int $id The unique identifier of the OTP code
     * @return OtpCode|null The OTP code model if found, otherwise null
     */
    public function findCodeById($id)
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Find an OTP code by email address.
     *
     * @param string $email The email address to search for
     * @return OtpCode|null The first OTP code matching the email, or null if not found
     */
    public function findCodeByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find an OTP code by email address and OTP code type.
     *
     * @param string $email The email address to search for
     * @param string $otpCodeType The type of OTP code to find
     * @return OtpCode|null The first OTP code matching the email and type, or null if not found
     */
    public function findCodeByEmailAndType($email, $otpCodeType)
    {
        return $this->model->where('email', $email)->where('type', $otpCodeType)->first();
    }
}
