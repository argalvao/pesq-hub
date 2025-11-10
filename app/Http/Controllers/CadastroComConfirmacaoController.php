<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TokenConfirmacaoService;
use App\Services\EmailService;
use App\Services\UsuarioService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CadastroComConfirmacaoController extends Controller
{
    protected $tokenService;
    protected $emailService;
    protected $usuarioService;

    public function __construct(
        TokenConfirmacaoService $tokenService,
        EmailService $emailService,
        UsuarioService $usuarioService
    ) {
        $this->tokenService = $tokenService;
        $this->emailService = $emailService;
        $this->usuarioService = $usuarioService;
    }

    /**
     * Etapa 1: Solicitar cadastro (gera e envia token)
     */
    public function solicitarCadastro(Request $request)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'nome' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'senha' => 'required|string|min:6',
                'senha_confirmation' => 'required|same:senha',
                'tipo_permissao' => 'required|in:DA,BASICO'
            ], [
                'nome.required' => 'O nome é obrigatório',
                'email.required' => 'O e-mail é obrigatório',
                'email.email' => 'E-mail inválido',
                'senha.required' => 'A senha é obrigatória',
                'senha.min' => 'A senha deve ter no mínimo 6 caracteres',
                'senha_confirmation.same' => 'As senhas não coincidem',
                'tipo_permissao.required' => 'Selecione o tipo de usuário',
                'tipo_permissao.in' => 'Tipo de usuário inválido'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $dados = $request->all();

            // Verificar se o e-mail já está cadastrado
            $usuarioExistente = $this->usuarioService->buscarPorEmail($dados['email']);
            if ($usuarioExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este e-mail já está cadastrado no sistema.'
                ], 422);
            }

            // Gerar token
            $token = $this->tokenService->gerarToken();

            // Armazenar token e dados temporariamente
            $dadosUsuario = [
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => $dados['senha'],
                'tipo_permissao' => $dados['tipo_permissao']
            ];

            $this->tokenService->armazenarToken($dados['email'], $token, $dadosUsuario);

            // Enviar e-mail com token
            $emailEnviado = $this->emailService->enviarTokenConfirmacao(
                $dados['email'],
                $dados['nome'],
                $token
            );

            if (!$emailEnviado) {
                Log::warning("Falha ao enviar e-mail para: {$dados['email']}. Token: {$token}");
            }

            Log::info("Token gerado para {$dados['email']}: {$token}");

            return response()->json([
                'success' => true,
                'message' => 'Código de confirmação enviado para seu e-mail. Verifique sua caixa de entrada.',
                'email' => $dados['email']
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao solicitar cadastro: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Etapa 2: Confirmar token e criar usuário
     */
    public function confirmarCadastro(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required|string|size:6|regex:/^[0-9]{6}$/'
            ], [
                'email.required' => 'E-mail é obrigatório',
                'token.required' => 'Código é obrigatório',
                'token.size' => 'O código deve ter 6 dígitos',
                'token.regex' => 'O código deve conter apenas números'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Verificar token
            $resultado = $this->tokenService->verificarToken(
                $request->email,
                $request->token
            );

            if (!$resultado['success']) {
                return response()->json($resultado, 422);
            }

            // Token válido - criar usuário
            $dadosUsuario = $resultado['dados_usuario'];
            $usuarioCriado = $this->usuarioService->criar($dadosUsuario);

            if (!$usuarioCriado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar usuário. Tente novamente.'
                ], 500);
            }

            Log::info("Usuário criado com sucesso: {$dadosUsuario['email']}");

            return response()->json([
                'success' => true,
                'message' => 'Cadastro confirmado com sucesso! Você já pode fazer login.',
                'user' => [
                    'nome' => $usuarioCriado['nome'],
                    'email' => $usuarioCriado['email']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao confirmar cadastro: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar cadastro. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Reenviar token
     */
    public function reenviarToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail inválido'
                ], 422);
            }

            $resultado = $this->tokenService->reenviarToken($request->email);

            if (!$resultado['success']) {
                return response()->json($resultado, 422);
            }

            // Enviar novo token por e-mail
            $chaveCache = 'token_confirmacao_' . md5(strtolower(trim($request->email)));
            $dados = cache()->get($chaveCache);
            
            if ($dados) {
                $this->emailService->enviarTokenConfirmacao(
                    $request->email,
                    $dados['dados_usuario']['nome'],
                    $resultado['token']
                );
            }

            Log::info("Token reenviado para: {$request->email}");

            return response()->json([
                'success' => true,
                'message' => 'Novo código enviado para seu e-mail.'
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao reenviar token: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reenviar código. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Cancelar processo de cadastro
     */
    public function cancelarCadastro(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail inválido'
                ], 422);
            }

            $this->tokenService->cancelarConfirmacao($request->email);

            return response()->json([
                'success' => true,
                'message' => 'Processo de cadastro cancelado.'
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao cancelar cadastro: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Consultar status do token
     */
    public function consultarStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail inválido'
                ], 422);
            }

            $status = $this->tokenService->consultarStatus($request->email);

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao consultar status: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar status.'
            ], 500);
        }
    }
}
