<?php

namespace Database\Seeders;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'role' => UserRoles::ADMIN->value,
        ]);
    }
}
