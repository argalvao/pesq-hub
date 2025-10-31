<?php

namespace App\Services;

use App\Models\User;
use App\Models\Professor;
use App\Models\LinhaPesquisa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DatabaseService
{
    // Níveis de permissão (compatibilidade com UserService)
    const NIVEL_ADMIN = 1;
    const NIVEL_PROFESSOR = 2;
    const NIVEL_ESTUDANTE = 3;

    // =============== PROFESSORES ===============

    public function getProfessores()
    {
        return Cache::remember('professores_db', 300, function () {
            try {
                return Professor::with('linhasPesquisa')->get()->map(function ($professor) {
                    return [
                        'id' => $professor->id,
                        'nome' => $professor->nome,
                        'email' => $professor->email,
                        'telefone' => $professor->telefone,
                        'curso' => $professor->curso,
                        'areas' => $professor->areas_interesse_array,
                        'linhas_pesquisa' => $professor->linhasPesquisa->pluck('nome')->toArray(),
                        'linhas_pesquisa_ids' => $professor->linhasPesquisa->pluck('id')->toArray()
                    ];
                })->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar professores: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function createProfessor($data)
    {
        try {
            // Criar ou buscar usuário
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nome'],
                    'password' => Hash::make('professor123'), // senha padrão
                ]
            );

            // Criar professor
            $professor = Professor::create([
                'user_id' => $user->id,
                'nome' => $data['nome'],
                'email' => $data['email'],
                'telefone' => $data['telefone'] ?? null,
                'curso' => $data['curso'],
                'areas_interesse' => isset($data['areas_interesse']) 
                    ? (is_array($data['areas_interesse']) ? implode(',', $data['areas_interesse']) : $data['areas_interesse'])
                    : (isset($data['areas']) ? (is_array($data['areas']) ? implode(',', $data['areas']) : $data['areas']) : '')
            ]);

            // Associar linhas de pesquisa se fornecidas
            if (isset($data['linhas_pesquisa_ids']) && is_array($data['linhas_pesquisa_ids'])) {
                $professor->linhasPesquisa()->sync($data['linhas_pesquisa_ids']);
            }

            Cache::forget('professores_db');

            return [
                'id' => $professor->id,
                'nome' => $professor->nome,
                'email' => $professor->email,
                'telefone' => $professor->telefone,
                'curso' => $professor->curso,
                'areas' => $professor->areas_interesse_array,
                'linhas_pesquisa_ids' => $professor->linhasPesquisa->pluck('id')->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao criar professor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfessor($id, $data)
    {
        try {
            $professor = Professor::findOrFail($id);
            
            $professor->update([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'telefone' => $data['telefone'] ?? null,
                'curso' => $data['curso'],
                'areas_interesse' => isset($data['areas_interesse']) 
                    ? (is_array($data['areas_interesse']) ? implode(',', $data['areas_interesse']) : $data['areas_interesse'])
                    : (isset($data['areas']) ? (is_array($data['areas']) ? implode(',', $data['areas']) : $data['areas']) : '')
            ]);

            // Atualizar usuário relacionado
            $professor->user->update([
                'name' => $data['nome'],
                'email' => $data['email']
            ]);

            // Atualizar linhas de pesquisa
            if (isset($data['linhas_pesquisa_ids']) && is_array($data['linhas_pesquisa_ids'])) {
                $professor->linhasPesquisa()->sync($data['linhas_pesquisa_ids']);
            }

            Cache::forget('professores_db');

            return [
                'id' => $professor->id,
                'nome' => $professor->nome,
                'email' => $professor->email,
                'telefone' => $professor->telefone,
                'curso' => $professor->curso,
                'areas' => $professor->areas_interesse_array,
                'linhas_pesquisa_ids' => $professor->linhasPesquisa->pluck('id')->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar professor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProfessor($id)
    {
        try {
            $professor = Professor::findOrFail($id);
            
            // Remover relacionamentos
            $professor->linhasPesquisa()->detach();
            
            // Remover usuário relacionado (cascade delete)
            $professor->user->delete();
            
            Cache::forget('professores_db');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao deletar professor: ' . $e->getMessage());
            throw $e;
        }
    }

    // =============== LINHAS DE PESQUISA ===============

    public function getLinhasPesquisa()
    {
        return Cache::remember('linhas_pesquisa_db', 300, function () {
            try {
                return LinhaPesquisa::all()->map(function ($linha) {
                    return [
                        'id' => $linha->id,
                        'nome' => $linha->nome,
                        'descricao' => $linha->descricao
                    ];
                })->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar linhas de pesquisa: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function createLinhaPesquisa($data)
    {
        try {
            $linha = LinhaPesquisa::create([
                'nome' => $data['nome'],
                'descricao' => $data['descricao']
            ]);

            Cache::forget('linhas_pesquisa_db');

            return [
                'id' => $linha->id,
                'nome' => $linha->nome,
                'descricao' => $linha->descricao
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao criar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateLinhaPesquisa($id, $data)
    {
        try {
            $linha = LinhaPesquisa::findOrFail($id);
            
            $linha->update([
                'nome' => $data['nome'],
                'descricao' => $data['descricao']
            ]);

            Cache::forget('linhas_pesquisa_db');

            return [
                'id' => $linha->id,
                'nome' => $linha->nome,
                'descricao' => $linha->descricao
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteLinhaPesquisa($id)
    {
        try {
            $linha = LinhaPesquisa::findOrFail($id);
            
            // Remover relacionamentos com professores
            $linha->professores()->detach();
            
            // Deletar linha de pesquisa
            $linha->delete();

            Cache::forget('linhas_pesquisa_db');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao deletar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    // =============== USUÁRIOS (compatibilidade com UserService) ===============

    public function getUsers()
    {
        return Cache::remember('usuarios_db', 300, function () {
            try {
                return User::all()->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'nivel_permissao' => $this->getUserLevel($user),
                        'ativo' => 1, // assumindo que todos usuários são ativos
                        'created_at' => $user->created_at->format('Y-m-d H:i:s')
                    ];
                })->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar usuários: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getUserByEmail($email)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return null;
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'nivel_permissao' => $this->getUserLevel($user),
                'ativo' => 1,
                'created_at' => $user->created_at->format('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário por email: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createUser($data)
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            Cache::forget('usuarios_db');

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nivel_permissao' => $data['nivel_permissao'] ?? self::NIVEL_ESTUDANTE,
                'ativo' => $data['ativo'] ?? 1,
                'created_at' => $user->created_at->format('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getUserLevel($user)
    {
        // Determinar nível baseado no email ou relações
        if (str_contains($user->email, 'admin')) {
            return self::NIVEL_ADMIN;
        } elseif ($user->professor) {
            return self::NIVEL_PROFESSOR;
        } else {
            return self::NIVEL_ESTUDANTE;
        }
    }
}
