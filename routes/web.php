<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('users', UserController::class)->except(['index', 'show']);
Route::post('/users/{user}/send-welcome-emails', [UserController::class, 'sendWelcomeEmails']);
