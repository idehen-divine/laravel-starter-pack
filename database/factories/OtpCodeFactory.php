<?php

namespace Database\Factories;

use App\Enums\OTPTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OtpCode>
 */
class OtpCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'code' => $this->faker->numberBetween(100000, 999999),
            'type' => $this->faker->randomElement([OTPTypeEnum::cases()]),
            'expired_at' => now()->addMinutes(5),
        ];
    }
}
