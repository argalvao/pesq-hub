<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\TokenConfirmacaoService;
use Illuminate\Validation\ValidationException;
use Exception;

class TokenConfirmacaoController extends Controller
{
    protected TokenConfirmacaoService $tokenService;

    public function __construct(TokenConfirmacaoService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Enviar token de confirmação por e-mail
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function enviarToken(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255',
                'nome' => 'required|string|min:2|max:100',
                'tipo' => 'nullable|string|in:admin,professor,estudante'
            ]);

            $resultado = $this->tokenService->enviarTokenConfirmacao(
                $validatedData['email'],
                $validatedData['nome'],
                $validatedData['tipo'] ?? null
            );

            $statusCode = $resultado['success'] ? 200 : 500;
            return response()->json($resultado, $statusCode);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Dados inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ' Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar token de confirmação
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verificarToken(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255',
                'token' => 'required|string|size:6|regex:/^[0-9]{6}$/'
            ]);

            $resultado = $this->tokenService->verificarToken(
                $validatedData['email'],
                $validatedData['token']
            );

            if ($resultado['success']) {
                // Verificar se há dados de registro pendente na sessão
                $pendingData = session('pending_registration');
                
                if ($pendingData && $pendingData['email'] === $validatedData['email']) {
                    // Criar usuário no banco
                    $databaseService = app(\App\Services\DatabaseService::class);
                    $user = $databaseService->createUser($pendingData);
                    
                    // Limpar dados pendentes e fazer login
                    session()->forget(['pending_registration', 'email']);
                    session()->put('user', $user);
                    
                    return response()->json([
                        'success' => true,
                        'message' => ' E-mail confirmado! Conta criada com sucesso.',
                        'redirect' => $this->getRedirectUrl($user)
                    ], 200);
                }
            }

            $statusCode = $resultado['success'] ? 200 : 400;
            return response()->json($resultado, $statusCode);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ' Dados inválidos para verificação',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ' Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRedirectUrl($user): string
    {
        $databaseService = app(\App\Services\DatabaseService::class);
        
        switch ($user['tipo_permissao']) {
            case $databaseService::NIVEL_ADMIN:
                return route('admin.dashboard');
            case $databaseService::NIVEL_ORGANIZADOR:
                return route('organizador.dashboard');
            case $databaseService::NIVEL_BASICO:
                return route('basico.dashboard');
            default:
                return route('home');
        }
    }

    /**
     * Consultar status do token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function consultarToken(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255'
            ]);

            $resultado = $this->tokenService->consultarToken($validatedData['email']);
            
            return response()->json([
                'success' => true,
                'data' => $resultado
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ' E-mail inválido',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar token (remover do cache)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelarToken(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255'
            ]);

            $removido = $this->tokenService->cancelarToken($validatedData['email']);
            
            return response()->json([
                'success' => true,
                'message' => $removido ? 'Token cancelado com sucesso' : 'Nenhum token ativo encontrado',
                'removido' => $removido
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ' E-mail inválido',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ' Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
}
