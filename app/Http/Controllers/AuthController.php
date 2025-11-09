<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\UsuarioService;
use App\Models\Usuario;

class AuthController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function showLogin()
    {
        if (Session::has('user')) {
            return $this->redirectBasedOnLevel(Session::get('user'));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $user = $this->usuarioService->findUserByEmail($request->email);

            if ($user && Hash::check($request->password, $user['senha'])) {
                if (!$user['ativo']) {
                    return back()->withErrors([
                        'email' => 'Sua conta está desativada. Entre em contato com o administrador.'
                    ]);
                }

                // Salvar usuário na sessão
                Session::put('user', $user);

                return $this->redirectBasedOnLevel($user);
            }
        } catch (\Exception $e) {
            Log::error('Erro no login: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Erro ao validar credenciais. Tente novamente.'
            ]);
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.'
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
        return view('auth.register');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $existingUser = $this->usuarioService->findUserByEmail($request->email);
            
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
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'senha' => 'required|string|min:6|confirmed',
            'tipo_permissao' => 'required|in:DA,BASICO' // Só organizador ou estudante
        ]);

        try {
            // Verificar se o e-mail já existe antes de tentar criar
            $existingUser = $this->usuarioService->findUserByEmail($request->email);
            if ($existingUser) {
                return back()->withErrors([
                    'email' => 'Este e-mail já está cadastrado no sistema.'
                ])->withInput();
            }

            $user = $this->usuarioService->createUser([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => $request->senha,
                'tipo_permissao' => $request->tipo_permissao,
                'ativo' => !($request->tipo_permissao == UsuarioService::NIVEL_ORGANIZADOR), // Organizador precisa aprovação
            ]);

            Session::put('user', $user);

            return $this->redirectBasedOnLevel($user)->with('success', 'Conta criada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro no registro: ' . $e->getMessage());
            return back()->withErrors([
                'email' => $e->getMessage()
            ])->withInput();
        }
    }

    private function redirectBasedOnLevel($user)
    {
        switch ($user['tipo_permissao']) {
            case UsuarioService::NIVEL_ADMIN:
                return redirect()->route('admin.dashboard');
            case UsuarioService::NIVEL_ORGANIZADOR:
                return redirect()->route('organizador.dashboard');
            case UsuarioService::NIVEL_BASICO:
                return redirect()->route('basico.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}
