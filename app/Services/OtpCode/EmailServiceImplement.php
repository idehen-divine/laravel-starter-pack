<?php

namespace App\Services\OtpCode;

use App\Enums\ServerEnum;
use App\Mail\OtpCodeMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use LaravelEasyRepository\ServiceApi;
use App\Repositories\User\UserRepository;
use App\Repositories\OtpCode\OtpCodeRepository;

class EmailServiceImplement extends ServiceApi implements OtpCodeService
{
    /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
    protected OtpCodeRepository $mainRepository;
    protected UserRepository $userRepository;
    protected OtpCodeRepository $otpCodeRepository;
    protected $otp;

    public function __construct(OtpCodeRepository $mainRepository, UserRepository $userRepository, OtpCodeRepository $otpCodeRepository)
    {
        $this->mainRepository = $mainRepository;
        $this->userRepository = $userRepository;
        $this->otpCodeRepository = $otpCodeRepository;
        $this->setOtp();
    }

    /**
     * Send an OTP (One-Time Password) code to the specified email address.
     *
     * @param string $email The recipient's email address
     * @param string $otpCodeType The type of OTP code being sent
     * @return \Illuminate\Http\JsonResponse|\App\Models\OtpCode Returns the created OTP code object or an error response
     */
    public function sendOtpCode($email, $otpCodeType): EmailServiceImplement|\App\Models\OtpCode
    {

        try {
            DB::beginTransaction();
            
            $oldOtpCode = $this->otpCodeRepository->findCodeByEmailAndType($email, $otpCodeType);

            if ($oldOtpCode) {
                $this->otpCodeRepository->delete($oldOtpCode->id);
            }

            $otpCode = $this->otpCodeRepository->create([
                'email' => $email,
                'code' => $this->otp,
                'type' => $otpCodeType,
                'expired_at' => now()->addMinutes(5),
            ]);

            Mail::to($otpCode->email)->send(new OtpCodeMail($otpCode));

            DB::commit();
            return $otpCode;
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->setCode(500)
                ->setMessage("Send OTP Code Failed")
                ->setError($e->getMessage());
        }
    }

    /**
     * Set the value of otp
     *
     * @return  self
     */ 
    public function setOtp()
    {
        $otp = mt_rand(10000, 99999);
        $this->otp = app()->environment([
            strtolower(ServerEnum::LOCAL->name), 
            strtolower(ServerEnum::STAGING->name), 
            strtolower(ServerEnum::DEVELOPMENT->name), 
            strtolower(ServerEnum::TESTING->name)
            ]) ? '111111' : $otp;
        return $this;
    }
}
