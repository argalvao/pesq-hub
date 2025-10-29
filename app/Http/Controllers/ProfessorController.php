<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\DatabaseService;

class ProfessorController extends Controller
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
            // Buscar o professor no banco baseado no email do usuário
            $professores = $this->databaseService->getProfessores();
            $professor = collect($professores)->firstWhere('email', $user['email']);
            
            $linhasPesquisa = $this->databaseService->getLinhasPesquisa();
            
            return view('professor.dashboard', compact('user', 'professor', 'linhasPesquisa'));
        } catch (\Exception $e) {
            return view('professor.dashboard', compact('user'))->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string',
            'curso' => 'required|string',
            'areas_interesse' => 'string|nullable',
            'linhas_pesquisa_ids' => 'array'
        ]);

        $user = Session::get('user');
        
        try {
            $professores = $this->googleSheetsService->getProfessores();
            $professor = collect($professores)->firstWhere('email', $user['email']);
            
            if ($professor) {
                // Atualizar professor existente
                $this->googleSheetsService->updateProfessor($professor['id'], [
                    'nome' => $request->nome,
                    'email' => $user['email'],
                    'telefone' => $request->telefone,
                    'curso' => $request->curso,
                    'areas_interesse' => $request->areas_interesse ? explode(',', $request->areas_interesse) : [],
                    'linhas_pesquisa_ids' => $request->linhas_pesquisa_ids ?? []
                ]);
            } else {
                // Criar novo professor
                $this->googleSheetsService->createProfessor([
                    'nome' => $request->nome,
                    'email' => $user['email'],
                    'telefone' => $request->telefone,
                    'curso' => $request->curso,
                    'areas_interesse' => $request->areas_interesse ? explode(',', $request->areas_interesse) : [],
                    'linhas_pesquisa_ids' => $request->linhas_pesquisa_ids ?? []
                ]);
            }

            return redirect()->route('professor.dashboard')->with('success', 'Perfil atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }
}
