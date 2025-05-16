<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::apiResource automatycznie ustawia prefix /api (konfigurowane w bootstrap/app.php:contentReference[oaicite:20]{index=20})
Route::apiResource('users', UserController::class);

// Niestandardowy endpoint do logowania powitania
Route::post('users/{user}/welcome', [UserController::class, 'sendWelcomeEmails']);
