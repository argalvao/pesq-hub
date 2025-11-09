<?php

namespace App\Services;

use App\Models\Professor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrganizadorService 
{
    public function atualizarDados($id, array $dados)
    {
        try {
            $professor = Professor::findOrFail($id);
            
            DB::beginTransaction();
            
            // Atualizar dados do professor
            $professor->update([
                'nome' => $dados['nome'] ?? $professor->nome,
                'email' => $dados['email'] ?? $professor->email,
                'telefone' => $dados['telefone'] ?? $professor->telefone,
                'curso' => $dados['curso'] ?? $professor->curso,
                'areas_interesse' => $dados['areas_interesse'] ?? $professor->areas_interesse
            ]);

            // Atualizar linhas de pesquisa se fornecidas
            if (isset($dados['linhas_pesquisa_ids'])) {
                $professor->linhasPesquisa()->sync($dados['linhas_pesquisa_ids']);
            }

            // Atualizar usuário associado se necessário
            if ($professor->user) {
                $professor->user->update([
                    'name' => $dados['nome'] ?? $professor->user->name,
                    'email' => $dados['email'] ?? $professor->user->email
                ]);
            }

            DB::commit();
            
            return $professor->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar perfil:', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
