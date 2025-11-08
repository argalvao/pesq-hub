<?php

namespace App\Services;
use App\Models\Usuario;

class UsuarioService {

    // Níveis de permissão
    const NIVEL_ADMIN = 'SUPER';
    const NIVEL_ORGANIZADOR = 'DA';
    const NIVEL_BASICO = 'BASICO';

    public function ativarUsuario($id) {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->ativo = true;
            $usuario->save();
            return response()->json(['success' => true, 'message' => 'Usuário ativado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function desativarUsuario($id) {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->ativo = false;
            $usuario->save();
            return response()->json(['success' => true, 'message' => 'Usuário desativado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500);
        }
    }

    public function canAccessAdmin($user)
    {
        return $user && ($user['nivel_permissao'] ?? null) == self::NIVEL_ADMIN;
    }

    public function canAccessOrganizador($user)
    {
        return $user && in_array(($user['nivel_permissao'] ?? null), [self::NIVEL_ADMIN, self::NIVEL_ORGANIZADOR]);
    }

    public function canAccessBasico($user)
    {
        return $user && in_array(($user['nivel_permissao'] ?? null), [self::NIVEL_ADMIN, self::NIVEL_ORGANIZADOR, self::NIVEL_BASICO]);
    }
}
