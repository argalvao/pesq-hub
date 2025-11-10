<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TokenConfirmacaoService
{
    /**
     * Tempo de expiração do token em minutos
     */
    private const EXPIRACAO_MINUTOS = 5;

    /**
     * Máximo de tentativas permitidas
     */
    private const MAX_TENTATIVAS = 3;

    /**
     * Gerar um token numérico de 6 dígitos
     */
    public function gerarToken(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Armazenar token e dados do usuário no cache
     */
    public function armazenarToken(string $email, string $token, array $dadosUsuario): bool
    {
        try {
            $chave = $this->gerarChaveCache($email);
            
            $dados = [
                'token' => $token,
                'dados_usuario' => $dadosUsuario,
                'tentativas' => 0,
                'criado_em' => now()->toDateTimeString()
            ];

            Cache::put($chave, $dados, now()->addMinutes(self::EXPIRACAO_MINUTOS));
            
            Log::info("Token armazenado para: {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao armazenar token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar se o token é válido
     */
    public function verificarToken(string $email, string $token): array
    {
        try {
            $chave = $this->gerarChaveCache($email);
            $dados = Cache::get($chave);

            if (!$dados) {
                return [
                    'success' => false,
                    'message' => 'Token expirado ou não encontrado. Solicite um novo código.'
                ];
            }

            // Verificar número de tentativas
            if ($dados['tentativas'] >= self::MAX_TENTATIVAS) {
                Cache::forget($chave);
                return [
                    'success' => false,
                    'message' => 'Número máximo de tentativas excedido. Solicite um novo código.'
                ];
            }

            // Incrementar tentativas
            $dados['tentativas']++;
            Cache::put($chave, $dados, now()->addMinutes(self::EXPIRACAO_MINUTOS));

            // Verificar token
            if ($dados['token'] !== $token) {
                $tentativasRestantes = self::MAX_TENTATIVAS - $dados['tentativas'];
                return [
                    'success' => false,
                    'message' => "Código inválido. Você tem {$tentativasRestantes} tentativa(s) restante(s)."
                ];
            }

            // Token válido - limpar cache e retornar dados do usuário
            Cache::forget($chave);
            
            return [
                'success' => true,
                'message' => 'E-mail confirmado com sucesso!',
                'dados_usuario' => $dados['dados_usuario']
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao verificar token: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao verificar código. Tente novamente.'
            ];
        }
    }

    /**
     * Reenviar token (gera novo token)
     */
    public function reenviarToken(string $email): array
    {
        try {
            $chave = $this->gerarChaveCache($email);
            $dados = Cache::get($chave);

            if (!$dados) {
                return [
                    'success' => false,
                    'message' => 'Sessão expirada. Reinicie o processo de cadastro.'
                ];
            }

            // Gerar novo token
            $novoToken = $this->gerarToken();
            $dados['token'] = $novoToken;
            $dados['tentativas'] = 0;
            $dados['criado_em'] = now()->toDateTimeString();

            Cache::put($chave, $dados, now()->addMinutes(self::EXPIRACAO_MINUTOS));

            return [
                'success' => true,
                'message' => 'Novo código enviado com sucesso!',
                'token' => $novoToken
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao reenviar token: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao reenviar código. Tente novamente.'
            ];
        }
    }

    /**
     * Cancelar processo de confirmação
     */
    public function cancelarConfirmacao(string $email): bool
    {
        try {
            $chave = $this->gerarChaveCache($email);
            Cache::forget($chave);
            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao cancelar confirmação: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Consultar status do token
     */
    public function consultarStatus(string $email): array
    {
        try {
            $chave = $this->gerarChaveCache($email);
            $dados = Cache::get($chave);

            if (!$dados) {
                return [
                    'existe' => false,
                    'message' => 'Nenhum processo de confirmação encontrado.'
                ];
            }

            $expiraEm = now()->addMinutes(self::EXPIRACAO_MINUTOS)->diffForHumans();

            return [
                'existe' => true,
                'tentativas_restantes' => self::MAX_TENTATIVAS - $dados['tentativas'],
                'criado_em' => $dados['criado_em'],
                'expira_em' => $expiraEm
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao consultar status: " . $e->getMessage());
            return [
                'existe' => false,
                'message' => 'Erro ao consultar status.'
            ];
        }
    }

    /**
     * Gerar chave única para o cache
     */
    private function gerarChaveCache(string $email): string
    {
        return 'token_confirmacao_' . md5(strtolower(trim($email)));
    }
}
