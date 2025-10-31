<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
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
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $user = $this->databaseService->getUserByEmail($request->email);
            
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

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'nivel_permissao' => 'required|in:2,3' // Só professor ou estudante
        ]);

        try {
            $user = $this->databaseService->createUser([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'nivel_permissao' => $request->nivel_permissao,
                'ativo' => 1
            ]);

            Session::put('user', $user);
            
            return $this->redirectBasedOnLevel($user)->with('success', 'Conta criada com sucesso!');
            
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage()
            ])->withInput();
        }
    }

    private function redirectBasedOnLevel($user)
    {
        switch ($user['nivel_permissao']) {
            case DatabaseService::NIVEL_ADMIN:
                return redirect()->route('admin.dashboard');
            case DatabaseService::NIVEL_PROFESSOR:
                return redirect()->route('professor.dashboard');
            case DatabaseService::NIVEL_ESTUDANTE:
                return redirect()->route('estudante.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}
