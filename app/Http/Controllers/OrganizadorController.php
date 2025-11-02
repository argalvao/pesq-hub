<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;

class OrganizadorController extends Controller
{
    protected $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    public function dashboard()
    {
        try {
            $professores = $this->googleSheetsService->getProfessores();
            $linhasPesquisa = $this->googleSheetsService->getLinhasPesquisa();

            return view('organizador.dashboard', compact('professores', 'linhasPesquisa'));
        } catch (\Exception $e) {
            return view('organizador.dashboard')->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    // Professores
    public function getProfessores()
    {
        try {
            $professores = $this->googleSheetsService->getProfessores();
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
            $professor = $this->googleSheetsService->createProfessor($request->all());
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
            $professor = $this->googleSheetsService->updateProfessor($id, $request->all());
            return response()->json(['data' => $professor, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyProfessor($id)
    {
        try {
            $this->googleSheetsService->deleteProfessor($id);
            return response()->json(['success' => true, 'message' => 'Professor excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    // Linhas de Pesquisa
    public function getLinhasPesquisa()
    {
        try {
            $linhas = $this->googleSheetsService->getLinhasPesquisa();
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
            $linha = $this->googleSheetsService->createLinhaPesquisa($request->all());
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
            $linha = $this->googleSheetsService->updateLinhaPesquisa($id, $request->all());
            return response()->json(['data' => $linha, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyLinhaPesquisa($id)
    {
        try {
            $this->googleSheetsService->deleteLinhaPesquisa($id);
            return response()->json(['success' => true, 'message' => 'Linha de pesquisa excluída com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }
}
