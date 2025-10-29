<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LinhaPesquisa;

class LinhasPesquisaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $linhas = [
            [
                'nome' => 'Inteligência Artificial',
                'descricao' => 'Pesquisa em algoritmos de IA e machine learning'
            ],
            [
                'nome' => 'Engenharia de Software',
                'descricao' => 'Desenvolvimento de metodologias e ferramentas de software'
            ],
            [
                'nome' => 'Computação Gráfica',
                'descricao' => 'Processamento de imagens e renderização 3D'
            ],
            [
                'nome' => 'Banco de Dados',
                'descricao' => 'Otimização e design de sistemas de banco de dados'
            ],
            [
                'nome' => 'Redes de Computadores',
                'descricao' => 'Protocolos e arquiteturas de rede'
            ],
            [
                'nome' => 'Matemática',
                'descricao' => 'Equações Diferenciais e Integrais'
            ]
        ];

        foreach ($linhas as $linha) {
            LinhaPesquisa::firstOrCreate(
                ['nome' => $linha['nome']],
                ['descricao' => $linha['descricao']]
            );
        }
    }
}
