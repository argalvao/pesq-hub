<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    public $service;
    private $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
        $this->initializeService();
    }

    public function getSpreadsheetId()
    {
        return $this->spreadsheetId;
    }

    private function initializeService()
    {
        $client = new Client();
        $client->setApplicationName('PesqHub');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/' . env('GOOGLE_APPLICATION_CREDENTIALS')));
        $client->setAccessType('offline');

        $this->service = new Sheets($client);
    }

    // Professores
    public function getProfessores()
    {
        return Cache::remember('professores', 300, function () {
            try {
                $range = 'professores!A:G'; // A=ID, B=Nome, C=Email, D=Telefone, E=Curso, F=Areas, G=LinhasPesquisaIds
                $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
                $values = $response->getValues();

                if (empty($values)) {
                    return [];
                }

                $professores = [];
                // Skip header row
                for ($i = 1; $i < count($values); $i++) {
                    $row = $values[$i];
                    if (count($row) >= 5) { // Minimum required columns
                        $professores[] = [
                            'id' => (int) ($row[0] ?? $i),
                            'nome' => $row[1] ?? '',
                            'email' => $row[2] ?? '',
                            'telefone' => $row[3] ?? '',
                            'curso' => $row[4] ?? '',
                            'areas_interesse' => isset($row[5]) ? explode(',', $row[5]) : [],
                            'linhas_pesquisa_ids' => isset($row[6]) ? array_map('intval', explode(',', $row[6])) : []
                        ];
                    }
                }

                return $professores;
            } catch (\Exception $e) {
                Log::error('Erro ao buscar professores: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function createProfessor($data)
    {
        try {
            $professores = $this->getProfessores();
            $newId = count($professores) > 0 ? max(array_column($professores, 'id')) + 1 : 1;

            $row = [
                $newId,
                $data['nome'],
                $data['email'],
                $data['telefone'] ?? '',
                $data['curso'],
                implode(',', $data['areas_interesse'] ?? []),
                implode(',', $data['linhas_pesquisa_ids'] ?? [])
            ];

            $range = 'professores!A:G';
            $values = [$row];
            $body = new ValueRange(['values' => $values]);
            
            $params = ['valueInputOption' => 'USER_ENTERED'];
            $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);

            Cache::forget('professores');
            
            return array_combine(['id', 'nome', 'email', 'telefone', 'curso', 'areas_interesse', 'linhas_pesquisa_ids'], $row);
        } catch (\Exception $e) {
            Log::error('Erro ao criar professor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfessor($id, $data)
    {
        try {
            $professores = $this->getProfessores();
            $index = array_search($id, array_column($professores, 'id'));
            
            if ($index === false) {
                throw new \Exception('Professor não encontrado');
            }

            $rowNumber = $index + 2; // +1 for header, +1 for 0-based index
            $range = "professores!A{$rowNumber}:G{$rowNumber}";

            $row = [
                $id,
                $data['nome'],
                $data['email'],
                $data['telefone'] ?? '',
                $data['curso'],
                implode(',', $data['areas_interesse'] ?? []),
                implode(',', $data['linhas_pesquisa_ids'] ?? [])
            ];

            $values = [$row];
            $body = new ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            
            $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);

            Cache::forget('professores');
            
            return array_combine(['id', 'nome', 'email', 'telefone', 'curso', 'areas_interesse', 'linhas_pesquisa_ids'], $row);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar professor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProfessor($id)
    {
        try {
            $professores = $this->getProfessores();
            $index = array_search($id, array_column($professores, 'id'));
            
            if ($index === false) {
                throw new \Exception('Professor não encontrado');
            }

            $rowNumber = $index + 2; // +1 for header, +1 for 0-based index
            
            // Delete row by clearing it (Google Sheets API doesn't have direct row deletion)
            $range = "professores!A{$rowNumber}:G{$rowNumber}";
            $body = new ValueRange(['values' => [['']]]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            
            $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);

            Cache::forget('professores');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao deletar professor: ' . $e->getMessage());
            throw $e;
        }
    }

    // Linhas de Pesquisa
    public function getLinhasPesquisa()
    {
        return Cache::remember('linhas_pesquisa', 300, function () {
            try {
                $range = 'linhas_pesquisa!A:C'; // A=ID, B=Nome, C=Descrição
                $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
                $values = $response->getValues();

                if (empty($values)) {
                    return [];
                }

                $linhas = [];
                // Skip header row
                for ($i = 1; $i < count($values); $i++) {
                    $row = $values[$i];
                    if (count($row) >= 3) {
                        $linhas[] = [
                            'id' => (int) ($row[0] ?? $i),
                            'nome' => $row[1] ?? '',
                            'descricao' => $row[2] ?? ''
                        ];
                    }
                }

                return $linhas;
            } catch (\Exception $e) {
                Log::error('Erro ao buscar linhas de pesquisa: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function createLinhaPesquisa($data)
    {
        try {
            $linhas = $this->getLinhasPesquisa();
            $newId = count($linhas) > 0 ? max(array_column($linhas, 'id')) + 1 : 1;

            $row = [
                $newId,
                $data['nome'],
                $data['descricao']
            ];

            $range = 'linhas_pesquisa!A:C';
            $values = [$row];
            $body = new ValueRange(['values' => $values]);
            
            $params = ['valueInputOption' => 'USER_ENTERED'];
            $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);

            Cache::forget('linhas_pesquisa');
            
            return array_combine(['id', 'nome', 'descricao'], $row);
        } catch (\Exception $e) {
            Log::error('Erro ao criar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateLinhaPesquisa($id, $data)
    {
        try {
            $linhas = $this->getLinhasPesquisa();
            $index = array_search($id, array_column($linhas, 'id'));
            
            if ($index === false) {
                throw new \Exception('Linha de pesquisa não encontrada');
            }

            $rowNumber = $index + 2; // +1 for header, +1 for 0-based index
            $range = "linhas_pesquisa!A{$rowNumber}:C{$rowNumber}";

            $row = [
                $id,
                $data['nome'],
                $data['descricao']
            ];

            $values = [$row];
            $body = new ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            
            $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);

            Cache::forget('linhas_pesquisa');
            
            return array_combine(['id', 'nome', 'descricao'], $row);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteLinhaPesquisa($id)
    {
        try {
            $linhas = $this->getLinhasPesquisa();
            $index = array_search($id, array_column($linhas, 'id'));
            
            if ($index === false) {
                throw new \Exception('Linha de pesquisa não encontrada');
            }

            $rowNumber = $index + 2; // +1 for header, +1 for 0-based index
            
            // Clear the row
            $range = "linhas_pesquisa!A{$rowNumber}:C{$rowNumber}";
            $body = new ValueRange(['values' => [['']]]);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            
            $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);

            Cache::forget('linhas_pesquisa');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao deletar linha de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }
}
