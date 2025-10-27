<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;
use Google\Service\Sheets\Request as SheetsRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\AddSheetRequest;
use Google\Service\Sheets\SheetProperties;
use Google\Service\Sheets\ValueRange;

class InitializeGoogleSheet extends Command
{
    protected $signature = 'sheets:init';
    protected $description = 'Inicializa a estrutura da planilha Google com as abas necessárias';

    public function handle()
    {
        try {
            $service = app(GoogleSheetsService::class);
            $spreadsheetId = env('GOOGLE_SHEET_ID');

            $this->info('Inicializando estrutura da planilha Google...');

            // Criar abas se não existirem
            $this->createSheets($service, $spreadsheetId);
            
            // Adicionar cabeçalhos
            $this->addHeaders($service, $spreadsheetId);
            
            // Adicionar dados de exemplo
            $this->addSampleData($service, $spreadsheetId);

            $this->info('Estrutura da planilha inicializada com sucesso!');
            $this->info('Acesse: https://docs.google.com/spreadsheets/d/' . $spreadsheetId);

        } catch (\Exception $e) {
            $this->error('Erro ao inicializar planilha: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function createSheets($service, $spreadsheetId)
    {
        $sheets = ['professores', 'linhas_pesquisa', 'usuarios'];
        
        foreach ($sheets as $sheetName) {
            try {
                // Tentar acessar a aba para ver se existe
                $range = $sheetName . '!A1';
                $service->service->spreadsheets_values->get($spreadsheetId, $range);
                $this->info("Aba '$sheetName' já existe.");
            } catch (\Exception $e) {
                // Aba não existe, criar
                $this->info("Criando aba '$sheetName'...");
                
                $requests = [
                    new SheetsRequest([
                        'addSheet' => new AddSheetRequest([
                            'properties' => new SheetProperties([
                                'title' => $sheetName
                            ])
                        ])
                    ])
                ];

                $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
                    'requests' => $requests
                ]);

                $service->service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
                $this->info("Aba '$sheetName' criada com sucesso.");
            }
        }
    }

    private function addHeaders($service, $spreadsheetId)
    {
        // Headers para professores
        $professoresHeaders = [['ID', 'Nome', 'Email', 'Telefone', 'Curso', 'Areas de Interesse', 'Linhas de Pesquisa IDs']];
        $range = 'professores!A1:G1';
        $body = new ValueRange(['values' => $professoresHeaders]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        
        try {
            $service->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
            $this->info('Cabeçalhos da aba professores adicionados.');
        } catch (\Exception $e) {
            $this->warn('Erro ao adicionar cabeçalhos de professores: ' . $e->getMessage());
        }

        // Headers para linhas de pesquisa
        $linhasHeaders = [['ID', 'Nome', 'Descrição']];
        $range = 'linhas_pesquisa!A1:C1';
        $body = new ValueRange(['values' => $linhasHeaders]);
        
        try {
            $service->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
            $this->info('Cabeçalhos da aba linhas_pesquisa adicionados.');
        } catch (\Exception $e) {
            $this->warn('Erro ao adicionar cabeçalhos de linhas de pesquisa: ' . $e->getMessage());
        }

        // Headers para usuários
        $usuariosHeaders = [['ID', 'Nome', 'Email', 'Senha', 'Nivel_Permissao', 'Ativo', 'Data_Criacao']];
        $range = 'usuarios!A1:G1';
        $body = new ValueRange(['values' => $usuariosHeaders]);
        
        try {
            $service->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
            $this->info('Cabeçalhos da aba usuarios adicionados.');
        } catch (\Exception $e) {
            $this->warn('Erro ao adicionar cabeçalhos de usuarios: ' . $e->getMessage());
        }
    }

    private function addSampleData($service, $spreadsheetId)
    {
        // Dados de exemplo para linhas de pesquisa
        $linhasSample = [
            [1, 'Inteligência Artificial', 'Pesquisa em algoritmos de IA e machine learning'],
            [2, 'Engenharia de Software', 'Desenvolvimento de metodologias e ferramentas de software'],
            [3, 'Computação Gráfica', 'Processamento de imagens e renderização 3D'],
            [4, 'Banco de Dados', 'Otimização e design de sistemas de banco de dados'],
            [5, 'Redes de Computadores', 'Protocolos e arquiteturas de rede']
        ];

        $range = 'linhas_pesquisa!A2:C6';
        $body = new ValueRange(['values' => $linhasSample]);
        $params = ['valueInputOption' => 'USER_ENTERED'];

        try {
            $service->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
            $this->info('Dados de exemplo de linhas de pesquisa adicionados.');
        } catch (\Exception $e) {
            $this->warn('Erro ao adicionar dados de exemplo de linhas: ' . $e->getMessage());
        }

        // Dados de exemplo para professores
        $professoresSample = [
            [1, 'Dr. João Silva', 'abel.ramalho18@gmail.com', '(75) 99999-0001', 'Ciência da Computação', 'Inteligência Artificial,Machine Learning', '1'],
            [2, 'Dra. Maria Santos', 'maria.santos@univ.edu', '(75) 99999-0002', 'Engenharia de Software', 'Desenvolvimento de Software,Metodologias Ágeis', '2'],
            [3, 'Dr. Carlos Oliveira', 'carlos.oliveira@univ.edu', '(75) 99999-0003', 'Ciência da Computação', 'Computação Gráfica,Realidade Virtual', '3'],
            [4, 'Dra. Ana Costa', 'ana.costa@univ.edu', '(75) 99999-0004', 'Sistemas de Informação', 'Banco de Dados,Big Data', '4'],
            [5, 'Dr. Pedro Lima', 'pedro.lima@univ.edu', '(75) 99999-0005', 'Redes de Computadores', 'Segurança,Protocolos de Rede', '5']
        ];

        $range = 'professores!A2:G6';
        $body = new ValueRange(['values' => $professoresSample]);

        try {
            $service->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
            $this->info('Dados de exemplo de professores adicionados.');
        } catch (\Exception $e) {
            $this->warn('Erro ao adicionar dados de exemplo de professores: ' . $e->getMessage());
        }

        // Dados de exemplo para usuários
        $usuariosSample = [
            [1, 'Administrador', 'admin@pesqhub.com', password_hash('admin123', PASSWORD_DEFAULT), 1, 1, date('Y-m-d H:i:s')],
            [2, 'Dr. João Silva', 'joao.silva@univ.edu', password_hash('professor123', PASSWORD_DEFAULT), 2, 1, date('Y-m-d H:i:s')],
            [3, 'Dra. Maria Santos', 'maria.santos@univ.edu', password_hash('professor123', PASSWORD_DEFAULT), 2, 1, date('Y-m-d H:i:s')],
            [4, 'Ana Estudante', 'ana.estudante@gmail.com', password_hash('estudante123', PASSWORD_DEFAULT), 3, 1, date('Y-m-d H:i:s')],
            [5, 'Carlos Estudante', 'carlos.estudante@gmail.com', password_hash('estudante123', PASSWORD_DEFAULT), 3, 1, date('Y-m-d H:i:s')]
        ];

        $range = 'usuarios!A2:G6';
        $body = new ValueRange(['values' => $usuariosSample]);

        try {
            $service->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
            $this->info('Dados de exemplo de usuários adicionados.');
            $this->info('Credenciais criadas:');
            $this->info('- Admin: admin@pesqhub.com / admin123');
            $this->info('- Professor: abel.ramalho18@gmail.com / professor123');
            $this->info('- Estudante: ana.estudante@gmail.com / estudante123');
        } catch (\Exception $e) {
            $this->warn('Erro ao adicionar dados de exemplo de usuários: ' . $e->getMessage());
        }
    }
}
