<?php

use Api\Auth\Infra\Controller\GoogleAuthController;
use Api\Auth\Infra\Controller\RevokeAuthController;
use Api\Auth\Infra\Middleware\GoogleBodyValidator;
use Api\Auth\Infra\Middleware\GoogleJwtValidator;
use Api\Common\Infra\Middleware\CsrfRenew;
use Api\Common\Infra\Middleware\CsrfValidation;
use Api\Common\Infra\Middleware\SessionValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function() {
        Route::post('google', GoogleAuthController::class)->middleware(CsrfRenew::class, GoogleBodyValidator::class, GoogleJwtValidator::class, StartSession::class);
        Route::delete('', RevokeAuthController::class)->middleware(CsrfValidation::class);
    });

    Route::get('session', fn() => new JsonResponse())->middleware(SessionValidation::class);

});
