<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendOTPVerificationRequest extends FormRequest
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
            'email' => 'required|email|exists:otp_codes,email',
            'type' => 'required|in:' . implode(',', array_map(fn($case) => $case->name, \App\Enums\OTPTypeEnum::cases())),
            'method' => 'nullable|in:' . implode(',', array_map(fn($case) => $case->name, \App\Enums\OTPMethodEnum::cases())),
        ];
    }
}
