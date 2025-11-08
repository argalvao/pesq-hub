<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseUserService;
use App\Models\User;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(DatabaseUserService $userService)
    {
        $this->userService = $userService;
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
            $user = $this->userService->findUserByEmail($request->email);

            if ($user && Hash::check($request->password, $user['password'])) {
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
            $existingUser = $this->userService->findUserByEmail($request->email);
            
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
            $existingUser = $this->userService->findUserByEmail($request->email);
            if ($existingUser) {
                return back()->withErrors([
                    'email' => 'Este e-mail já está cadastrado no sistema.'
                ])->withInput();
            }

            $user = $this->userService->createUser([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'nivel_permissao' => $request->nivel_permissao,
                'ativo' => !($request->nivel_permissao == 2), // Organizador precisa aprovação
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
        switch ($user['nivel_permissao']) {
            case 1: // ADMIN
                return redirect()->route('admin.dashboard');
            case 2: // ORGANIZADOR
                return redirect()->route('organizador.dashboard');
            case 3: // BASICO
                return redirect()->route('basico.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}
