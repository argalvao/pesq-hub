<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseService;

class HomeController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function index()
    {
        return view('home');
    }

    public function getData()
    {
        try {
            $professores = $this->databaseService->getProfessores();
            $linhasPesquisa = $this->databaseService->getLinhasPesquisa();

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
