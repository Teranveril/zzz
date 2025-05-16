<?php

use App\Http\Controllers\API\UserController;

Route::apiResource('users', UserController::class)->except(['index', 'show']);
Route::post('/users/{user}/send-welcome-emails', [UserController::class, 'sendWelcomeEmails']);
