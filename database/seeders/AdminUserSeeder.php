<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Administardor',                
            'last_name' => '',
            'id_document' => '1122334455',
            'email' => 'admin@comunidadunir.net',
            'password' => Hash::make('Unir123$*'), 
            'role' => 'admin',
        ]);
    }
}
