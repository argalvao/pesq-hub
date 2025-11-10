<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseService;
use App\Services\EmailService;

class PasswordResetController extends Controller
{
    protected $databaseService;
    protected $emailService;

    public function __construct(DatabaseService $databaseService, EmailService $emailService)
    {
        $this->databaseService = $databaseService;
        $this->emailService = $emailService;
    }

    /**
     * Enviar token de recuperação por email
     */
    public function sendToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $email = $request->email;
            
            // Verificar se o usuário existe
            $user = $this->databaseService->getUserByEmail($email);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail não encontrado em nossos registros.'
                ], 404);
            }

            // Gerar token de 6 dígitos
            $token = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Armazenar token no cache por 15 minutos
            $cacheKey = 'password_reset_' . md5($email);
            Cache::put($cacheKey, [
                'token' => $token,
                'email' => $email,
                'attempts' => 0,
                'created_at' => now()
            ], 900); // 15 minutos

            // Enviar email com token
            $emailSent = $this->sendResetEmail($email, $user['nome'], $token);

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token de recuperação enviado para seu e-mail. Verifique sua caixa de entrada.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao enviar e-mail. Tente novamente.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Processar redefinição de senha
     */
    public function updatePassword(Request $request)
    {
        Log::info('updatePassword: Headers recebidos', [
            'content-type' => $request->header('content-type'),
            'accept' => $request->header('accept'),
            'method' => $request->method(),
            'user-agent' => $request->header('user-agent')
        ]);
        Log::info('updatePassword iniciado', ['request' => $request->all()]);
        
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        try {
            $email = $request->email;
            $token = $request->token;
            $newPassword = $request->password;

            Log::info('Dados extraídos', ['email' => $email, 'token' => $token]);

            // Verificar token no cache
            $cacheKey = 'password_reset_' . md5($email);
            $resetData = Cache::get($cacheKey);

            Log::info('Cache lido', ['cacheKey' => $cacheKey, 'resetData' => $resetData]);

            if (!$resetData) {
                Log::error('Cache não encontrado');
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Token expirado ou inválido. Solicite um novo token.'], 400);
                }
                return back()->withInput()->with('error', 'Token expirado ou inválido. Solicite um novo token.');
            }

            // Verificar tentativas
            Log::info('Verificando tentativas', ['attempts' => $resetData['attempts'], 'type' => gettype($resetData['attempts'])]);
            if ($resetData['attempts'] >= 3) {
                Cache::forget($cacheKey);
                Log::error('Muitas tentativas');
                
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Muitas tentativas inválidas. Solicite um novo token.'], 400);
                }
                return back()->withInput()->with('error', 'Muitas tentativas inválidas. Solicite um novo token.');
            }

            // Verificar se o token está correto
            Log::info('Comparando tokens', ['esperado' => $resetData['token'], 'recebido' => $token]);
            if ($resetData['token'] !== $token) {
                $resetData['attempts']++;
                Cache::put($cacheKey, $resetData, 900);
                Log::error('Token inválido, incrementando attempts', ['novo_attempts' => $resetData['attempts']]);
                
                $tentativasRestantes = 3 - $resetData['attempts'];
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => "Token inválido. Tentativas restantes: {$tentativasRestantes}"], 400);
                }
                return back()->withInput()->with('error', "Token inválido. Tentativas restantes: {$tentativasRestantes}");
            }

            Log::info('Token válido, buscando usuário');

            // Verificar se o usuário ainda existe
            $user = $this->databaseService->getUserByEmail($email);
            if (!$user) {
                Log::error('Usuário não encontrado');
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
                }
                return back()->withInput()->with('error', 'Usuário não encontrado.');
            }

            Log::info('Atualizando senha do usuário', ['user_id' => $user['id']]);

            // Atualizar senha
            $this->databaseService->updateUser($user['id'], [
                'senha' => $newPassword
            ]);

            Log::info('Senha atualizada com sucesso');

            // Remover token do cache
            Cache::forget($cacheKey);

            Log::info('Verificando expectsJson', ['expectsJson' => $request->expectsJson(), 'accept_header' => $request->header('accept')]);

            // Para rotas API, sempre retornar JSON
            if ($request->is('api/*') || $request->expectsJson()) {
                $response = response()->json(['success' => true, 'message' => 'Senha redefinida com sucesso! Você pode fazer login com a nova senha.']);
                Log::info('Retornando resposta JSON', ['response' => $response->getContent()]);
                return $response;
            }
            Log::info('Retornando redirect porque não é API nem expects JSON');
            return redirect()->route('login')->with('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação', ['errors' => $e->errors()]);
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            Log::error("Erro geral no updatePassword", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao redefinir senha. Tente novamente.'], 500);
            }
            return back()->withInput()->with('error', 'Erro ao redefinir senha. Tente novamente.');
        }
    }

    /**
     * Enviar email de recuperação
     */
    private function sendResetEmail($email, $nome, $token)
    {
        try {
            $subject = 'PesqHub - Recuperação de Senha';
            
            $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #4F46E5;'>Recuperação de Senha - PesqHub</h2>
                    
                    <p>Olá <strong>{$nome}</strong>,</p>
                    
                    <p>Você solicitou a recuperação de sua senha. Use o token abaixo para redefinir sua senha:</p>
                    
                    <div style='background-color: #F3F4F6; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                        <h1 style='color: #1F2937; font-size: 36px; letter-spacing: 8px; margin: 0;'>{$token}</h1>
                        <p style='color: #6B7280; margin: 10px 0 0 0;'>Token de 6 dígitos</p>
                    </div>
                    
                    <p>Acesse o sistema PesqHub e use este token na tela de recuperação de senha.</p>
                    
                    <div style='background-color: #FEF2F2; border: 1px solid #FECACA; border-radius: 6px; padding: 15px; margin: 20px 0;'>
                        <p style='color: #DC2626; margin: 0; font-size: 14px;'>
                            <strong>⚠️ Importante:</strong><br>
                            • Este token expira em 15 minutos<br>
                            • Você tem 3 tentativas para usar o token<br>
                            • Se você não solicitou esta recuperação, ignore este e-mail<br>
                            • Use este token no sistema PesqHub para redefinir sua senha
                        </p>
                    </div>
                    
                    <hr style='border: none; border-top: 1px solid #E5E7EB; margin: 30px 0;'>
                    
                    <p style='color: #6B7280; font-size: 12px; text-align: center;'>
                        Este e-mail foi enviado pelo sistema PesqHub.<br>
                        Acesse <a href=\"http://localhost:8001\" style=\"color: #4F46E5;\">http://localhost:8001</a> e clique em \"Login\" para usar o token de recuperação.
                    </p>
                </div>
            ";

            return $this->emailService->sendEmail($email, $subject, $message);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de reset de senha', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
