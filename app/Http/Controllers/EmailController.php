<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\EmailService;
use Illuminate\Validation\ValidationException;
use Exception;

class EmailController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Enviar e-mail usando template
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request): JsonResponse
    {
        try {
            // Validação dos parâmetros
            $validatedData = $request->validate([
                'destinatario' => 'required|email',
                'template' => 'required|string|min:3',
                'assunto' => 'nullable|string|max:200',
                'dados' => 'nullable|array',
                'remetente' => 'nullable|email',
                'nome_remetente' => 'nullable|string|max:100'
            ]);

            // Verificar se template existe
            if (!$this->emailService->templateExiste($validatedData['template'])) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Template não encontrado: ' . $validatedData['template'],
                    'templates_disponiveis' => $this->emailService->listarTemplates()
                ], 404);
            }

            // Enviar e-mail usando o service
            $resultado = $this->emailService->enviarEmail(
                $validatedData['destinatario'],
                $validatedData['template'],
                $validatedData['dados'] ?? [],
                $validatedData['assunto'] ?? null,
                $validatedData['remetente'] ?? 'abel@ecomp.uefs.br',
                $validatedData['nome_remetente'] ?? 'PesqHub - UEFS'
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
                'message' => '❌ Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar e-mail de contato com organizador
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendContactProfessor(Request $request): JsonResponse
    {
        try {
            // Validação específica para contato com organizador
            $validatedData = $request->validate([
                'email_professor' => 'required|email',
                'nome_professor' => 'required|string|max:100',
                'nome_estudante' => 'required|string|max:100',
                'email_estudante' => 'required|email',
                'mensagem' => 'required|string|min:5|max:2000',
                'curso_estudante' => 'nullable|string|max:100',
                'assunto' => 'nullable|string|max:200'
            ]);

            // Usar o método específico do EmailService
            $resultado = $this->emailService->enviarContatoProfessor(
                $validatedData['email_professor'],
                $validatedData['nome_professor'],
                $validatedData['nome_estudante'],
                $validatedData['email_estudante'],
                $validatedData['mensagem'],
                $validatedData['curso_estudante'] ?? null,
                $validatedData['assunto'] ?? null
            );

            $statusCode = $resultado['success'] ? 200 : 500;
            return response()->json($resultado, $statusCode);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Dados inválidos para contato com organizador',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
}
