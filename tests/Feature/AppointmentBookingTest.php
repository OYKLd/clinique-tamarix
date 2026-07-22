<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    private Specialty $specialty;

    private Doctor $doctor;

    private Carbon $date;

    protected function setUp(): void
    {
        parent::setUp();

        $this->specialty = Specialty::create([
            'name' => 'Cardiologie',
            'slug' => 'cardiologie',
            'is_active' => true,
        ]);

        $this->doctor = Doctor::create([
            'specialty_id' => $this->specialty->id,
            'title' => 'Dr',
            'first_name' => 'Serge',
            'last_name' => 'Yao',
            'slug' => 'dr-serge-yao',
            'is_active' => true,
        ]);

        // Prochain lundi, pour disposer d'un jour de consultation certain
        $this->date = today()->next(Carbon::MONDAY);

        Availability::create([
            'doctor_id' => $this->doctor->id,
            'weekday' => 1,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);
    }

    public function test_le_formulaire_de_reservation_est_accessible(): void
    {
        $this->get(route('appointments.create'))
            ->assertOk()
            ->assertSee('Prendre rendez-vous');
    }

    public function test_les_creneaux_disponibles_sont_retournes(): void
    {
        $this->getJson(route('booking.slots', [
            'specialite' => 'cardiologie',
            'medecin' => 'dr-serge-yao',
            'date' => $this->date->toDateString(),
        ]))
            ->assertOk()
            ->assertJsonPath('slots.0.start', '08:00')
            ->assertJsonCount(8, 'slots');
    }

    public function test_un_patient_peut_reserver_un_rendez_vous(): void
    {
        $response = $this->post(route('appointments.store'), $this->payload());

        $appointment = Appointment::first();

        $this->assertNotNull($appointment);
        $response->assertRedirect(route('appointments.confirmation', $appointment->tracking_code));

        $this->assertSame(AppointmentStatus::Pending, $appointment->status);
        $this->assertMatchesRegularExpression('/^TMX-\d{4}-\d{4}$/', $appointment->tracking_code);
        $this->assertSame('+2250712345678', $appointment->patient->phone);
        $this->assertTrue($appointment->patient->whatsapp_consent);
    }

    public function test_une_notification_est_journalisee_a_la_reservation(): void
    {
        $this->post(route('appointments.store'), $this->payload());

        $this->assertDatabaseHas('notification_logs', [
            'template' => 'rdv_recu',
            'recipient' => '+2250712345678',
        ]);
    }

    public function test_un_creneau_deja_reserve_n_est_plus_propose(): void
    {
        $this->post(route('appointments.store'), $this->payload());

        $this->getJson(route('booking.slots', [
            'specialite' => 'cardiologie',
            'medecin' => 'dr-serge-yao',
            'date' => $this->date->toDateString(),
        ]))
            ->assertOk()
            ->assertJsonPath('slots.0.start', '08:30')
            ->assertJsonCount(7, 'slots');
    }

    public function test_un_creneau_ne_peut_pas_etre_reserve_deux_fois(): void
    {
        $this->post(route('appointments.store'), $this->payload());

        $this->post(route('appointments.store'), $this->payload([
            'first_name' => 'Autre',
            'phone' => '+225 05 55 55 55 55',
        ]))->assertSessionHasErrors('heure');

        $this->assertSame(1, Appointment::count());
    }

    public function test_le_consentement_est_obligatoire(): void
    {
        $payload = $this->payload();
        unset($payload['whatsapp_consent']);

        $this->post(route('appointments.store'), $payload)
            ->assertSessionHasErrors('whatsapp_consent');

        $this->assertSame(0, Appointment::count());
    }

    public function test_une_date_passee_est_refusee(): void
    {
        $this->post(route('appointments.store'), $this->payload([
            'date' => today()->subDay()->toDateString(),
        ]))->assertSessionHasErrors('date');
    }

    public function test_le_pot_de_miel_bloque_les_robots(): void
    {
        $this->post(route('appointments.store'), $this->payload(['website' => 'spam']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Appointment::count());
    }

    public function test_le_premier_medecin_disponible_est_attribue(): void
    {
        $this->post(route('appointments.store'), $this->payload(['medecin' => 'any']))
            ->assertSessionHasNoErrors();

        $this->assertSame($this->doctor->id, Appointment::first()->doctor_id);
    }

    public function test_les_numeros_ivoiriens_sont_normalises(): void
    {
        $this->assertSame('+2250712345678', Patient::normalizePhone('07 12 34 56 78'));
        $this->assertSame('+2250712345678', Patient::normalizePhone('+225 07 12 34 56 78'));
        $this->assertSame('+2250712345678', Patient::normalizePhone('00225 0712345678'));
        $this->assertSame('+2250712345678', Patient::normalizePhone('225-07-12-34-56-78'));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'specialite' => 'cardiologie',
            'medecin' => 'dr-serge-yao',
            'date' => $this->date->toDateString(),
            'heure' => '08:00',
            'first_name' => 'Awa',
            'last_name' => 'Ouattara',
            'phone' => '07 12 34 56 78',
            'whatsapp_consent' => '1',
        ], $overrides);
    }
}
