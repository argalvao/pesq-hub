<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DatabaseUserService
{
    // Níveis de permissão
    const NIVEL_ADMIN = 1;
    const NIVEL_ORGANIZADOR = 2;
    const NIVEL_BASICO = 3;

    public function getUsers()
    {
        try {
            return User::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuários: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findUserByEmail($email)
    {
        try {
            $user = User::where('email', $email)->first();
            if (!$user) {
                return null;
            }
            
            // Incluir password que está oculto no Model
            $userData = $user->toArray();
            $userData['password'] = $user->password;
            
            return $userData;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário por email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Alias para findUserByEmail (usado no CadastroComConfirmacaoController)
     */
    public function buscarPorEmail($email)
    {
        return $this->findUserByEmail($email);
    }

    public function findUserById($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return null;
            }
            
            // Incluir password que está oculto no Model
            $userData = $user->toArray();
            $userData['password'] = $user->password;
            
            return $userData;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário por ID: ' . $e->getMessage());
            throw $e;
        }
    }

    public function validateCredentials($email, $password)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || !$user->ativo) {
                return false;
            }

            return Hash::check($password, $user->password);
        } catch (\Exception $e) {
            Log::error('Erro ao validar credenciais: ' . $e->getMessage());
            return false;
        }
    }

    public function createUser($data)
    {
        try {
            // Verificar se email já existe
            if ($this->findUserByEmail($data['email'])) {
                throw new \Exception('Email já está em uso');
            }

            // Se a senha já estiver hasheada (bcrypt), não hashear novamente
            if (isset($data['password']) && !str_starts_with($data['password'], '$2y$')) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'nivel_permissao' => $data['nivel_permissao'] ?? self::NIVEL_BASICO,
                'ativo' => $data['ativo'] ?? true,
            ]);

            Log::info('Usuário criado com sucesso no banco: ' . $user->email);

            return $user->toArray();
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Alias para createUser (usado no CadastroComConfirmacaoController)
     */
    public function criar($data)
    {
        return $this->createUser($data);
    }

    public function updateUser($id, $data)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                throw new \Exception('Usuário não encontrado');
            }

            // Verificar se email já existe em outro usuário
            $existingUser = User::where('email', $data['email'])
                ->where('id', '!=', $id)
                ->first();
            
            if ($existingUser) {
                throw new \Exception('Email já está em uso por outro usuário');
            }

            // Se a senha foi fornecida, hashear
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            Log::info('Usuário atualizado com sucesso: ' . $user->email);

            return $user->fresh()->toArray();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                throw new \Exception('Usuário não encontrado');
            }

            $user->delete();

            Log::info('Usuário deletado com sucesso: ' . $user->email);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao deletar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getNivelPermissaoTexto($nivel)
    {
        switch ($nivel) {
            case self::NIVEL_ADMIN:
                return 'Administrador';
            case self::NIVEL_ORGANIZADOR:
                return 'Organizador';
            case self::NIVEL_BASICO:
                return 'Estudante';
            default:
                return 'Desconhecido';
        }
    }

    public function canAccessAdmin($user)
    {
        return $user && $user['nivel_permissao'] == self::NIVEL_ADMIN;
    }

    public function canAccessOrganizador($user)
    {
        return $user && in_array($user['nivel_permissao'], [self::NIVEL_ADMIN, self::NIVEL_ORGANIZADOR]);
    }

    public function canAccessEstudante($user)
    {
        return $user && in_array($user['nivel_permissao'], [self::NIVEL_ADMIN, self::NIVEL_ORGANIZADOR, self::NIVEL_BASICO]);
    }
}
