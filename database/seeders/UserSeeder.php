<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Comptes de démonstration — mot de passe « password » à changer
     * impérativement avant la mise en production.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Direction Tamarix', 'email' => 'direction@clinique-tamarix.ci', 'role' => UserRole::Direction],
            ['name' => 'Administration Tamarix', 'email' => 'admin@clinique-tamarix.ci', 'role' => UserRole::Administration],
            ['name' => 'Accueil Tamarix', 'email' => 'accueil@clinique-tamarix.ci', 'role' => UserRole::Accueil],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => 'password',
                    'is_active' => true,
                ],
            );
        }
    }
}
