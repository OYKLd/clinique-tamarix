<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTrackingTest extends TestCase
{
    use RefreshDatabase;

    private Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        $specialty = Specialty::create(['name' => 'Pédiatrie', 'slug' => 'pediatrie', 'is_active' => true]);

        $doctor = Doctor::create([
            'specialty_id' => $specialty->id,
            'title' => 'Dr',
            'first_name' => 'Adjoua',
            'last_name' => 'N\'Guessan',
            'slug' => 'dr-adjoua-nguessan',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'first_name' => 'Aya',
            'last_name' => 'Assi',
            'phone' => '+2250712345678',
            'whatsapp_consent' => true,
        ]);

        $this->appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'date' => today()->addWeek(),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'status' => AppointmentStatus::Confirmed,
        ]);
    }

    public function test_un_patient_retrouve_son_rendez_vous(): void
    {
        $this->post(route('appointments.track.search'), [
            'phone' => '07 12 34 56 78',
            'tracking_code' => $this->appointment->tracking_code,
        ])->assertRedirect(route('appointments.track'));

        $this->get(route('appointments.track'))
            ->assertOk()
            ->assertSee($this->appointment->tracking_code)
            ->assertSee('Aya');
    }

    public function test_le_code_de_suivi_est_insensible_a_la_casse(): void
    {
        $this->post(route('appointments.track.search'), [
            'phone' => '+2250712345678',
            'tracking_code' => strtolower($this->appointment->tracking_code),
        ]);

        $this->get(route('appointments.track'))->assertSee($this->appointment->tracking_code);
    }

    public function test_un_mauvais_couple_telephone_code_est_refuse(): void
    {
        $this->post(route('appointments.track.search'), [
            'phone' => '07 99 99 99 99',
            'tracking_code' => $this->appointment->tracking_code,
        ])->assertSessionHas('error');

        // Le formulaire est repeuplé avec la saisie, mais aucune donnée
        // du rendez-vous ne doit fuiter.
        $this->get(route('appointments.track'))
            ->assertDontSee('Aya')
            ->assertDontSee('Dr Adjoua')
            ->assertSee('Retrouver mon rendez-vous');
    }

    public function test_un_patient_peut_annuler_son_rendez_vous(): void
    {
        $this->post(route('appointments.track.search'), [
            'phone' => '+2250712345678',
            'tracking_code' => $this->appointment->tracking_code,
        ]);

        $this->post(route('appointments.track.cancel'))->assertRedirect(route('appointments.track'));

        $this->appointment->refresh();

        $this->assertSame(AppointmentStatus::Cancelled, $this->appointment->status);
        $this->assertSame('patient', $this->appointment->cancelled_by);
        $this->assertNotNull($this->appointment->cancelled_at);

        $this->assertDatabaseHas('notification_logs', ['template' => 'rdv_annule']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'appointment.cancelled']);
    }

    public function test_un_rendez_vous_passe_n_est_pas_annulable(): void
    {
        $this->appointment->update(['date' => today()->subDay()]);

        $this->post(route('appointments.track.search'), [
            'phone' => '+2250712345678',
            'tracking_code' => $this->appointment->tracking_code,
        ]);

        $this->post(route('appointments.track.cancel'))->assertSessionHas('error');

        $this->assertSame(AppointmentStatus::Confirmed, $this->appointment->refresh()->status);
    }
}
