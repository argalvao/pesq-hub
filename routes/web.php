<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganizadorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AboutController;

// Rotas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/data', [HomeController::class, 'getData'])->name('api.data');
Route::get('/sobre', [AboutController::class, 'index'])->name('sobre');

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rota para verificação de e-mail (pública)
Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');

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

    // === NOVAS ROTAS PARA O DASHBOARD ===
    // Rotas para carregar dados para os modais
    Route::get('/cursos', [AdminController::class, 'getCursos'])->name('cursos.index');
    Route::get('/areas-pesquisa', [AdminController::class, 'getAreasPesquisa'])->name('areas.index');
    Route::get('/usuarios', [AdminController::class, 'getUsuarios'])->name('usuarios.index');
    // =====================================

    // API routes para gerenciamento de usuários
    Route::put('/usuarios/desativar/{id}', [AdminController::class, 'desativarUsuario'])->name('usuarios.update');
    Route::put('/usuarios/ativar/{id}', [AdminController::class, 'ativarUsuario'])->name('usuarios.update');

});

// Rotas de organizador (protegidas por user.level:organizador middleware)
Route::middleware('user.level:organizador')->prefix('organizador')->name('organizador.')->group(function () {
    Route::get('/dashboard', [OrganizadorController::class, 'dashboard'])->name('dashboard');
    Route::post('/profile', [OrganizadorController::class, 'updateProfile'])->name('profile.update');

    // API routes para Áreas de Pesquisa (GET já existe)
    Route::get('/areas-pesquisa', [OrganizadorController::class, 'getAreasPesquisa'])->name('areas.index');
    Route::post('/areas-pesquisa', [OrganizadorController::class, 'storeAreaPesquisa'])->name('areas.store');
    Route::put('/areas-pesquisa/{id}', [OrganizadorController::class, 'updateAreaPesquisa'])->name('areas.update');
    Route::delete('/areas-pesquisa/{id}', [OrganizadorController::class, 'destroyAreaPesquisa'])->name('areas.destroy');

    // API routes para Professores
    Route::get('/professores', [OrganizadorController::class, 'getProfessores'])->name('professores.index');
    Route::post('/professores', [OrganizadorController::class, 'storeProfessor'])->name('professores.store');
    Route::put('/professores/{id}', [OrganizadorController::class, 'updateProfessor'])->name('professores.update');
    Route::delete('/professores/{id}', [OrganizadorController::class, 'destroyProfessor'])->name('professores.destroy');

    // API routes para Linhas de Pesquisa
    Route::get('/linhas-pesquisa', [OrganizadorController::class, 'getLinhasPesquisa'])->name('linhas.index');
    Route::post('/linhas-pesquisa', [OrganizadorController::class, 'storeLinhaPesquisa'])->name('linhas.store');
    Route::put('/linhas-pesquisa/{id}', [OrganizadorController::class, 'updateLinhaPesquisa'])->name('linhas.update');
    Route::delete('/linhas-pesquisa/{id}', [OrganizadorController::class, 'destroyLinhaPesquisa'])->name('linhas.destroy');

    // API route para Cursos (necessária para o modal de Professor)
    Route::get('/cursos', [OrganizadorController::class, 'getCursos'])->name('cursos.index');
});

// Rotas de estudante (protegidas por user.level:basico middleware)
Route::middleware('user.level:basico')->prefix('basico')->name('basico.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\EstudanteController::class, 'dashboard'])->name('dashboard');
    Route::get('/favorites', [App\Http\Controllers\EstudanteController::class, 'favorites'])->name('favorites');
    Route::get('/profile', [App\Http\Controllers\EstudanteController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\EstudanteController::class, 'updateProfile'])->name('profile.update');
});

// Rotas de e-mail
Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');

// Rota pública para contato com professor (sem necessidade de login)
Route::post('/contact-professor', [EmailController::class, 'sendContactProfessor'])->name('contact.professor');

// Rotas para confirmação de cadastro por token
Route::post('/token/enviar', [App\Http\Controllers\TokenConfirmacaoController::class, 'enviarToken'])->name('token.enviar');
Route::post('/token/verificar', [App\Http\Controllers\TokenConfirmacaoController::class, 'verificarToken'])->name('token.verificar');
Route::get('/token/consultar', [App\Http\Controllers\TokenConfirmacaoController::class, 'consultarToken'])->name('token.consultar');
Route::delete('/token/cancelar', [App\Http\Controllers\TokenConfirmacaoController::class, 'cancelarToken'])->name('token.cancelar');

// Rotas para cadastro com confirmação por token
Route::post('/cadastro/solicitar', [App\Http\Controllers\CadastroComConfirmacaoController::class, 'solicitarCadastro'])->name('cadastro.solicitar');
Route::post('/cadastro/confirmar', [App\Http\Controllers\CadastroComConfirmacaoController::class, 'confirmarCadastro'])->name('cadastro.confirmar');
Route::post('/cadastro/reenviar-token', [App\Http\Controllers\CadastroComConfirmacaoController::class, 'reenviarToken'])->name('cadastro.reenviar');
Route::delete('/cadastro/cancelar', [App\Http\Controllers\CadastroComConfirmacaoController::class, 'cancelarCadastro'])->name('cadastro.cancelar');
// Rota pública para contato com organizador (sem necessidade de login)
Route::post('/contact-organizador', [EmailController::class, 'sendContactProfessor'])->name('contact.organizador');
