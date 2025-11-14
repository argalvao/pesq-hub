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

    public function profile()
    {
        $user = Session::get('user');
        
        try {
            // Buscar dados necessários para o perfil
            $cursos = $this->databaseService->getCursos();
            $areas = $this->databaseService->getAreasPesquisa();
            
            // Buscar dados completos do usuário (incluindo os novos campos)
            $userCompleto = $this->databaseService->getUserById($user['id']);
            
            return view('estudante.profile', compact('userCompleto', 'cursos', 'areas'));
        } catch (\Exception $e) {
            // Garantir que a view tenha os dados mínimos esperados
            $userFallback = is_array($user) ? $user : [];
            $userCompleto = array_merge([
                'nome' => '',
                'email' => '',
                'id_curso' => '',
                'periodo' => '',
                'telefone' => '',
                'lattes' => '',
                'biografia' => '',
                'areas_interesse_ids' => []
            ], $userFallback);

            return view('estudante.profile', [
                'userCompleto' => $userCompleto,
                'cursos' => [],
                'areas' => []
            ])->with('error', 'Erro ao carregar dados do perfil: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Session::get('user');
        
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'id_curso' => 'nullable|string|exists:curso,id',
            'periodo' => 'nullable|integer|min:1|max:10',
            'lattes' => 'nullable|url|max:500',
            'biografia' => 'nullable|string|max:500',
            'areas_interesse_ids' => 'nullable|array',
            'areas_interesse_ids.*' => 'string|exists:area_pesquisa,id'
        ]);

        try {
            $data = $request->only([
                'nome', 
                'email', 
                'telefone', 
                'id_curso', 
                'periodo',
                'lattes',
                'biografia',
                'areas_interesse_ids'
            ]);

            // Atualizar usuário no banco de dados
            $updatedUser = $this->databaseService->updateUser($user['id'], $data);
            
            // Atualizar dados na sessão
            $newUserSession = array_merge($user, [
                'nome' => $data['nome'],
                'email' => $data['email'],
                'id_curso' => $data['id_curso'],
                'telefone' => $data['telefone'] ?? null,
                'periodo' => $data['periodo'] ?? null,
                'lattes' => $data['lattes'] ?? null,
                'biografia' => $data['biografia'] ?? null,
                'areas_interesse_ids' => $data['areas_interesse_ids'] ?? []
            ]);
            
            Session::put('user', $newUserSession);
            
            return redirect()->route('basico.profile')->with('success', 'Perfil atualizado com sucesso!');
            
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }
}
