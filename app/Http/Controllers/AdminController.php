<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseService;

class AdminController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function dashboard()
    {
        try {
            $professores = $this->databaseService->getProfessores();
            $linhasPesquisa = $this->databaseService->getLinhasPesquisa();

            return view('admin.dashboard', compact('professores', 'linhasPesquisa'));
        } catch (\Exception $e) {
            return view('admin.dashboard')->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    // Professores
    public function getProfessores()
    {
        try {
            $professores = $this->databaseService->getProfessores();
            return response()->json(['data' => $professores, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function storeProfessor(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'telefone' => 'required|string',
            'curso' => 'required|string',
            'areas_interesse' => 'array',
            'linhas_pesquisa_ids' => 'array'
        ]);

        try {
            $professor = $this->databaseService->createProfessor($request->all());
            return response()->json(['data' => $professor, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function updateProfessor(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'telefone' => 'required|string',
            'curso' => 'required|string',
            'areas_interesse' => 'array',
            'linhas_pesquisa_ids' => 'array'
        ]);

        try {
            $professor = $this->databaseService->updateProfessor($id, $request->all());
            return response()->json(['data' => $professor, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyProfessor($id)
    {
        try {
            $this->databaseService->deleteProfessor($id);
            return response()->json(['success' => true, 'message' => 'Professor excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    // Linhas de Pesquisa
    public function getLinhasPesquisa()
    {
        try {
            $linhas = $this->databaseService->getLinhasPesquisa();
            return response()->json(['data' => $linhas, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function storeLinhaPesquisa(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string'
        ]);

        try {
            $linha = $this->databaseService->createLinhaPesquisa($request->all());
            return response()->json(['data' => $linha, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function updateLinhaPesquisa(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string'
        ]);

        try {
            $linha = $this->databaseService->updateLinhaPesquisa($id, $request->all());
            return response()->json(['data' => $linha, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyLinhaPesquisa($id)
    {
        try {
            $this->databaseService->deleteLinhaPesquisa($id);
            return response()->json(['success' => true, 'message' => 'Linha de pesquisa excluída com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function desativarUsuario($id)
    {
        try {
            $this->databaseService->desativarUsuario($id);
            return response()->json(['success' => true, 'message' => 'Usuário desativado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function ativarUsuario($id)
    {
        try {
            $this->databaseService->ativarUsuario($id);
            return response()->json(['success' => true, 'message' => 'Usuário ativado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }
}
