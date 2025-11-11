<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TokenConfirmacaoController;
use App\Http\Controllers\CadastroComConfirmacaoController;

// Rotas de recuperação de senha (sem CSRF)
Route::post('/password/send-token', [PasswordResetController::class, 'sendToken'])->name('api.password.send-token');
Route::post('/password/update', [PasswordResetController::class, 'updatePassword'])->name('api.password.update');

// Rotas para confirmação de cadastro por token (sem CSRF)
Route::post('/token/enviar', [TokenConfirmacaoController::class, 'enviarToken'])->name('api.token.enviar');
Route::post('/token/verificar', [TokenConfirmacaoController::class, 'verificarToken'])->name('api.token.verificar');
Route::get('/token/consultar', [TokenConfirmacaoController::class, 'consultarToken'])->name('api.token.consultar');
Route::delete('/token/cancelar', [TokenConfirmacaoController::class, 'cancelarToken'])->name('api.token.cancelar');

// Rotas para cadastro com confirmação por token (sem CSRF)
Route::post('/cadastro/solicitar', [CadastroComConfirmacaoController::class, 'solicitarCadastro'])->name('api.cadastro.solicitar');
Route::post('/cadastro/confirmar', [CadastroComConfirmacaoController::class, 'confirmarCadastro'])->name('api.cadastro.confirmar');
Route::post('/cadastro/reenviar-token', [CadastroComConfirmacaoController::class, 'reenviarToken'])->name('api.cadastro.reenviar');
Route::delete('/cadastro/cancelar', [CadastroComConfirmacaoController::class, 'cancelarCadastro'])->name('api.cadastro.cancelar');
