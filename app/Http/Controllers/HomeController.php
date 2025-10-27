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
}
