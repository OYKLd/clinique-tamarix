<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            ['first_name' => 'Aïcha', 'last_name' => 'Koné', 'specialty' => 'Médecine générale', 'bio' => 'Médecin généraliste diplômée de l\'UFR Sciences Médicales d\'Abidjan, 12 ans d\'expérience en médecine de famille et suivi des maladies chroniques.'],
            ['first_name' => 'Jean-Marc', 'last_name' => 'Kouassi', 'specialty' => 'Médecine générale', 'bio' => 'Praticien expérimenté en médecine générale et médecine d\'urgence, ancien médecin-chef de district sanitaire.'],
            ['first_name' => 'Mariam', 'last_name' => 'Diabaté', 'specialty' => 'Gynécologie-Obstétrique', 'bio' => 'Gynécologue-obstétricienne, spécialisée dans le suivi des grossesses à risque et l\'échographie obstétricale.'],
            ['first_name' => 'Adjoua', 'last_name' => 'N\'Guessan', 'specialty' => 'Pédiatrie', 'bio' => 'Pédiatre passionnée par la santé de l\'enfant, ancienne interne des hôpitaux, référente vaccination.'],
            ['first_name' => 'Ibrahim', 'last_name' => 'Traoré', 'specialty' => 'Chirurgie générale', 'bio' => 'Chirurgien généraliste, plus de 15 ans de pratique en chirurgie viscérale et chirurgie d\'urgence.'],
            ['first_name' => 'Serge', 'last_name' => 'Yao', 'specialty' => 'Cardiologie', 'bio' => 'Cardiologue, spécialiste de l\'hypertension artérielle et de l\'insuffisance cardiaque, formé à Abidjan et à Dakar.'],
            ['first_name' => 'Fatoumata', 'last_name' => 'Bamba', 'specialty' => 'Dermatologie', 'bio' => 'Dermatologue, prise en charge des affections cutanées tropicales et de la dermatologie esthétique.'],
            ['first_name' => 'Paul', 'last_name' => 'Koffi', 'specialty' => 'Ophtalmologie', 'bio' => 'Ophtalmologue, dépistage et suivi du glaucome, chirurgie de la cataracte.'],
        ];

        foreach ($doctors as $index => $data) {
            $specialty = Specialty::where('name', $data['specialty'])->first();

            if (! $specialty) {
                continue;
            }

            $doctor = Doctor::firstOrCreate(
                ['slug' => Str::slug("dr-{$data['first_name']}-{$data['last_name']}")],
                [
                    'specialty_id' => $specialty->id,
                    'title' => 'Dr',
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'bio' => $data['bio'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );

            $this->seedAvailabilities($doctor, $index);
        }

        // Compte back-office de démonstration pour le premier médecin
        $first = Doctor::orderBy('sort_order')->first();
        if ($first && ! $first->user_id) {
            $user = User::firstOrCreate(
                ['email' => 'medecin@clinique-tamarix.ci'],
                [
                    'name' => $first->full_name,
                    'role' => UserRole::Medecin,
                    'password' => 'password',
                    'is_active' => true,
                ],
            );
            $first->update(['user_id' => $user->id]);
        }
    }

    /**
     * Planning hebdomadaire de démonstration : matin et après-midi
     * en semaine, samedi matin pour un médecin sur deux.
     */
    private function seedAvailabilities(Doctor $doctor, int $index): void
    {
        if ($doctor->availabilities()->exists()) {
            return;
        }

        foreach ([1, 2, 3, 4, 5] as $weekday) {
            $doctor->availabilities()->create([
                'weekday' => $weekday,
                'start_time' => '08:00',
                'end_time' => '12:30',
                'slot_duration' => 30,
            ]);
            $doctor->availabilities()->create([
                'weekday' => $weekday,
                'start_time' => '14:00',
                'end_time' => '17:30',
                'slot_duration' => 30,
            ]);
        }

        if ($index % 2 === 0) {
            $doctor->availabilities()->create([
                'weekday' => 6,
                'start_time' => '08:00',
                'end_time' => '13:00',
                'slot_duration' => 30,
            ]);
        }
    }
}
