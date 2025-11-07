<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CadastroComConfirmacaoController;

// Rotas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/data', [HomeController::class, 'getData'])->name('api.data');

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas de cadastro com confirmação por token
Route::post('/cadastro/solicitar', [CadastroComConfirmacaoController::class, 'solicitarCadastro'])->name('cadastro.solicitar');
Route::post('/cadastro/confirmar', [CadastroComConfirmacaoController::class, 'confirmarCadastro'])->name('cadastro.confirmar');
Route::post('/cadastro/reenviar-token', [CadastroComConfirmacaoController::class, 'reenviarToken'])->name('cadastro.reenviar');
Route::delete('/cadastro/cancelar', [CadastroComConfirmacaoController::class, 'cancelarCadastro'])->name('cadastro.cancelar');
Route::get('/cadastro/status', [CadastroComConfirmacaoController::class, 'consultarStatus'])->name('cadastro.status');

// Rotas de admin (protegidas por user.level:admin middleware)
Route::middleware('user.level:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // API routes para professores
    Route::get('/professores', [AdminController::class, 'getProfessores'])->name('professores.index');
    Route::post('/professores', [AdminController::class, 'storeProfessor'])->name('professores.store');
    Route::put('/professores/{id}', [AdminController::class, 'updateProfessor'])->name('professores.update');
    Route::delete('/professores/{id}', [AdminController::class, 'destroyProfessor'])->name('professores.destroy');
    
    // API routes para linhas de pesquisa
    Route::get('/linhas-pesquisa', [AdminController::class, 'getLinhasPesquisa'])->name('linhas.index');
    Route::post('/linhas-pesquisa', [AdminController::class, 'storeLinhaPesquisa'])->name('linhas.store');
    Route::put('/linhas-pesquisa/{id}', [AdminController::class, 'updateLinhaPesquisa'])->name('linhas.update');
    Route::delete('/linhas-pesquisa/{id}', [AdminController::class, 'destroyLinhaPesquisa'])->name('linhas.destroy');
});

// Rotas de professor (protegidas por user.level:professor middleware)
Route::middleware('user.level:professor')->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\ProfessorController::class, 'dashboard'])->name('dashboard');
    Route::post('/profile', [App\Http\Controllers\ProfessorController::class, 'updateProfile'])->name('profile.update');
});

// Rotas de estudante (protegidas por user.level:estudante middleware)
Route::middleware('user.level:estudante')->prefix('estudante')->name('estudante.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\EstudanteController::class, 'dashboard'])->name('dashboard');
    Route::get('/favorites', [App\Http\Controllers\EstudanteController::class, 'favorites'])->name('favorites');
});

// Rotas de e-mail
Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');

// Rota pública para contato com professor (sem necessidade de login)
Route::post('/contact-professor', [EmailController::class, 'sendContactProfessor'])->name('contact.professor');
