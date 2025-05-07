<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\OtpCode;
use App\Enums\OTPTypeEnum;
use App\Services\OtpCode\OtpCodeService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use App\Repositories\OtpCode\OtpCodeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_verify_email_otp_verification()
    {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setRole(RoleEnum::USER->name);
        $otpCode = app(OtpCodeService::class)->sendOtpCode($user->email, OTPTypeEnum::VERIFY_EMAIL_OTP->name);

        $response = $this->postJson(route('auth.verify-otp'), [
            'email' => $user->email,
            'otp_code' => $otpCode->code,
            'type' => OTPTypeEnum::VERIFY_EMAIL_OTP->name
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'user_name',
                        'email',
                        'first_name',
                        'last_name',
                        'other_name',
                        'phone_no',
                        'avatar',
                        'status',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                    'permissions'
                ]
            ])
            ->assertJson([
                'code' => 200,
                'message' => 'Login Success',
            ]);

        $this->assertIsString($response->json('data.token'));
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_user_can_verify_2fa_authentication_otp_verification()
    {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setRole(RoleEnum::USER->name);
        $otpCode = app(OtpCodeService::class)->sendOtpCode($user->email, OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name);

        $response = $this->postJson(route('auth.verify-otp'), [
            'email' => $user->email,
            'otp_code' => $otpCode->code,
            'type' => OTPTypeEnum::VERIFY_2FA_AUTHENTICATION_OTP->name
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'user_name',
                        'email',
                        'first_name',
                        'last_name',
                        'other_name',
                        'phone_no',
                        'avatar',
                        'status',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                    'permissions'
                ]
            ])
            ->assertJson([
                'code' => 200,
                'message' => 'Login Success',
            ]);

        $this->assertIsString($response->json('data.token'));
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_user_can_verify_reset_password_otp()
    {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setRole(RoleEnum::USER->name);
        $otpCode = app(OtpCodeService::class)->sendOtpCode($user->email, OTPTypeEnum::RESET_PASSWORD_OTP->name);

        $response = $this->postJson(route('auth.verify-otp'), [
            'email' => $user->email,
            'otp_code' => $otpCode->code,
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'message' => 'OTP Code verified successfully.',
                'data' => [
                    'email' => $user->email
                ]
            ]);
        $this->assertDatabaseHas(OtpCode::class, [
            'email' => $user->email,
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name,
            'code' => '0'
        ]);
    }

    public function test_user_can_verify_reset_email_otp()
    {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setRole(RoleEnum::USER->name);
        $otpCode = app(OtpCodeService::class)->sendOtpCode($user->email, OTPTypeEnum::RESET_EMAIL_OTP->name);
        $user->tokens()->delete();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('user.verify-email-reset-otp'), [
                    'email' => $user->email,
                    'otp_code' => $otpCode->code,
                    'type' => OTPTypeEnum::RESET_EMAIL_OTP->name
                ]);

        $response->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'message' => 'OTP Code verified successfully.',
                'data' => [
                    'email' => $user->email
                ]
            ]);
    }

    public function test_user_can_resend_otp_code()
    {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setRole(RoleEnum::USER->name);

        // Loop through all OTP types
        foreach (OTPTypeEnum::cases() as $otpType) {
            // Send original OTP
            app(OtpCodeRepository::class)->findCodeByEmail($user->email)?->delete();
            app(OtpCodeService::class)->sendOtpCode($user->email, $otpType->name);

            // Resend OTP request
            $response = $this->postJson(route('auth.resend-otp-verification'), [
                'email' => $user->email,
                'type' => $otpType->name
            ]);

            // Assert response
            if ($response->status() !== 200) {
                dump("Failed for OTP type: $otpType->name", $response->json());
            }

            $response->assertStatus(200)
                ->assertJson([
                    'code' => 200,
                    'message' => 'OTP verification sent to email.',
                    'data' => [
                        'email' => $user->email,
                    ]
                ]);

            // Optionally assert the DB has the new OTP
            $this->assertDatabaseHas('otp_codes', [
                'email' => $user->email,
                'type' => $otpType->name,
                'is_verified' => false,
            ]);
        }
    }

}
