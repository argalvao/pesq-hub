<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseService;

class AuthController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
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

            $user = $this->databaseService->createUser([
                'nome' => $request->name,
                'email' => $request->email,
                'senha' => $request->password,
                'tipo_permissao' => (
                    $request->nivel_permissao == 2
                    ? DatabaseService::NIVEL_ORGANIZADOR
                    : DatabaseService::NIVEL_BASICO
                ),

                'ativo' => !($request->nivel_permissao == 2),
                'id_curso' => $request->id_curso ?? null
            ]);

            Session::put('user', $user);

            return $this->redirectBasedOnLevel($user)
                ->with('success', 'Conta criada com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage()
            ])->withInput();
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

    private function getRedirectUrl($user)
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
}
