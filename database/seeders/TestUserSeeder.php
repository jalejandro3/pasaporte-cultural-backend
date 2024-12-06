<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario de pruebas con otro rol
        User::create([
            'first_name' => 'UsuarioPruebas',
            'last_name' => 'Demo',
            'id_document' => '2233445566',
            'email' => 'testuser@comunidadunir.net',
            'email_verified_at' => now(),
            'password' => Hash::make('Test123$*'),
            'role' => 'tester', // Cambia el rol según tus necesidades
        ]);
    }
}
