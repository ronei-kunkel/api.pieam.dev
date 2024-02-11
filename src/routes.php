<?php

use Api\Auth\Infra\Controller\GoogleAuthController;
use Api\Auth\Infra\Controller\RevokeAuthController;
use Api\Auth\Infra\Middleware\GoogleBodyValidator;
use Api\Auth\Infra\Middleware\GoogleJwtValidator;
use Api\Common\Infra\Middleware\CsrfValidation;
use Api\Common\Infra\Middleware\SessionValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/google', GoogleAuthController::class)->middleware(GoogleBodyValidator::class, GoogleJwtValidator::class, StartSession::class);

    Route::delete('auth', RevokeAuthController::class)->middleware(CsrfValidation::class, SessionValidation::class);

    Route::get('session', fn() => new JsonResponse())->middleware(CsrfValidation::class, SessionValidation::class);
});