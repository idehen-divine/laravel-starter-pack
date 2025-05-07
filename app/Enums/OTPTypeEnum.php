<?php

namespace App\Enums;

enum OTPTypeEnum
{
    case VERIFY_EMAIL_OTP;
    case RESET_PASSWORD_OTP;
    case RESET_EMAIL_OTP;
    case VERIFY_2FA_AUTHENTICATION_OTP;
    case VERIFY_2FA_AUTHORIZATION_OTP;
}
