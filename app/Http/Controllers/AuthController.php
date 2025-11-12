<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseService;
use App\Services\TokenConfirmacaoService;

class AuthController extends Controller
{
    protected $databaseService;
    protected $tokenService;

    public function __construct(DatabaseService $databaseService, TokenConfirmacaoService $tokenService)
    {
        $this->databaseService = $databaseService;
        $this->tokenService = $tokenService;
    }

    public function showLogin()
    {
        if (Session::has('user')) {
            return $this->redirectBasedOnLevel(Session::get('user'));
        }
        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $user = $this->databaseService->getUserByEmail($request->email);

            if ($user && Hash::check($request->password, $user['senha'])) {
                if (!$user['ativo']) {
                    if ($request->ajax() || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Sua conta está desativada. Entre em contato com o administrador.'
                        ], 422);
                    }
                    return back()->withErrors([
                        'email' => 'Sua conta está desativada. Entre em contato com o administrador.'
                    ]);
                }

                // Salvar usuário na sessão
                Session::put('user', $user);

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => $this->getRedirectUrl($user)
                    ]);
                }

                return $this->redirectBasedOnLevel($user);
            }
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao validar credenciais. Tente novamente.'
                ], 500);
            }
            return back()->withErrors([
                'email' => 'Erro ao validar credenciais. Tente novamente.'
            ]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado ou credenciais incorretas.'
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Usuário não encontrado ou credenciais incorretas.'
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout realizado com sucesso.');
    }

    public function showRegister()
    {
        $cursos = $this->databaseService->getCursos();
        return view('auth.register', [
            'cursos' => $cursos
        ]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $existingUser = $this->databaseService->getUserByEmail($request->email);
            
            return response()->json([
                'exists' => $existingUser !== null,
                'message' => $existingUser ? 'E-mail já cadastrado no sistema' : 'E-mail disponível'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Erro ao verificar e-mail'
            ], 500);
        }
    }

    public function checkPhone(Request $request)
    {
        $request->validate([
            'telefone' => 'required|string',
            'professor_id' => 'nullable|uuid'
        ]);

        try {
            // Limpa o telefone para comparação (remove formatação)
            $cleanPhone = preg_replace('/\D/', '', $request->telefone);
            
            $existingProfessor = $this->databaseService->getProfessorByPhone($cleanPhone, $request->professor_id);
            
            return response()->json([
                'exists' => $existingProfessor !== null,
                'message' => $existingProfessor ? 'Telefone já cadastrado para outro professor' : 'Telefone disponível'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Erro ao verificar telefone'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'nivel_permissao' => 'required|in:2,3' // Só organizador ou estudante
        ]);

        try {
            // Verificar se o e-mail já existe antes de tentar criar
            $existingUser = $this->databaseService->getUserByEmail($request->email);
            if ($existingUser) {
                return back()->withErrors([
                    'email' => 'Este e-mail já está cadastrado no sistema.'
                ])->withInput();
            }

            // Salvar dados do cadastro na sessão temporariamente
            Session::put('pending_registration', [
                'nome' => $request->name,
                'email' => $request->email,
                'senha' => $request->password,
                'tipo_permissao' => (
                    $request->nivel_permissao == 2
                    ? DatabaseService::NIVEL_ORGANIZADOR
                    : DatabaseService::NIVEL_BASICO
                ),
                'ativo' => true, // Após a confirmação do e-mail, o usuário é marcado como ativo
                'id_curso' => $request->id_curso ?? null
            ]);

            // Enviar token de confirmação
            $resultado = $this->tokenService->enviarTokenConfirmacao(
                $request->email,
                $request->name,
                $request->nivel_permissao == 2 ? 'organizador' : 'estudante'
            );

            if ($resultado['success']) {
                // Salvar email na sessão e redirecionar para tela de confirmação
                Session::put('email', $request->email);
                return redirect()->route('confirm.token')
                    ->with('success', 'Código de confirmação enviado para seu e-mail!');
            } else {
                return back()->withErrors([
                    'email' => 'Erro ao enviar código de confirmação. Tente novamente.'
                ])->withInput();
            }

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage()
            ])->withInput();
        }
    }

    public function confirmToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6|regex:/^[0-9]{6}$/'
        ]);

        try {
            // Verificar token
            $resultado = $this->tokenService->verificarToken(
                $request->email,
                $request->token
            );

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'] ?? '❌ Token inválido'
                ], 400);
            }

            // Buscar dados pendentes na sessão
            $pendingData = Session::get('pending_registration');
            
            if (!$pendingData || $pendingData['email'] !== $request->email) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Dados de cadastro não encontrados. Tente se cadastrar novamente.'
                ], 400);
            }

            // Criar usuário no banco
            $user = $this->databaseService->createUser($pendingData);
            
            // Limpar dados pendentes
            Session::forget(['pending_registration', 'email']);
            
            // Fazer login automático
            Session::put('user', $user);
            
            return response()->json([
                'success' => true,
                'message' => '✅ E-mail confirmado! Conta criada com sucesso.',
                'redirect' => $this->getRedirectUrl($user)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Erro ao confirmar token: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRedirectUrl($user): string
    {
        switch ($user['tipo_permissao']) {
            case DatabaseService::NIVEL_ADMIN:
                return route('admin.dashboard');
            case DatabaseService::NIVEL_ORGANIZADOR:
                return route('organizador.dashboard');
            case DatabaseService::NIVEL_BASICO:
                return route('basico.dashboard');
            default:
                return route('home');
        }
    }

    private function redirectBasedOnLevel($user)
    {
        switch ($user['tipo_permissao']) {
            case DatabaseService::NIVEL_ADMIN:
                return redirect()->route('admin.dashboard');
            case DatabaseService::NIVEL_ORGANIZADOR:
                return redirect()->route('organizador.dashboard');
            case DatabaseService::NIVEL_BASICO:
                return redirect()->route('basico.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}
