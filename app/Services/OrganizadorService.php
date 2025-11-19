<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrganizadorService 
{
    public function atualizarDados($id, array $dados)
    {
        try {
            $organizador = Usuario::findOrFail($id);
            
            DB::beginTransaction();
            
            // Atualizar dados do organizador
            $organizador->update([
                'nome' => $dados['nome'] ?? $organizador->nome,
                'email' => $dados['email'] ?? $organizador->email,
                'telefone' => $dados['telefone'] ?? $organizador->telefone,
                'id_curso' => $dados['id_curso'] ?? $organizador->id_curso,
                'periodo' => $dados['periodo'] ?? $organizador->periodo,
                'biografia' => $dados['biografia'] ?? $organizador->biografia,
                'lattes' => $dados['lattes'] ?? $organizador->lattes
            ]);

            // Atualizar áreas de interesse se fornecidas
            if (isset($dados['areas_interesse_ids'])) {
                $organizador->areasInteresse()->sync($dados['areas_interesse_ids']);
            }

            // Atualizar senha se fornecida
            if (isset($dados['senha'])) {
                $organizador->senha = $dados['senha'];
                $organizador->save();
            }

            DB::commit();
            
            return $organizador->fresh(['curso', 'areasInteresse']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar perfil do organizador:', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
