<?php

use Api\Auth\Infra\GoogleAuthController;
use Api\Auth\Infra\Middleware\HasGoogleCsrfCookie;
use Api\Auth\Infra\Middleware\GoogleBodyValidator;
use Api\Auth\Infra\Middleware\GoogleCsrfValidator;
use Api\Auth\Infra\Middleware\GoogleJwtValidator;
use Api\Auth\Infra\RevokeAuthController;
use Api\Script\GsiClientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    // fixed script of google sign in client to set cookie both on domain and subdomain
    Route::prefix('gsi')->group(function () {
        Route::get('client', GsiClientController::class);
    });

    Route::prefix('auth')->group(function () {
        Route::post(
            'google',
            GoogleAuthController::class
        )->middleware(
            HasGoogleCsrfCookie::class,
            GoogleBodyValidator::class,
            GoogleCsrfValidator::class,
            GoogleJwtValidator::class
        );
        // Route::post('revoke', RevokeAuthController::class);
    });
});
