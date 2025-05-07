<?php

namespace App\Mail;

use App\Models\User;
use App\Models\OtpCode;
use App\Enums\OTPTypeEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    public OtpCode $otpCode;
    public $user_name;
    public $otp;
    public $subject;
    public $view;

    /**
     * Create a new message instance.
     */
    public function __construct(OtpCode $otpCode)
    {
        $this->otpCode = $otpCode;
        $this->otp = $otpCode->code;
        $this->setUserName();
        $this->setSubject();
        $this->setView();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->view,
            with: [
                'user_name' => $this->user_name,
                'otp' => $this->otp,
            ],
        );
    }

    /**
     * Set the value of user_name.
     *
     * @return  self
     */ 
    public function setUserName()
    {
        $this->user_name = optional(User::where('email', $this->otpCode->email)->first())->user_name ?? 'User';
        return $this;
    }

    /**
     * Set the value of subject
     *
     * @return  self
     */
    public function setSubject()
    {
        $this->subject = match ($this->otpCode->type) {
            OTPTypeEnum::VERIFY_EMAIL_OTP->name => 'Damodi - Email Verification',
            OTPTypeEnum::RESET_EMAIL_OTP->name => 'Damodi - Email Change Verification',
            OTPTypeEnum::RESET_PASSWORD_OTP->name => 'Damodi - Reset Password Verification',
            OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name => 'Damodi - Two-Factor Authentication Verification',
            OTPTypeEnum::VERIFY_2FA_AUTHORIZATION_OTP->name => 'Damodi - Two-Factor Authorization Verification',
            default => 'Damodi - OTP Verification',
        };

        return $this;
    }


    /**
     * Set the value of view
     *
     * @return  self
     */ 
    public function setView()
    {
        $this->view = match ($this->otpCode->type) {
            OTPTypeEnum::VERIFY_EMAIL_OTP->name => 'emails.verify-email-otp-mail',
            OTPTypeEnum::RESET_EMAIL_OTP->name => 'emails.reset-email-otp-mail',
            OTPTypeEnum::RESET_PASSWORD_OTP->name => 'emails.reset-password-otp-mail',
            OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name => 'emails.verify-2fa-otp-mail',
            OTPTypeEnum::VERIFY_2FA_AUTHORIZATION_OTP->name => 'emails.verify-2fa-otp-mail',
            default => 'emails.otp-mail',
        };

        return $this;
    }
}
