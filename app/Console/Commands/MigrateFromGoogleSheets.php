<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;
use App\Services\DatabaseService;
use App\Models\User;
use App\Models\Professor;
use App\Models\LinhaPesquisa;
use Illuminate\Support\Facades\Hash;

class MigrateFromGoogleSheets extends Command
{
    protected $signature = 'app:migrate-from-sheets';
    protected $description = 'Migra todos os dados do Google Sheets para o banco PostgreSQL';

    protected $googleService;
    protected $databaseService;

    public function __construct()
    {
        parent::__construct();
        $this->googleService = app(GoogleSheetsService::class);
        $this->databaseService = app(DatabaseService::class);
    }

    public function handle()
    {
        $this->info('🚀 Iniciando migração do Google Sheets para PostgreSQL...');

        try {
            // 1. Migrar Linhas de Pesquisa
            $this->migrateLinhasPesquisa();
            
            // 2. Migrar Usuários
            $this->migrateUsuarios();
            
            // 3. Migrar Professores
            $this->migrateProfessores();

            $this->info('✅ Migração concluída com sucesso!');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro na migração: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function migrateLinhasPesquisa()
    {
        $this->info('📋 Migrando Linhas de Pesquisa...');
        
        try {
            $linhasSheets = $this->googleService->getLinhasPesquisa();
            
            foreach ($linhasSheets as $linha) {
                $existing = LinhaPesquisa::where('nome', $linha['nome'])->first();
                
                if (!$existing) {
                    LinhaPesquisa::create([
                        'nome' => $linha['nome'],
                        'descricao' => $linha['descricao']
                    ]);
                    
                    $this->line("  ✓ Linha de pesquisa criada: {$linha['nome']}");
                } else {
                    $this->line("  ℹ Linha de pesquisa já existe: {$linha['nome']}");
                }
            }
            
            $this->info("📋 Linhas de Pesquisa: " . count($linhasSheets) . " processadas");
            
        } catch (\Exception $e) {
            $this->error('Erro ao migrar linhas de pesquisa: ' . $e->getMessage());
            throw $e;
        }
    }

    private function migrateUsuarios()
    {
        $this->info('👥 Migrando Usuários...');
        
        try {
            // Usar UserService para pegar usuários do Google Sheets
            $userService = app(\App\Services\UserService::class);
            $usuariosSheets = $userService->getUsers();
            
            foreach ($usuariosSheets as $usuario) {
                $existing = User::where('email', $usuario['email'])->first();
                
                if (!$existing) {
                    User::create([
                        'name' => $usuario['name'],
                        'email' => $usuario['email'],
                        'password' => $usuario['password'] // Já está hasheado no Google Sheets
                    ]);
                    
                    $this->line("  ✓ Usuário criado: {$usuario['email']}");
                } else {
                    $this->line("  ℹ Usuário já existe: {$usuario['email']}");
                }
            }
            
            $this->info("👥 Usuários: " . count($usuariosSheets) . " processados");
            
        } catch (\Exception $e) {
            $this->error('Erro ao migrar usuários: ' . $e->getMessage());
            throw $e;
        }
    }

    private function migrateProfessores()
    {
        $this->info('👨‍🏫 Migrando Professores...');
        
        try {
            $professoresSheets = $this->googleService->getProfessores();
            
            foreach ($professoresSheets as $prof) {
                $existing = Professor::where('email', $prof['email'])->first();
                
                if (!$existing) {
                    // Buscar ou criar usuário
                    $user = User::where('email', $prof['email'])->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $prof['nome'],
                            'email' => $prof['email'],
                            'password' => Hash::make('professor123') // senha padrão
                        ]);
                    }

                    // Criar professor
                    $professor = Professor::create([
                        'user_id' => $user->id,
                        'nome' => $prof['nome'],
                        'email' => $prof['email'],
                        'telefone' => $prof['telefone'] ?? null,
                        'curso' => $prof['curso'] ?? 'Não informado',
                        'areas_interesse' => is_array($prof['areas_interesse'] ?? []) ? implode(',', $prof['areas_interesse']) : ($prof['areas_interesse'] ?? '')
                    ]);

                    // Associar linhas de pesquisa
                    if (!empty($prof['linhas_pesquisa_ids'])) {
                        foreach ($prof['linhas_pesquisa_ids'] as $linhaId) {
                            // Buscar linha de pesquisa pelo nome (já que IDs podem ser diferentes)
                            $linhasSheets = $this->googleService->getLinhasPesquisa();
                            $linhaNome = null;
                            
                            foreach ($linhasSheets as $linha) {
                                if ($linha['id'] == $linhaId) {
                                    $linhaNome = $linha['nome'];
                                    break;
                                }
                            }
                            
                            if ($linhaNome) {
                                $linhaBanco = LinhaPesquisa::where('nome', $linhaNome)->first();
                                if ($linhaBanco) {
                                    $professor->linhasPesquisa()->attach($linhaBanco->id);
                                }
                            }
                        }
                    }
                    
                    $this->line("  ✓ Professor criado: {$prof['nome']}");
                } else {
                    $this->line("  ℹ Professor já existe: {$prof['nome']}");
                }
            }
            
            $this->info("👨‍🏫 Professores: " . count($professoresSheets) . " processados");
            
        } catch (\Exception $e) {
            $this->error('Erro ao migrar professores: ' . $e->getMessage());
            throw $e;
        }
    }
}
