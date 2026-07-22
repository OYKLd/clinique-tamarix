<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function pagesPubliques(): array
    {
        return [
            'accueil' => ['home'],
            'la clinique' => ['about'],
            'nos services' => ['services'],
            'équipe médicale' => ['team'],
            'actualités' => ['articles.index'],
            'contact' => ['contact'],
            'prise de rendez-vous' => ['appointments.create'],
            'suivi de rendez-vous' => ['appointments.track'],
            'mentions légales' => ['legal.mentions'],
            'confidentialité' => ['legal.privacy'],
        ];
    }

    /**
     * @dataProvider pagesPubliques
     */
    public function test_les_pages_publiques_repondent(string $route): void
    {
        $this->get(route($route))->assertOk();
    }

    public function test_les_en_tetes_de_securite_sont_presents(): void
    {
        $this->get(route('home'))
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_le_plan_du_site_est_genere(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<urlset', false)
            ->assertSee(route('appointments.create'), false);
    }

    public function test_les_donnees_structurees_sont_presentes(): void
    {
        $this->get(route('home'))
            ->assertSee('application/ld+json', false)
            ->assertSee('MedicalClinic', false);
    }

    public function test_le_formulaire_de_contact_enregistre_un_message(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Awa Ouattara',
            'phone' => '+225 07 00 00 00 00',
            'message' => 'Bonjour, quels sont vos horaires ?',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('contact_messages', ['name' => 'Awa Ouattara']);
    }

    public function test_le_pot_de_miel_bloque_le_spam_de_contact(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Robot',
            'message' => 'Spam',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_le_webhook_refuse_un_mauvais_jeton(): void
    {
        config(['whatsapp.webhook_verify_token' => 'jeton-attendu']);

        $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=mauvais&hub_challenge=abc')
            ->assertForbidden();

        $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=jeton-attendu&hub_challenge=abc')
            ->assertOk()
            ->assertSee('abc');
    }
}
