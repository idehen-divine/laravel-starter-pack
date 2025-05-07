<?php

namespace App\Providers;

use App\Enums\OTPMethodEnum;
use Illuminate\Support\ServiceProvider;
use App\Services\OtpCode\OtpCodeService;
use App\Services\OtpCode\EmailServiceImplement;

class OtpCodeProvider extends ServiceProvider
{
    protected $implementations = [
        OTPMethodEnum::EMAIL->name => EmailServiceImplement::class,
        // OTPMethodEnum::SMS->name => SmsServiceImplement::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OtpCodeService::class, function ($app) {
                $method = request()->input('method') ? request()->input('method') : OTPMethodEnum::EMAIL->name;

            if (!isset($this->implementations[$method])) {
                throw new \InvalidArgumentException("Invalid otp provider method selected");
            }

            return $app->make($this->implementations[$method]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
