<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;

// Rotas de recuperação de senha (sem CSRF)
Route::post('/password/send-token', [PasswordResetController::class, 'sendToken'])->name('api.password.send-token');
Route::post('/password/update', [PasswordResetController::class, 'updatePassword'])->name('api.password.update');
