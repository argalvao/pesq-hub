<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\UserService;

class CheckUserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $level
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $level = null)
    {
        $user = Session::get('user');

        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        if (!$user['ativo']) {
            Session::forget('user');
            return redirect()->route('login')->with('error', 'Sua conta está desativada.');
        }

        // Se não foi especificado nível, apenas verifica se está logado
        if (!$level) {
            return $next($request);
        }

        $userService = app(UserService::class);

        switch ($level) {
            case 'admin':
                if (!$userService->canAccessAdmin($user)) {
                    return redirect()->route('home')->with('error', 'Acesso negado. Você não tem permissão de administrador.');
                }
                break;

            case 'professor':
                if (!$userService->canAccessProfessor($user)) {
                    return redirect()->route('home')->with('error', 'Acesso negado. Você não tem permissão de organizador.');
                }
                break;

            case 'estudante':
                if (!$userService->canAccessEstudante($user)) {
                    return redirect()->route('home')->with('error', 'Acesso negado.');
                }
                break;

            default:
                return redirect()->route('home')->with('error', 'Nível de acesso inválido.');
        }

        return $next($request);
    }
}
