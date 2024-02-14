<?php

use Illuminate\Support\Facades\Route;

use Api\Auth\Infra\Middleware\GoogleBodyValidator;
use Api\Auth\Infra\Middleware\GoogleJwtValidator;
use Api\_Common\Infra\Middleware\CsrfRenew;
use Api\_Common\Infra\Middleware\CsrfValidation;
use Api\_Common\Infra\Middleware\SessionNeeded;

use Api\Auth\Infra\Controller\GoogleAuthController;
use Api\Auth\Infra\Controller\RevokeAuthController;
use Api\User\Infra\Controller\InfoController as UserInfoController;


Route::prefix('auth')->group(function () {
    Route::post('google', GoogleAuthController::class)->middleware(CsrfRenew::class, GoogleBodyValidator::class, GoogleJwtValidator::class);
    Route::delete('', RevokeAuthController::class)->middleware(SessionNeeded::class);
});

Route::middleware(SessionNeeded::class)->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('info', UserInfoController::class);
    });

});
