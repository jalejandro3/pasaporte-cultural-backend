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
            'first_name' => 'Administrador',
            'last_name' => 'Pasaporte Cultural',
            'id_document' => '',
            'email' => 'admin_pcu@comunidadunir.net',
            'email_verified_at' => now(),
            'password' => Hash::make('UnirPcu2025'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
