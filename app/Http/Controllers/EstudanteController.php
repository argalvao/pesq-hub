<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\DatabaseService;

class EstudanteController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function dashboard()
    {
        $user = Session::get('user');
        
        try {
            $professores = $this->databaseService->getProfessores();
            $linhasPesquisa = $this->databaseService->getLinhasPesquisa();
            
            return view('estudante.dashboard', compact('user', 'professores', 'linhasPesquisa'));
        } catch (\Exception $e) {
            return view('estudante.dashboard', compact('user'))->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    public function favorites()
    {
        $user = Session::get('user');
        // Aqui você pode implementar um sistema de favoritos se desejar
        // Por enquanto, apenas retorna a view
        
        return view('estudante.favorites', compact('user'));
    }
}
