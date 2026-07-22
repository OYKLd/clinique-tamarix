{{-- Données structurées Schema.org — visibilité dans Google (clinique + recherche locale) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'MedicalClinic',
    'name' => setting('clinic_name', 'Clinique Médico-Chirurgicale Tamarix'),
    'slogan' => setting('clinic_slogan', 'Nous plantons l\'Espérance'),
    'url' => url('/'),
    'logo' => asset('images/logo.png'),
    'image' => asset('images/hero.webp'),
    'telephone' => setting('clinic_phone'),
    'email' => setting('clinic_email'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => setting('clinic_address'),
        'addressLocality' => 'Abidjan',
        'addressCountry' => 'CI',
    ],
    'openingHours' => 'Mo-Sa 08:00-18:00',
    'availableService' => \App\Models\Specialty::active()->ordered()->get()
        ->map(fn ($specialty) => [
            '@type' => 'MedicalProcedure',
            'name' => $specialty->name,
            'description' => $specialty->description,
        ])->all(),
    'potentialAction' => [
        '@type' => 'ReserveAction',
        'name' => 'Prendre rendez-vous en ligne',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => route('appointments.create'),
            'actionPlatform' => [
                'http://schema.org/DesktopWebPlatform',
                'http://schema.org/MobileWebPlatform',
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
