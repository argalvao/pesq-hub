<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseService;
use App\Services\OrganizadorService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrganizadorController extends Controller
{
    protected $databaseService;

    // Assumindo que você já injeta o DatabaseService
    protected $organizadorService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
        $this->organizadorService = app(OrganizadorService::class);
    }

    // Método existente mantido como estava
    public function updateProfile(Request $request)
    {
        try {
            $professor = $this->databaseService->updateProfessorProfile(
                auth()->id(),
                $request->all()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso',
                'data' => $professor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    // Novo método para edição de perfil via Google Sheets
    public function editarPerfil(Request $request)
    {
        // Validação básica
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        try {
            $user = Session::get('user');

            // Atualiza os dados
            $this->organizadorService->atualizarDados(
                $user['id'], 
                $request->only(['nome', 'email'])
            );

            // Atualiza a sessão
            Session::put('user', array_merge($user, [
                'nome' => $request->nome,
                'email' => $request->email
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil'
            ], 500);
        }
    }

    public function dashboard()
    {
        // Esta view (ex: 'organizador.dashboard') DEVE existir
        // e é onde você colocará o componente na Etapa 3.
        return view('organizador.dashboard');
    }

    // =============== PROFESSORES ===============
    // (Idêntico ao AdminController)

    public function getProfessores()
    {
        try {
            $professores = $this->databaseService->getProfessores(); //
            return response()->json(['data' => $professores, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function storeProfessor(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:professor,email',
            'telefone' => 'nullable|string|max:20',
            'id_curso' => 'required|string|exists:curso,id',
            'departamento' => 'nullable|string|max:255',
            'areas_interesse_ids' => 'nullable|array',
            'areas_interesse_ids.*' => 'string|exists:area_pesquisa,id',
            'linhas_pesquisa_ids' => 'nullable|array',
            'linhas_pesquisa_ids.*' => 'string|exists:linha_pesquisa,id'
        ]);

        try {
            // Adiciona o 'criado_por' com o ID do organizador logado
            $data = $request->all();
            $data['criado_por'] = auth()->id();

            $professor = $this->databaseService->createProfessor($data); //
            return response()->json(['data' => $professor, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function updateProfessor(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('professor')->ignore($id)],
            'telefone' => 'nullable|string|max:20',
            'id_curso' => 'required|string|exists:curso,id',
            'departamento' => 'nullable|string|max:255',
            'areas_interesse_ids' => 'nullable|array',
            'areas_interesse_ids.*' => 'string|exists:area_pesquisa,id',
            'linhas_pesquisa_ids' => 'nullable|array',
            'linhas_pesquisa_ids.*' => 'string|exists:linha_pesquisa,id'
        ]);

        try {
            $professor = $this->databaseService->updateProfessor($id, $request->all()); //
            return response()->json(['data' => $professor, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyProfessor($id)
    {
        try {
            $this->databaseService->deleteProfessor($id); //
            return response()->json(['success' => true, 'message' => 'Professor excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    // =============== LINHAS DE PESQUISA ===============
    // (Idêntico ao AdminController)

    public function getLinhasPesquisa()
    {
        try {
            $linhas = $this->databaseService->getLinhasPesquisa(); //
            return response()->json(['data' => $linhas, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function storeLinhaPesquisa(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:linha_pesquisa,nome',
            'descricao' => 'nullable|string',
            'id_area_pesquisa' => 'required|string|exists:area_pesquisa,id'
        ]);

        try {
            // Adiciona o 'criado_por' com o ID do organizador logado
            $data = $request->all();
            $data['criado_por'] = auth()->id();

            $linha = $this->databaseService->createLinhaPesquisa($data); //
            return response()->json(['data' => $linha, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function updateLinhaPesquisa(Request $request, $id)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('linha_pesquisa')->ignore($id)],
            'descricao' => 'nullable|string',
            'id_area_pesquisa' => 'required|string|exists:area_pesquisa,id'
        ]);

        try {
            $linha = $this->databaseService->updateLinhaPesquisa($id, $request->all()); //
            return response()->json(['data' => $linha, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyLinhaPesquisa($id)
    {
        try {
            $this->databaseService->deleteLinhaPesquisa($id); //
            return response()->json(['success' => true, 'message' => 'Linha de pesquisa excluída com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    // =============== ÁREAS DE PESQUISA ===============
    // (Idêntico ao AdminController)

    public function getAreasPesquisa()
    {
        try {
            $areas = $this->databaseService->getAreasPesquisa(); //
            return response()->json(['data' => $areas, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function storeAreaPesquisa(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:area_pesquisa,nome',
            'descricao' => 'nullable|string',
        ]);

        try {
            // Adiciona o 'criado_por' com o ID do organizador logado
            $data = $request->all();
            $data['criado_por'] = auth()->id();

            $area = $this->databaseService->createAreaPesquisa($data); //
            return response()->json(['data' => $area, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function updateAreaPesquisa(Request $request, $id)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('area_pesquisa')->ignore($id)],
            'descricao' => 'nullable|string',
        ]);

        try {
            $area = $this->databaseService->updateAreaPesquisa($id, $request->all()); //
            return response()->json(['data' => $area, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function destroyAreaPesquisa($id)
    {
        try {
            $this->databaseService->deleteAreaPesquisa($id); //
            return response()->json(['success' => true, 'message' => 'Área de pesquisa excluída com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 409); // 409 Conflict
        }
    }

    // =============== CURSOS ===============
    // (Necessário para o modal de Professor)

    public function getCursos()
    {
        try {
            $cursos = $this->databaseService->getCursos(); //
            return response()->json(['data' => $cursos, 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }
}
