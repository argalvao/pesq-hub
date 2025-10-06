<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;

class HomeController extends Controller
{
    protected $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    public function index()
    {
        return view('home');
    }

    public function getData()
    {
        try {
            $professores = $this->googleSheetsService->getProfessores();
            $linhasPesquisa = $this->googleSheetsService->getLinhasPesquisa();

            return response()->json([
                'professores' => $professores,
                'linhas_pesquisa' => $linhasPesquisa,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar dados: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'professor_id' => 'required|integer'
        ]);

        try {
            // Aqui você pode implementar o envio de email
            // Por enquanto, vamos apenas simular o sucesso
            
            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage()
            ], 500);
        }
    }
}
