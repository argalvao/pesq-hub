<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
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
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        try {
            $email = $request->email;
            $token = $request->token;
            $newPassword = $request->password;

            // Verificar token no cache
            $cacheKey = 'password_reset_' . md5($email);
            $resetData = Cache::get($cacheKey);

            if (!$resetData) {
                return back()->withInput()->with('error', 'Token expirado ou inválido. Solicite um novo token.');
            }

            // Verificar tentativas
            if ($resetData['attempts'] >= 3) {
                Cache::forget($cacheKey);
                return back()->withInput()->with('error', 'Muitas tentativas inválidas. Solicite um novo token.');
            }

            // Verificar se o token está correto
            if ($resetData['token'] !== $token) {
                $resetData['attempts']++;
                Cache::put($cacheKey, $resetData, 900);
                
                return back()->withInput()->with('error', 'Token inválido. Tentativas restantes: ' . (3 - $resetData['attempts']));
            }

            // Verificar se o usuário ainda existe
            $user = $this->databaseService->getUserByEmail($email);
            if (!$user) {
                return back()->withInput()->with('error', 'Usuário não encontrado.');
            }

            // Atualizar senha
            $this->databaseService->updateUser($user['id'], [
                'senha' => $newPassword
            ]);

            // Remover token do cache
            Cache::forget($cacheKey);

            return redirect()->route('login')->with('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');

        } catch (\Exception $e) {
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
            $resetUrl = route('password.reset') . '?email=' . urlencode($email);
            
            $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #4F46E5;'>Recuperação de Senha - PesqHub</h2>
                    
                    <p>Olá <strong>{$nome}</strong>,</p>
                    
                    <p>Você solicitou a recuperação de sua senha. Use o token abaixo para redefinir sua senha:</p>
                    
                    <div style='background-color: #F3F4F6; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                        <h1 style='color: #1F2937; font-size: 36px; letter-spacing: 8px; margin: 0;'>{$token}</h1>
                        <p style='color: #6B7280; margin: 10px 0 0 0;'>Token de 6 dígitos</p>
                    </div>
                    
                    <p>Ou clique no botão abaixo para ir diretamente à tela de redefinição:</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetUrl}' 
                           style='background-color: #4F46E5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            Redefinir Senha
                        </a>
                    </div>
                    
                    <div style='background-color: #FEF2F2; border: 1px solid #FECACA; border-radius: 6px; padding: 15px; margin: 20px 0;'>
                        <p style='color: #DC2626; margin: 0; font-size: 14px;'>
                            <strong>⚠️ Importante:</strong><br>
                            • Este token expira em 15 minutos<br>
                            • Você tem 3 tentativas para usar o token<br>
                            • Se você não solicitou esta recuperação, ignore este e-mail
                        </p>
                    </div>
                    
                    <hr style='border: none; border-top: 1px solid #E5E7EB; margin: 30px 0;'>
                    
                    <p style='color: #6B7280; font-size: 12px; text-align: center;'>
                        Este e-mail foi enviado pelo sistema PesqHub.<br>
                        Se você não conseguir clicar no botão, copie e cole este link no seu navegador:<br>
                        <a href='{$resetUrl}' style='color: #4F46E5;'>{$resetUrl}</a>
                    </p>
                </div>
            ";

            return $this->emailService->sendEmail($email, $subject, $message);

        } catch (\Exception $e) {
            return false;
        }
    }
}
