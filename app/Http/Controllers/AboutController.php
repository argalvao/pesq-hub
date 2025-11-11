<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseService;

class AboutController extends Controller
{
    protected $dbService;

    public function __construct(DatabaseService $dbService)
    {
        $this->dbService = $dbService;
    }

    public function index()
    {
        $stats = [
            'professores' => $this->getProfessoresCount(),
            'linhas_pesquisa' => $this->getLinhasPesquisaCount(),
            'areas_pesquisa' => $this->getAreasPesquisaCount(),
            'usuarios_ativos' => $this->getUsuariosAtivosCount(),
        ];

        return view('about.index', compact('stats'));
    }

    private function getProfessoresCount()
    {
        try {
            $professores = $this->dbService->getProfessores();
            return count($professores);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getLinhasPesquisaCount()
    {
        try {
            $linhas = $this->dbService->getLinhasPesquisa();
            return count($linhas);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getAreasPesquisaCount()
    {
        try {
            $areas = $this->dbService->getAreasPesquisa();
            return count($areas);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getUsuariosAtivosCount()
    {
        try {
            $usuarios = $this->dbService->getUsers();
            return count(array_filter($usuarios, function($usuario) {
                return $usuario['ativo'] == 1 || $usuario['ativo'] === true;
            }));
        } catch (\Exception $e) {
            return 0;
        }
    }
}
