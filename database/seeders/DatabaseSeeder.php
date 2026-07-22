<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            SpecialtySeeder::class,
            UserSeeder::class,
            DoctorSeeder::class,
            ArticleSeeder::class,
        ]);

        // Données de démonstration uniquement hors production
        if (! app()->isProduction()) {
            $this->call(DemoAppointmentSeeder::class);
        }
    }
}
