<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Google\Service\Sheets\ValueRange;

class UserService
{
    protected $googleSheetsService;
    
    // Níveis de permissão
    const NIVEL_ADMIN = 1;
    const NIVEL_PROFESSOR = 2;
    const NIVEL_ESTUDANTE = 3;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    public function getUsers()
    {
        return Cache::remember('usuarios', 300, function () {
            try {
                $range = 'usuarios!A:G'; // A=ID, B=Nome, C=Email, D=Senha, E=Nivel, F=Ativo, G=DataCriacao
                $response = $this->googleSheetsService->service->spreadsheets_values->get(
                    $this->googleSheetsService->getSpreadsheetId(), 
                    $range
                );
                $values = $response->getValues();

                if (empty($values)) {
                    return [];
                }

                $usuarios = [];
                // Skip header row
                for ($i = 1; $i < count($values); $i++) {
                    $row = $values[$i];
                    if (count($row) >= 5) { // Minimum required columns
                        $usuarios[] = [
                            'id' => (int) ($row[0] ?? $i),
                            'name' => $row[1] ?? '',
                            'email' => $row[2] ?? '',
                            'password' => $row[3] ?? '',
                            'nivel_permissao' => (int) ($row[4] ?? 3),
                            'ativo' => (bool) ($row[5] ?? 1),
                            'created_at' => $row[6] ?? null
                        ];
                    }
                }

                return $usuarios;
            } catch (\Exception $e) {
                Log::error('Erro ao buscar usuários: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function findUserByEmail($email)
    {
        $users = $this->getUsers();
        return collect($users)->firstWhere('email', $email);
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
        $users = $this->getUsers();
        return collect($users)->firstWhere('id', $id);
    }

    public function validateCredentials($email, $password)
    {
        $user = $this->findUserByEmail($email);
        
        if (!$user || !$user['ativo']) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    public function createUser($data)
    {
        try {
            $users = $this->getUsers();
            $newId = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;

            // Verificar se email já existe
            if ($this->findUserByEmail($data['email'])) {
                throw new \Exception('Email já está em uso');
            }

            $row = [
                $newId,
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['nivel_permissao'] ?? self::NIVEL_ESTUDANTE,
                $data['ativo'] ?? 1,
                date('Y-m-d H:i:s')
            ];

            $range = 'usuarios!A:G';
            $values = [$row];
            $body = new ValueRange(['values' => $values]);
            
            $params = ['valueInputOption' => 'USER_ENTERED'];
            $this->googleSheetsService->service->spreadsheets_values->append(
                $this->googleSheetsService->getSpreadsheetId(), 
                $range, 
                $body, 
                $params
            );

            Cache::forget('usuarios');
            
            return [
                'id' => $newId,
                'name' => $data['name'],
                'email' => $data['email'],
                'nivel_permissao' => $data['nivel_permissao'] ?? self::NIVEL_ESTUDANTE,
                'ativo' => $data['ativo'] ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
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
            $users = $this->getUsers();
            $index = array_search($id, array_column($users, 'id'));
            
            if ($index === false) {
                throw new \Exception('Usuário não encontrado');
            }

            // Verificar se email já existe em outro usuário
            $existingUser = $this->findUserByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                throw new \Exception('Email já está em uso por outro usuário');
            }

            $rowNumber = $index + 2; // +1 for header, +1 for 0-based index
            $range = "usuarios!A{$rowNumber}:G{$rowNumber}";

            $currentUser = $users[$index];
            
            $row = [
                $id,
                $data['name'] ?? $currentUser['name'],
                $data['email'] ?? $currentUser['email'],
                isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : $currentUser['password'],
                $data['nivel_permissao'] ?? $currentUser['nivel_permissao'],
                $data['ativo'] ?? $currentUser['ativo'],
                $currentUser['created_at']
            ];

            $values = [$row];
            $body = new ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            
            $this->googleSheetsService->service->spreadsheets_values->update(
                $this->googleSheetsService->getSpreadsheetId(), 
                $range, 
                $body, 
                $params
            );

            Cache::forget('usuarios');
            
            return [
                'id' => $id,
                'name' => $data['name'] ?? $currentUser['name'],
                'email' => $data['email'] ?? $currentUser['email'],
                'nivel_permissao' => $data['nivel_permissao'] ?? $currentUser['nivel_permissao'],
                'ativo' => $data['ativo'] ?? $currentUser['ativo'],
                'created_at' => $currentUser['created_at']
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser($id)
    {
        try {
            $users = $this->getUsers();
            $index = array_search($id, array_column($users, 'id'));
            
            if ($index === false) {
                throw new \Exception('Usuário não encontrado');
            }

            $rowNumber = $index + 2; // +1 for header, +1 for 0-based index
            
            // Clear the row
            $range = "usuarios!A{$rowNumber}:G{$rowNumber}";
            $body = new ValueRange(['values' => [['']]]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            
            $this->googleSheetsService->service->spreadsheets_values->update(
                $this->googleSheetsService->getSpreadsheetId(), 
                $range, 
                $body, 
                $params
            );

            Cache::forget('usuarios');
            
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
            case self::NIVEL_PROFESSOR:
                return 'Professor';
            case self::NIVEL_ESTUDANTE:
                return 'Estudante';
            default:
                return 'Desconhecido';
        }
    }

    public function canAccessAdmin($user)
    {
        return $user && $user['nivel_permissao'] == self::NIVEL_ADMIN;
    }

    public function canAccessProfessor($user)
    {
        return $user && in_array($user['nivel_permissao'], [self::NIVEL_ADMIN, self::NIVEL_PROFESSOR]);
    }

    public function canAccessEstudante($user)
    {
        return $user && in_array($user['nivel_permissao'], [self::NIVEL_ADMIN, self::NIVEL_PROFESSOR, self::NIVEL_ESTUDANTE]);
    }
}
