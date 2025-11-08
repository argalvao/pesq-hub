<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nome' => 'Administrador',
                'email' => 'admin@pesqhub.com',
                'senha' => Hash::make('admin123'),
                'ativo' => true,
                'tipo_permissao' => 'SUPER'
            ],
            [
                'nome' => 'Dr. João Silva',
                'email' => 'joao.silva@univ.edu',
                'senha' => Hash::make('professor123'),
                'ativo' => true,
                'tipo_permissao' => 'DA'
            ],
            [
                'nome' => 'Dra. Maria Santos',
                'email' => 'maria.santos@univ.edu',
                'senha' => Hash::make('professor123'),
                'ativo' => true,
                'tipo_permissao' => 'DA'
            ],
            [
                'nome' => 'Ana Estudante',
                'email' => 'ana.estudante@gmail.com',
                'senha' => Hash::make('estudante123'),
                'ativo' => true,
                'tipo_permissao' => 'BASICO'
            ],
            [
                'nome' => 'Carlos Estudante',
                'email' => 'carlos.estudante@gmail.com',
                'senha' => Hash::make('estudante123'),
                'ativo' => true,
                'tipo_permissao' => 'BASICO'
            ],
            [
                'nome' => 'Diego Rocha',
                'email' => 'diego93rocha@gmail.com',
                'senha' => Hash::make('professor123'),
                'ativo' => true,
                'tipo_permissao' => 'DA'
            ],
            [
                'nome' => 'Dermeval Neves',
                'email' => 'dermevalneves@gmail.com',
                'senha' => Hash::make('professor123'),
                'ativo' => true,
                'tipo_permissao' => 'DA'
            ],
            [
                'nome' => 'Vinícius Fernandes',
                'email' => 'vinyfernandes10@hotmail.com',
                'senha' => Hash::make('professor123'),
                'ativo' => true,
                'tipo_permissao' => 'DA'
            ],
            [
                'nome' => 'Vasco da Gama',
                'email' => 'vasco@hotmail.com',
                'senha' => Hash::make('professor123'),
                'ativo' => true,
                'tipo_permissao' => 'DA'
            ],
            [
                'nome' => 'Estudante Teste',
                'email' => 'estudante@hotmail.com',
                'senha' => Hash::make('estudante123'),
                'ativo' => true,
                'tipo_permissao' => 'BASICO'
            ]
        ];

        foreach ($users as $userData) {
            Usuario::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'nome' => $userData['nome'],
                    'senha' => $userData['senha'],
                    'ativo' => $userData['ativo'],
                    'tipo_permissao' => $userData['tipo_permissao']
                ]
            );
        }
    }
}
