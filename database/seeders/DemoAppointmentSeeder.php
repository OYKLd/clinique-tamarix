<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

/**
 * Rendez-vous fictifs pour alimenter le tableau de bord et les statistiques
 * en environnement de démonstration. Ne pas exécuter en production.
 */
class DemoAppointmentSeeder extends Seeder
{
    private array $firstNames = ['Awa', 'Moussa', 'Fanta', 'Yao', 'Adjoua', 'Sékou', 'Aya', 'Karim', 'Affoué', 'Drissa', 'Nadège', 'Ismaël', 'Rokia', 'Franck', 'Salimata'];

    private array $lastNames = ['Ouattara', 'Koné', 'Kouamé', 'Soro', 'N\'Dri', 'Cissé', 'Assi', 'Fofana', 'Gnamien', 'Touré', 'Aka', 'Sangaré', 'Brou', 'Coulibaly', 'Kacou'];

    public function run(): void
    {
        if (Appointment::exists()) {
            return;
        }

        $patients = collect($this->firstNames)->map(function (string $firstName, int $i) {
            return Patient::create([
                'first_name' => $firstName,
                'last_name' => $this->lastNames[$i],
                'phone' => sprintf('+2250%d%08d', random_int(1, 7), random_int(0, 99999999)),
                'whatsapp_consent' => true,
            ]);
        });

        $doctors = Doctor::with('availabilities')->get();

        foreach (range(-30, 14) as $offset) {
            $date = today()->addDays($offset);

            // Dimanche : clinique fermée aux consultations programmées
            if ($date->isoWeekday() === 7) {
                continue;
            }

            foreach ($doctors->random(min(3, $doctors->count())) as $doctor) {
                $availability = $doctor->availabilities
                    ->where('weekday', $date->isoWeekday())
                    ->first();

                if (! $availability) {
                    continue;
                }

                $slots = random_int(1, 3);
                for ($i = 0; $i < $slots; $i++) {
                    $start = \Illuminate\Support\Carbon::parse($availability->start_time)
                        ->addMinutes($availability->slot_duration * random_int(0, 5));

                    $status = $offset < 0
                        ? collect([AppointmentStatus::Completed, AppointmentStatus::Completed, AppointmentStatus::Completed, AppointmentStatus::Cancelled])->random()
                        : collect([AppointmentStatus::Pending, AppointmentStatus::Confirmed, AppointmentStatus::Confirmed])->random();

                    $exists = Appointment::where('doctor_id', $doctor->id)
                        ->whereDate('date', $date)
                        ->where('start_time', $start->format('H:i:s'))
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    Appointment::create([
                        'patient_id' => $patients->random()->id,
                        'doctor_id' => $doctor->id,
                        'specialty_id' => $doctor->specialty_id,
                        'date' => $date,
                        'start_time' => $start->format('H:i'),
                        'end_time' => $start->copy()->addMinutes($availability->slot_duration)->format('H:i'),
                        'status' => $status,
                        'is_new_patient' => (bool) random_int(0, 1),
                        'source' => 'online',
                        'confirmed_at' => in_array($status, [AppointmentStatus::Confirmed, AppointmentStatus::Completed], true) ? $date->copy()->subDays(2) : null,
                        'cancelled_at' => $status === AppointmentStatus::Cancelled ? $date->copy()->subDay() : null,
                        'cancelled_by' => $status === AppointmentStatus::Cancelled ? collect(['patient', 'clinic'])->random() : null,
                        'completed_at' => $status === AppointmentStatus::Completed ? $date->copy()->setTimeFromTimeString($start->format('H:i')) : null,
                    ]);
                }
            }
        }
    }
}
