<?php

namespace App\Services;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        return $user && ($user['tipo_permissao'] ?? null) == self::NIVEL_ADMIN;
    }

    public function canAccessOrganizador($user)
    {
        return $user && in_array(($user['tipo_permissao'] ?? null), [self::NIVEL_ADMIN, self::NIVEL_ORGANIZADOR]);
    }

    public function canAccessBasico($user)
    {
        return $user && in_array(($user['tipo_permissao'] ?? null), [self::NIVEL_ADMIN, self::NIVEL_ORGANIZADOR, self::NIVEL_BASICO]);
    }

    public function getNivelPermissaoTexto($tipo_permissao)
    {
        $niveis = [
            self::NIVEL_ADMIN => 'Administrador',
            self::NIVEL_ORGANIZADOR => 'Organizador',
            self::NIVEL_BASICO => 'Básico'
        ];

        return $niveis[$tipo_permissao] ?? 'Desconhecido';
    }

    public function findUserByEmail($email)
    {
        try {
            $usuario = Usuario::where('email', $email)->first();
            if (!$usuario) {
                return null;
            }
            
            // Retornar como array incluindo senha
            $userData = $usuario->toArray();
            $userData['senha'] = $usuario->senha;
            
            return $userData;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário por email: ' . $e->getMessage());
            throw $e;
        }
    }

    public function buscarPorEmail($email)
    {
        return $this->findUserByEmail($email);
    }

    public function createUser(array $data)
    {
        try {
            $usuario = Usuario::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'senha' => Hash::make($data['senha']),
                'tipo_permissao' => $data['tipo_permissao'],
                'ativo' => $data['ativo'] ?? true,
            ]);

            return $usuario->toArray();
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUsers()
    {
        try {
            return Usuario::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuários: ' . $e->getMessage());
            throw $e;
        }
    }

    public function criar(array $data)
    {
        return $this->createUser($data);
    }
}
