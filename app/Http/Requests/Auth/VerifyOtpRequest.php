<?php

namespace App\Http\Requests\Auth;

use App\Enums\OTPTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'otp_code' => 'required|numeric|digits:6',
            'type' => [
                'required',
                Rule::in([
                    OTPTypeEnum::VERIFY_EMAIL_OTP->name,
                    OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name,
                    OTPTypeEnum::VERIFY_2FA_AUTHORIZATION_OTP->name,
                    OTPTypeEnum::RESET_PASSWORD_OTP->name,
                ])
            ],
        ];
    }
}