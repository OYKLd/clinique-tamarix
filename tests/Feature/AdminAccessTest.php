<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_back_office_est_protege(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get('/admin/rendez-vous')->assertRedirect(route('admin.login'));
        $this->get('/admin/statistiques')->assertRedirect(route('admin.login'));
    }

    public function test_un_utilisateur_peut_se_connecter(): void
    {
        $user = User::factory()->create([
            'email' => 'accueil@clinique-tamarix.ci',
            'password' => 'motdepasse-solide-1',
            'role' => UserRole::Accueil,
            'is_active' => true,
        ]);

        $this->post(route('admin.login.attempt'), [
            'email' => 'accueil@clinique-tamarix.ci',
            'password' => 'motdepasse-solide-1',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('activity_logs', ['action' => 'auth.login']);
    }

    public function test_un_compte_desactive_ne_peut_pas_se_connecter(): void
    {
        User::factory()->inactive()->create([
            'email' => 'ancien@clinique-tamarix.ci',
            'password' => 'motdepasse-solide-1',
        ]);

        $this->post(route('admin.login.attempt'), [
            'email' => 'ancien@clinique-tamarix.ci',
            'password' => 'motdepasse-solide-1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_l_accueil_n_accede_pas_aux_statistiques(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Accueil]))
            ->get('/admin/statistiques')
            ->assertForbidden();
    }

    public function test_l_accueil_n_accede_pas_a_la_gestion_des_medecins(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Accueil]))
            ->get('/admin/medecins')
            ->assertForbidden();
    }

    public function test_un_medecin_ne_voit_que_ses_propres_rendez_vous(): void
    {
        $specialty = Specialty::create(['name' => 'ORL', 'slug' => 'orl', 'is_active' => true]);

        $user = User::factory()->create(['role' => UserRole::Medecin]);

        $mine = Doctor::create([
            'specialty_id' => $specialty->id, 'user_id' => $user->id,
            'title' => 'Dr', 'first_name' => 'Paul', 'last_name' => 'Koffi',
            'slug' => 'dr-paul-koffi', 'is_active' => true,
        ]);

        $other = Doctor::create([
            'specialty_id' => $specialty->id,
            'title' => 'Dr', 'first_name' => 'Serge', 'last_name' => 'Yao',
            'slug' => 'dr-serge-yao', 'is_active' => true,
        ]);

        $patient = Patient::create([
            'first_name' => 'Awa', 'last_name' => 'Kone',
            'phone' => '+2250700000000', 'whatsapp_consent' => true,
        ]);

        foreach ([$mine, $other] as $doctor) {
            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'specialty_id' => $specialty->id,
                'date' => today()->addDays(3),
                'start_time' => '09:00',
                'end_time' => '09:30',
                'status' => AppointmentStatus::Confirmed,
            ]);
        }

        $response = $this->actingAs($user)->get('/admin/rendez-vous')->assertOk();

        $response->assertSee('Dr Paul KOFFI');
        $response->assertDontSee('Dr Serge YAO');

        // Les fiches patients sont réservées à l'accueil et à l'administration
        $this->actingAs($user)->get('/admin/patients')->assertForbidden();
    }

    public function test_la_confirmation_declenche_la_notification(): void
    {
        $specialty = Specialty::create(['name' => 'Cardiologie', 'slug' => 'cardiologie', 'is_active' => true]);

        $doctor = Doctor::create([
            'specialty_id' => $specialty->id, 'title' => 'Dr',
            'first_name' => 'Serge', 'last_name' => 'Yao',
            'slug' => 'dr-serge-yao', 'is_active' => true,
        ]);

        $patient = Patient::create([
            'first_name' => 'Awa', 'last_name' => 'Kone',
            'phone' => '+2250700000000', 'whatsapp_consent' => true,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'date' => today()->addDays(2),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'status' => AppointmentStatus::Pending,
        ]);

        $this->actingAs(User::factory()->create(['role' => UserRole::Accueil]))
            ->post("/admin/rendez-vous/{$appointment->id}/confirmer");

        $this->assertSame(AppointmentStatus::Confirmed, $appointment->refresh()->status);
        $this->assertNotNull($appointment->confirmed_at);
        $this->assertDatabaseHas('notification_logs', ['template' => 'rdv_confirme']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'appointment.confirmed']);
    }
}
