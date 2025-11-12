<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Professor;
use App\Models\LinhaPesquisa;
use App\Models\AreaPesquisa;
use App\Models\Curso;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DatabaseService
{
    // Níveis de permissão
    const NIVEL_ADMIN = 'SUPER';
    const NIVEL_ORGANIZADOR = 'DA';
    const NIVEL_BASICO = 'BASICO';

    // =============== PROFESSORES ===============

    public function getProfessores()
    {
        return Cache::remember('professores_db', 300, function () {
            try {
                return Professor::with(['curso', 'linhasPesquisa', 'areasInteresse'])
                    ->get()
                    ->map(function ($professor) {
                        return [
                            'id' => $professor->id,
                            'nome' => $professor->nome,
                            'email' => $professor->email,
                            'telefone' => $professor->telefone,
                            'id_curso' => $professor->id_curso,
                            'curso' => $professor->curso ? $professor->curso->nome : null,
                            'departamento' => $professor->departamento,
                            'linhas_pesquisa' => $professor->linhasPesquisa->map(function ($linha) {
                                return [
                                    'id' => $linha->id,
                                    'nome' => $linha->nome
                                ];
                            })->toArray(),
                            'linhas_pesquisa_ids' => $professor->linhasPesquisa->pluck('id')->toArray(),
                            'areas_interesse' => $professor->areasInteresse->map(function ($area) {
                                return [
                                    'id' => $area->id,
                                    'nome' => $area->nome
                                ];
                            })->toArray(),
                            'areas_interesse_ids' => $professor->areasInteresse->pluck('id')->toArray(),
                            'criado_por' => $professor->criado_por
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar professores: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getProfessorById($id)
    {
        try {
            $professor = Professor::with(['curso', 'linhasPesquisa', 'areasInteresse'])
                ->findOrFail($id);

            return [
                'id' => $professor->id,
                'nome' => $professor->nome,
                'email' => $professor->email,
                'telefone' => $professor->telefone,
                'id_curso' => $professor->id_curso,
                'curso' => $professor->curso ? $professor->curso->nome : null,
                'departamento' => $professor->departamento,
                'linhas_pesquisa' => $professor->linhasPesquisa->map(function ($linha) {
                    return [
                        'id' => $linha->id,
                        'nome' => $linha->nome,
                        'descricao' => $linha->descricao
                    ];
                })->toArray(),
                'areas_interesse' => $professor->areasInteresse->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'nome' => $area->nome,
                        'descricao' => $area->descricao
                    ];
                })->toArray(),
                'criado_por' => $professor->criado_por
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar organizador: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createProfessor($data)
    {
        try {
            DB::beginTransaction();

            $professor = Professor::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'telefone' => $data['telefone'] ?? null,
                'id_curso' => $data['id_curso'],
                'departamento' => $data['departamento'] ?? null,
                'criado_por' => $data['criado_por'] ?? auth()->id()
            ]);

            // Associar linhas de pesquisa
            if (isset($data['linhas_pesquisa_ids']) && is_array($data['linhas_pesquisa_ids'])) {
                $professor->linhasPesquisa()->attach($data['linhas_pesquisa_ids']);
            }

            // Associar áreas de interesse
            if (isset($data['areas_interesse_ids']) && is_array($data['areas_interesse_ids'])) {
                $professor->areasInteresse()->attach($data['areas_interesse_ids']);
            }

            DB::commit();
            Cache::forget('professores_db');

            return $this->getProfessorById($professor->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar organizador: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfessor($id, $data)
    {
        try {
            DB::beginTransaction();

            $professor = Professor::findOrFail($id);

            $professor->update([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'telefone' => $data['telefone'] ?? null,
                'id_curso' => $data['id_curso'],
                'departamento' => $data['departamento'] ?? null
            ]);

            // Sincronizar linhas de pesquisa (remove antigas e adiciona novas)
            if (isset($data['linhas_pesquisa_ids'])) {
                $professor->linhasPesquisa()->sync($data['linhas_pesquisa_ids']);
            }

            // Sincronizar áreas de interesse
            if (isset($data['areas_interesse_ids'])) {
                $professor->areasInteresse()->sync($data['areas_interesse_ids']);
            }

            DB::commit();
            Cache::forget('professores_db');

            return $this->getProfessorById($professor->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar organizador: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProfessor($id)
    {
        try {
            DB::beginTransaction();

            $professor = Professor::findOrFail($id);

            // Remover relacionamentos many-to-many
            $professor->linhasPesquisa()->detach();
            $professor->areasInteresse()->detach();

            // Deletar organizador
            $professor->delete();

            DB::commit();
            Cache::forget('professores_db');

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar organizador: ' . $e->getMessage());
            throw $e;
        }
    }

    // =============== LINHAS DE PESQUISA ===============

    public function getLinhasPesquisa()
    {
        return Cache::remember('linhas_pesquisa_db', 300, function () {
            try {
                return LinhaPesquisa::with(['areaPesquisa', 'professores'])
                    ->get()
                    ->map(function ($linha) {
                        return [
                            'id' => $linha->id,
                            'nome' => $linha->nome,
                            'descricao' => $linha->descricao,
                            'id_area_pesquisa' => $linha->id_area_pesquisa,
                            'area_pesquisa' => $linha->areaPesquisa ? $linha->areaPesquisa->nome : null,
                            'professores_count' => $linha->professores->count(),
                            'criado_por' => $linha->criado_por
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar linhas de pesquisa: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getLinhaPesquisaById($id)
    {
        try {
            $linha = LinhaPesquisa::with(['areaPesquisa', 'professores'])
                ->findOrFail($id);

            return [
                'id' => $linha->id,
                'nome' => $linha->nome,
                'descricao' => $linha->descricao,
                'id_area_pesquisa' => $linha->id_area_pesquisa,
                'area_pesquisa' => $linha->areaPesquisa ? [
                    'id' => $linha->areaPesquisa->id,
                    'nome' => $linha->areaPesquisa->nome
                ] : null,
                'professores' => $linha->professores->map(function ($professor) {
                    return [
                        'id' => $professor->id,
                        'nome' => $professor->nome,
                        'email' => $professor->email
                    ];
                })->toArray(),
                'criado_por' => $linha->criado_por
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createLinhaPesquisa($data)
    {
        try {
            $linha = LinhaPesquisa::create([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'id_area_pesquisa' => $data['id_area_pesquisa'],
                'criado_por' => $data['criado_por'] ?? auth()->id()
            ]);

            Cache::forget('linhas_pesquisa_db');

            return $this->getLinhaPesquisaById($linha->id);

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
                'descricao' => $data['descricao'] ?? null,
                'id_area_pesquisa' => $data['id_area_pesquisa']
            ]);

            Cache::forget('linhas_pesquisa_db');

            return $this->getLinhaPesquisaById($linha->id);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteLinhaPesquisa($id)
    {
        try {
            DB::beginTransaction();

            $linha = LinhaPesquisa::findOrFail($id);

            // Remover relacionamentos com professores
            $linha->professores()->detach();

            // Deletar linha de pesquisa
            $linha->delete();

            DB::commit();
            Cache::forget('linhas_pesquisa_db');

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    // =============== ÁREAS DE PESQUISA ===============

    public function getAreasPesquisa()
    {
        return Cache::remember('areas_pesquisa_db', 300, function () {
            try {
                return AreaPesquisa::withCount(['linhasPesquisa', 'professores'])
                    ->get()
                    ->map(function ($area) {
                        return [
                            'id' => $area->id,
                            'nome' => $area->nome,
                            'descricao' => $area->descricao,
                            'linhas_pesquisa_count' => $area->linhas_pesquisa_count,
                            'professores_count' => $area->professores_count,
                            'criado_por' => $area->criado_por
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar áreas de pesquisa: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getAreaPesquisaById($id)
    {
        try {
            $area = AreaPesquisa::with(['linhasPesquisa', 'professores'])
                ->findOrFail($id);

            return [
                'id' => $area->id,
                'nome' => $area->nome,
                'descricao' => $area->descricao,
                'linhas_pesquisa' => $area->linhasPesquisa->map(function ($linha) {
                    return [
                        'id' => $linha->id,
                        'nome' => $linha->nome
                    ];
                })->toArray(),
                'professores' => $area->professores->map(function ($professor) {
                    return [
                        'id' => $professor->id,
                        'nome' => $professor->nome,
                        'email' => $professor->email
                    ];
                })->toArray(),
                'criado_por' => $area->criado_por
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar área de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createAreaPesquisa($data)
    {
        try {
            $area = AreaPesquisa::create([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'criado_por' => $data['criado_por'] ?? auth()->id()
            ]);

            Cache::forget('areas_pesquisa_db');

            return $this->getAreaPesquisaById($area->id);

        } catch (\Exception $e) {
            Log::error('Erro ao criar área de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateAreaPesquisa($id, $data)
    {
        try {
            $area = AreaPesquisa::findOrFail($id);

            $area->update([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null
            ]);

            Cache::forget('areas_pesquisa_db');

            return $this->getAreaPesquisaById($area->id);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar área de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteAreaPesquisa($id)
    {
        try {
            DB::beginTransaction();

            $area = AreaPesquisa::findOrFail($id);

            // Verificar se há linhas de pesquisa vinculadas
            if ($area->linhasPesquisa()->count() > 0) {
                throw new \Exception('Não é possível deletar área com linhas de pesquisa vinculadas');
            }

            // Remover relacionamentos com professores
            $area->professores()->detach();

            // Deletar área
            $area->delete();

            DB::commit();
            Cache::forget('areas_pesquisa_db');

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar área de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    // =============== CURSOS ===============

    public function getCursos()
    {
        return Cache::remember('cursos_db', 300, function () {
            try {
                return Curso::withCount(['professores', 'usuarios'])
                    ->get()
                    ->map(function ($curso) {
                        return [
                            'id' => $curso->id,
                            'nome' => $curso->nome,
                            'professores_count' => $curso->professores_count,
                            'usuarios_count' => $curso->usuarios_count
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar cursos: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getCursoById($id)
    {
        try {
            $curso = Curso::with(['professores', 'usuarios'])
                ->findOrFail($id);

            return [
                'id' => $curso->id,
                'nome' => $curso->nome,
                'professores' => $curso->professores->map(function ($professor) {
                    return [
                        'id' => $professor->id,
                        'nome' => $professor->nome,
                        'email' => $professor->email
                    ];
                })->toArray(),
                'usuarios' => $curso->usuarios->map(function ($usuario) {
                    return [
                        'id' => $usuario->id,
                        'nome' => $usuario->nome,
                        'email' => $usuario->email
                    ];
                })->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar curso: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createCurso($data)
    {
        try {
            $curso = Curso::create([
                'nome' => $data['nome']
            ]);

            Cache::forget('cursos_db');

            return [
                'id' => $curso->id,
                'nome' => $curso->nome
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao criar curso: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateCurso($id, $data)
    {
        try {
            $curso = Curso::findOrFail($id);

            $curso->update([
                'nome' => $data['nome']
            ]);

            Cache::forget('cursos_db');

            return [
                'id' => $curso->id,
                'nome' => $curso->nome
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar curso: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCurso($id)
    {
        try {
            $curso = Curso::findOrFail($id);

            // Verificar se há professores ou usuários vinculados
            if ($curso->professores()->count() > 0 || $curso->usuarios()->count() > 0) {
                throw new \Exception('Não é possível deletar curso com professores ou usuários vinculados');
            }

            $curso->delete();

            Cache::forget('cursos_db');

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao deletar curso: ' . $e->getMessage());
            throw $e;
        }
    }

   // =============== USUÁRIOS ===============

    public function getUsers()
    {
        return Cache::remember('usuarios_db', 300, function () {
            try {
                // Foi usado whereNot para já filtrar os admins da consulta
                return Usuario::with('curso')
                    ->where('tipo_permissao', '!=', self::NIVEL_ADMIN) // Filtra 'SUPER'
                    ->get()
                    ->map(function ($user) {

                        // --- LÓGICA DE TRADUÇÃO ADICIONADA ---
                        $levelName = 'N/A'; // Padrão
                        if ($user->tipo_permissao === self::NIVEL_ORGANIZADOR) { // 'DA'
                            $levelName = 'Organizador';
                        } else if ($user->tipo_permissao === self::NIVEL_BASICO) { // 'BASICO'
                            $levelName = 'Estudante';
                        } else if ($user->tipo_permissao === self::NIVEL_ADMIN) { // 'BASICO'
                            $levelName = 'Administrador';
                        }
                        // ----------------------------------------

                        return [
                            'id' => $user->id,
                            'nome' => $user->nome,
                            'email' => $user->email,

                            // --- CORREÇÃO AQUI ---
                            // 1. Foi modificado o nome da chave para 'level' (o que o JS espera)
                            // 2. Foi usado o valor já traduzido ($levelName)
                            'level' => $levelName,
                            // ---------------------

                            'ativo' => $user->ativo,
                            'id_curso' => $user->id_curso,
                            'curso' => $user->curso ? $user->curso->nome : null,
                            'data_criacao' => $user->data_criacao ? $user->data_criacao->format('Y-m-d H:i:s') : null,
                            'data_atualizacao' => $user->data_atualizacao ? $user->data_atualizacao->format('Y-m-d H:i:s') : null,

                            'is_super' => $user->isSuperAdmin(),
                            'is_da' => $user->isDepartamentoAcademico(),
                            'is_basico' => $user->isBasico()
                        ];
                    })
                    ->values() // Re-indexa o array
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Erro ao buscar usuários: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getUserById($id)
    {
        try {
            $user = Usuario::with(['curso', 'areasInteresse'])->findOrFail($id);

            return [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'tipo_permissao' => $user->tipo_permissao,
                'ativo' => $user->ativo,
                'id_curso' => $user->id_curso,
                'telefone' => $user->telefone,
                'periodo' => $user->periodo,
                'biografia' => $user->biografia,
                'lattes' => $user->lattes,
                'curso' => $user->curso ? [
                    'id' => $user->curso->id,
                    'nome' => $user->curso->nome
                ] : null,
                'areas_interesse' => $user->areasInteresse->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'nome' => $area->nome,
                        'descricao' => $area->descricao
                    ];
                })->toArray(),
                'areas_interesse_ids' => $user->areasInteresse->pluck('id')->toArray(),
                'data_criacao' => $user->data_criacao->format('Y-m-d H:i:s'),
                'data_atualizacao' => $user->data_atualizacao->format('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $user = Usuario::where('email', $email)->first();

            if (!$user) {
                return null;
            }

            return [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'senha' => $user->senha,
                'tipo_permissao' => $user->tipo_permissao,
                'ativo' => $user->ativo,
                'id_curso' => $user->id_curso,
                'data_criacao' => $user->data_criacao ? $user->data_criacao->format('Y-m-d H:i:s') : null
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário por email: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createUser($data)
    {
        try {
            $user = Usuario::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'senha' => $data['senha'], // Hash é feito automaticamente no model
                'id_curso' => $data['id_curso'] ?? null,
                'ativo' => $data['ativo'] ?? false,
                'tipo_permissao' => $data['tipo_permissao'] ?? self::NIVEL_BASICO
            ]);

            Cache::forget('usuarios_db');

            return [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'tipo_permissao' => $user->tipo_permissao,
                'ativo' => $user->ativo,
                'data_criacao' => $user->data_criacao->format('Y-m-d H:i:s')
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUser($id, $data)
    {
        try {
            DB::beginTransaction();

            $user = Usuario::findOrFail($id);

            $updateData = [
                'nome' => $data['nome'] ?? $user->nome,
                'email' => $data['email'] ?? $user->email,
                'id_curso' => $data['id_curso'] ?? $user->id_curso,
                'tipo_permissao' => $data['tipo_permissao'] ?? $user->tipo_permissao,
                'telefone' => $data['telefone'] ?? $user->telefone,
                'periodo' => $data['periodo'] ?? $user->periodo,
                'biografia' => $data['biografia'] ?? $user->biografia,
                'lattes' => $data['lattes'] ?? $user->lattes
            ];

            // Atualizar senha apenas se fornecida
            if (isset($data['senha'])) {
                $updateData['senha'] = $data['senha'];
            }

            // Atualizar status ativo
            if (isset($data['ativo'])) {
                $updateData['ativo'] = $data['ativo'];
            }

            $user->update($updateData);

            // Atualizar áreas de interesse se fornecidas
            if (isset($data['areas_interesse_ids'])) {
                $user->areasInteresse()->sync($data['areas_interesse_ids']);
            }

            DB::commit();
            Cache::forget('usuarios_db');

            return $this->getUserById($user->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = Usuario::findOrFail($id);
            $user->delete();

            Cache::forget('usuarios_db');

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao deletar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ativarUsuario($id)
    {
        return $this->updateUser($id, ['ativo' => true]);
    }

    public function desativarUsuario($id)
    {
        return $this->updateUser($id, ['ativo' => false]);
    }

    // =============== MÉTODOS AUXILIARES ===============

    public function limparCache()
    {
        Cache::forget('professores_db');
        Cache::forget('linhas_pesquisa_db');
        Cache::forget('areas_pesquisa_db');
        Cache::forget('cursos_db');
        Cache::forget('usuarios_db');
    }

    public function getEstatisticas()
    {
        return [
            'total_professores' => Professor::count(),
            'total_linhas_pesquisa' => LinhaPesquisa::count(),
            'total_areas_pesquisa' => AreaPesquisa::count(),
            'total_cursos' => Curso::count(),
            'total_usuarios' => Usuario::count(),
            'usuarios_ativos' => Usuario::where('ativo', true)->count(),
            'usuarios_pendentes' => Usuario::where('ativo', false)->count()
        ];
    }
}
