<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
                'name' => 'Administrador',
                'email' => 'admin@pesqhub.com',
                'password' => Hash::make('admin123')
            ],
            [
                'name' => 'Dr. João Silva',
                'email' => 'joao.silva@univ.edu',
                'password' => Hash::make('professor123')
            ],
            [
                'name' => 'Dra. Maria Santos',
                'email' => 'maria.santos@univ.edu',
                'password' => Hash::make('professor123')
            ],
            [
                'name' => 'Ana Estudante',
                'email' => 'ana.estudante@gmail.com',
                'password' => Hash::make('estudante123')
            ],
            [
                'name' => 'Carlos Estudante',
                'email' => 'carlos.estudante@gmail.com',
                'password' => Hash::make('estudante123')
            ],
            [
                'name' => 'Diego Rocha',
                'email' => 'diego93rocha@gmail.com',
                'password' => Hash::make('professor123')
            ],
            [
                'name' => 'Dermeval Neves',
                'email' => 'dermevalneves@gmail.com',
                'password' => Hash::make('professor123')
            ],
            [
                'name' => 'Vinícius Fernandes',
                'email' => 'vinyfernandes10@hotmail.com',
                'password' => Hash::make('professor123')
            ],
            [
                'name' => 'Vasco da Gama',
                'email' => 'vasco@hotmail.com',
                'password' => Hash::make('professor123')
            ],
            [
                'name' => 'Estudante Teste',
                'email' => 'estudante@hotmail.com',
                'password' => Hash::make('estudante123')
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );
        }
    }
}
