<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Professor;
use App\Models\User;
use App\Models\LinhaPesquisa;

class ProfessoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professores = [
            [
                'email' => 'joao.silva@univ.edu',
                'nome' => 'Dr. João Silva',
                'telefone' => '(75) 99999-0001',
                'curso' => 'Ciência da Computação',
                'areas_interesse' => 'Inteligência Artificial,Machine Learning',
                'linhas' => ['Inteligência Artificial']
            ],
            [
                'email' => 'maria.santos@univ.edu',
                'nome' => 'Dra. Maria Santos',
                'telefone' => '(75) 99999-0002',
                'curso' => 'Engenharia de Software',
                'areas_interesse' => 'Desenvolvimento de Software,Metodologias Ágeis',
                'linhas' => ['Engenharia de Software']
            ],
            [
                'email' => 'carlos.oliveira@univ.edu',
                'nome' => 'Dr. Carlos Oliveira',
                'telefone' => '(75) 99999-0003',
                'curso' => 'Ciência da Computação',
                'areas_interesse' => 'Computação Gráfica,Realidade Virtual',
                'linhas' => ['Computação Gráfica']
            ],
            [
                'email' => 'ana.costa@univ.edu',
                'nome' => 'Dra. Ana Costa',
                'telefone' => '(75) 99999-0004',
                'curso' => 'Sistemas de Informação',
                'areas_interesse' => 'Banco de Dados,Big Data',
                'linhas' => ['Banco de Dados']
            ],
            [
                'email' => 'pedro.lima@univ.edu',
                'nome' => 'Dr. Pedro Lima',
                'telefone' => '(75) 99999-0005',
                'curso' => 'Redes de Computadores',
                'areas_interesse' => 'Segurança,Protocolos de Rede',
                'linhas' => ['Redes de Computadores']
            ]
        ];

        foreach ($professores as $profData) {
            // Buscar ou criar usuário
            $user = User::firstOrCreate(
                ['email' => $profData['email']],
                [
                    'name' => $profData['nome'],
                    'password' => \Illuminate\Support\Facades\Hash::make('professor123')
                ]
            );

            // Criar organizador
            $professor = Professor::firstOrCreate(
                ['email' => $profData['email']],
                [
                    'user_id' => $user->id,
                    'nome' => $profData['nome'],
                    'telefone' => $profData['telefone'],
                    'curso' => $profData['curso'],
                    'areas_interesse' => $profData['areas_interesse']
                ]
            );

            // Associar linhas de pesquisa
            foreach ($profData['linhas'] as $linhaNome) {
                $linha = LinhaPesquisa::where('nome', $linhaNome)->first();
                if ($linha && !$professor->linhasPesquisa->contains($linha->id)) {
                    $professor->linhasPesquisa()->attach($linha->id);
                }
            }
        }
    }
}
