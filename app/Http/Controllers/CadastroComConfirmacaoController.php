<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\TokenConfirmacaoService;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Hash;
use Exception;

class CadastroComConfirmacaoController extends Controller
{
    protected TokenConfirmacaoService $tokenService;
    protected DatabaseService $databaseService;

    public function __construct(TokenConfirmacaoService $tokenService, DatabaseService $databaseService)
    {
        $this->tokenService = $tokenService;
        $this->databaseService = $databaseService;
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function solicitarCadastro(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|min:2|max:100',
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:6|confirmed',
                'nivel_permissao' => 'required|in:2,3', 
            ]);

            // Verificar se e-mail já existe
            $usuarioExistente = $this->databaseService->getUserByEmail($validatedData['email']);
            if ($usuarioExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este e-mail já está cadastrado no sistema',
                    'codigo' => 'EMAIL_JA_EXISTE'
                ], 422);
            }

            $tipoUsuario = $validatedData['nivel_permissao'] == 2 ? 'professor' : 'estudante';

            $dadosTemporarios = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'nivel_permissao' => $validatedData['nivel_permissao'],
                'tipo' => $tipoUsuario
            ];

            $resultado = $this->tokenService->enviarTokenConfirmacao(
                $validatedData['email'],
                $validatedData['name'],
                $tipoUsuario
            );

            if ($resultado['success']) {
                $chaveCadastro = 'cadastro_temp_' . md5(strtolower($validatedData['email']));
                cache()->put($chaveCadastro, $dadosTemporarios, 300); 

                return response()->json([
                    'success' => true,
                    'message' => 'Token de confirmação enviado! Verifique seu e-mail.',
                    'email' => $validatedData['email'],
                    'proxima_etapa' => 'confirmar_token',
                    'expira_em' => 300
                ], 200);
            } else {
                return response()->json($resultado, 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ' Dados inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmarCadastro(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255',
                'token' => 'required|string|size:6|regex:/^[0-9]{6}$/'
            ]);

            $verificacaoToken = $this->tokenService->verificarToken(
                $validatedData['email'],
                $validatedData['token']
            );

            if (!$verificacaoToken['success']) {
                return response()->json($verificacaoToken, 400);
            }

            $chaveCadastro = 'cadastro_temp_' . md5(strtolower($validatedData['email']));
            $dadosTemporarios = cache()->get($chaveCadastro);

            if (!$dadosTemporarios) {
                return response()->json([
                    'success' => false,
                    'message' => ' Dados de cadastro expirados. Inicie o processo novamente.',
                    'codigo' => 'DADOS_EXPIRADOS'
                ], 400);
            }

            $usuarioExistente = $this->databaseService->getUserByEmail($validatedData['email']);
            if ($usuarioExistente) {
                cache()->forget($chaveCadastro);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Este e-mail já foi cadastrado por outro processo',
                    'codigo' => 'EMAIL_JA_EXISTE'
                ], 422);
            }

            $userData = [
                'name' => $dadosTemporarios['name'],
                'email' => $dadosTemporarios['email'],
                'password' => $dadosTemporarios['password'], 
                'nivel_permissao' => $dadosTemporarios['nivel_permissao'],
                'ativo' => 1
            ];

            $novoUsuario = $this->databaseService->createUser($userData);

            cache()->forget($chaveCadastro);

            return response()->json([
                'success' => true,
                'message' => 'Cadastro confirmado com sucesso! Você já pode fazer login.',
                'usuario' => [
                    'id' => $novoUsuario['id'],
                    'name' => $novoUsuario['name'],
                    'email' => $novoUsuario['email'],
                    'tipo' => $dadosTemporarios['tipo']
                ],
                'codigo' => 'CADASTRO_CONFIRMADO'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
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
     * Reenviar token para um cadastro pendente
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reenviarToken(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255'
            ]);

            $chaveCadastro = 'cadastro_temp_' . md5(strtolower($validatedData['email']));
            $dadosTemporarios = cache()->get($chaveCadastro);

            if (!$dadosTemporarios) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum cadastro pendente encontrado para este e-mail',
                    'codigo' => 'CADASTRO_NAO_PENDENTE'
                ], 404);
            }

            $this->tokenService->cancelarToken($validatedData['email']);

            $resultado = $this->tokenService->enviarTokenConfirmacao(
                $dadosTemporarios['email'],
                $dadosTemporarios['name'],
                $dadosTemporarios['tipo']
            );

            if ($resultado['success']) {
                cache()->put($chaveCadastro, $dadosTemporarios, 300);

                return response()->json([
                    'success' => true,
                    'message' => ' Novo token enviado! Verifique seu e-mail.',
                    'email' => $validatedData['email']
                ], 200);
            } else {
                return response()->json($resultado, 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'E-mail inválido',
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelarCadastro(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255'
            ]);

            $this->tokenService->cancelarToken($validatedData['email']);

            $chaveCadastro = 'cadastro_temp_' . md5(strtolower($validatedData['email']));
            cache()->forget($chaveCadastro);

            return response()->json([
                'success' => true,
                'message' => '✅ Processo de cadastro cancelado com sucesso',
                'codigo' => 'CADASTRO_CANCELADO'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
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
