<?php

namespace App\Services;

use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class OrganizadorService 
{
    private $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = Config::get('google.sheets.post_spreadsheet_id');
    }

    public function atualizarDados($id, array $dados)
    {
        try {
            // Busca todos os usuários
            $usuarios = Sheets::spreadsheet($this->spreadsheetId)
                ->sheet('usuarios')
                ->get();

            $headers = $usuarios->shift(); // Remove cabeçalho
            $rowIndex = null;

            // Encontra o índice do usuário
            foreach ($usuarios as $index => $row) {
                if ($row[0] === $id) {
                    $rowIndex = $index + 2; // +2 pois temos o cabeçalho
                    break;
                }
            }

            if (!$rowIndex) {
                throw new \Exception('Usuário não encontrado');
            }

            // Prepara os dados
            $values = [
                [
                    $id,
                    $dados['nome'],
                    $dados['email'],
                    '2', // Nível organizador
                    'S'  // Status ativo
                ]
            ];

            // Atualiza a planilha
            Sheets::spreadsheet($this->spreadsheetId)
                ->sheet('usuarios')
                ->range("A{$rowIndex}:E{$rowIndex}")
                ->update($values);

            // Limpa o cache
            Cache::forget('usuarios_data');

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil:', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
