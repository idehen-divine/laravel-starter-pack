<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;

Route::get('/', function () {
    return redirect(url('/docs'));
});

Route::get('/health-check', [HomeController::class, 'healthCheck'])->name('health-check');