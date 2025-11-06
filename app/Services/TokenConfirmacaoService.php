<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Services\EmailService;

class TokenConfirmacaoService
{
    protected EmailService $emailService;
    protected int $tempoExpiracao = 300; 

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     *
     * @param string $email 
     * @param string $nome 
     * @param string|null $tipo 
     * @return array
     */
    public function enviarTokenConfirmacao(string $email, string $nome, ?string $tipo = null): array
    {
        try {
            $token = $this->emailService->gerarToken();
            
            $chaveCache = $this->gerarChaveCache($email);
            $dadosToken = [
                'token' => $token,
                'email' => $email,
                'nome' => $nome,
                'tipo' => $tipo,
                'tentativas' => 0,
                'criado_em' => time()
            ];
            
            Cache::put($chaveCache, $dadosToken, $this->tempoExpiracao);
            
            $resultadoEmail = $this->emailService->enviarConfirmacaoCadastro($email, $nome, $token, $tipo);
            
            if ($resultadoEmail['success']) {
                return [
                    'success' => true,
                    'message' => ' Token de confirmação enviado com sucesso!',
                    'email' => $email,
                    'expira_em' => $this->tempoExpiracao,
                    'timestamp' => time()
                ];
            } else {
                Cache::forget($chaveCache);
                return $resultadoEmail;
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => ' Erro ao enviar token: ' . $e->getMessage(),
                'timestamp' => time()
            ];
        }
    }

    /**
     *
     * @param string $email 
     * @param string $token 
     * @return array
     */
    public function verificarToken(string $email, string $token): array
    {
        try {
            $chaveCache = $this->gerarChaveCache($email);
            $dadosToken = Cache::get($chaveCache);
            
            if (!$dadosToken) {
                return [
                    'success' => false,
                    'message' => 'Token expirado ou inválido',
                    'codigo' => 'TOKEN_EXPIRADO'
                ];
            }
            
            $dadosToken['tentativas']++;
            
            if ($dadosToken['tentativas'] > 3) {
                Cache::forget($chaveCache);
                return [
                    'success' => false,
                    'message' => 'Muitas tentativas incorretas. Solicite um novo token.',
                    'codigo' => 'LIMITE_TENTATIVAS'
                ];
            }
            
            if ($dadosToken['token'] !== $token) {
                Cache::put($chaveCache, $dadosToken, $this->tempoExpiracao);
                
                $tentativasRestantes = 3 - $dadosToken['tentativas'];
                return [
                    'success' => false,
                    'message' => " Token incorreto. Tentativas restantes: {$tentativasRestantes}",
                    'codigo' => 'TOKEN_INCORRETO',
                    'tentativas_restantes' => $tentativasRestantes
                ];
            }
            
            Cache::forget($chaveCache);
            
            return [
                'success' => true,
                'message' => 'Token confirmado com sucesso!',
                'email' => $dadosToken['email'],
                'nome' => $dadosToken['nome'],
                'tipo' => $dadosToken['tipo'],
                'codigo' => 'TOKEN_CONFIRMADO'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => ' Erro ao verificar token: ' . $e->getMessage(),
                'codigo' => 'ERRO_INTERNO'
            ];
        }
    }

    /**
     * @param string $email
     * @return array
     */
    public function consultarToken(string $email): array
    {
        $chaveCache = $this->gerarChaveCache($email);
        $dadosToken = Cache::get($chaveCache);
        
        if (!$dadosToken) {
            return [
                'existe' => false,
                'message' => 'Nenhum token ativo encontrado'
            ];
        }
        
        $tempoRestante = $this->tempoExpiracao - (time() - $dadosToken['criado_em']);
        
        return [
            'existe' => true,
            'email' => $dadosToken['email'],
            'tentativas_usadas' => $dadosToken['tentativas'],
            'tentativas_restantes' => 3 - $dadosToken['tentativas'],
            'tempo_restante_segundos' => max(0, $tempoRestante),
            'tempo_restante_formatado' => $this->formatarTempo($tempoRestante),
            'criado_em' => date('d/m/Y H:i:s', $dadosToken['criado_em'])
        ];
    }

    /**
     *
     * @param string $email
     * @return bool
     */
    public function cancelarToken(string $email): bool
    {
        $chaveCache = $this->gerarChaveCache($email);
        return Cache::forget($chaveCache);
    }

    /**
     *
     * @param string $email
     * @return string
     */
    private function gerarChaveCache(string $email): string
    {
        return 'token_confirmacao_' . md5(strtolower($email));
    }

    /**
     *
     * @param int $segundos
     * @return string
     */
    private function formatarTempo(int $segundos): string
    {
        if ($segundos <= 0) {
            return 'Expirado';
        }
        
        $minutos = intval($segundos / 60);
        $segundos = $segundos % 60;
        
        if ($minutos > 0) {
            return "{$minutos}min {$segundos}s";
        }
        
        return "{$segundos}s";
    }

    /**
     *
     * @return int 
     */
    public function limparTokensExpirados(): int
    {
        return 0;
    }
}
