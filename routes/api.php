<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\HobbyController;
use App\Http\Middleware\TokenAuthMiddleware;
use App\Http\Middleware\RateLimiterMiddleware;

Route::post('/auth/token', [AuthController::class, 'issueToken']);

Route::middleware([TokenAuthMiddleware::class, RateLimiterMiddleware::class])->group(function () {
    Route::apiResource('users', UserController::class);

    Route::post('/users/{id}/relationships', [RelationshipController::class, 'store']);
    Route::delete('/users/{id}/relationships', [RelationshipController::class, 'destroy']);

    Route::post('/users/{id}/hobbies', [HobbyController::class, 'store']);
    Route::get('/metrics/reputation', [UserController::class, 'systemMetrics']);
});
