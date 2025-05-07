<?php

use App\Enums\OTPTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('code');
            $table->enum('type', [
                OTPTypeEnum::VERIFY_EMAIL_OTP->name,
                OTPTypeEnum::RESET_PASSWORD_OTP->name,
                OTPTypeEnum::RESET_EMAIL_OTP->name,
                OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name,
                OTPTypeEnum::VERIFY_2FA_AUTHORIZATION_OTP->name,
            ]);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
