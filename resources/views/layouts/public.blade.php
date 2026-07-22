<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Clinique Médico-Chirurgicale Tamarix — Abidjan')</title>
    <meta name="description" content="@yield('meta_description', 'Clinique Médico-Chirurgicale Tamarix à Abidjan : consultations, chirurgie, urgences 24h/24 et prise de rendez-vous en ligne. « Nous plantons l\'Espérance ».')">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="Clinique Médico-Chirurgicale Tamarix">
    <meta property="og:title" content="@yield('title', 'Clinique Médico-Chirurgicale Tamarix — Abidjan')">
    <meta property="og:description" content="@yield('meta_description', 'Prenez rendez-vous en ligne avec nos médecins spécialistes à Abidjan.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Polices : Lora (titres) + Plus Jakarta Sans (texte) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    @include('partials.public.structured-data')

    @stack('head')
</head>
<body>

    @include('partials.public.navbar')

    <main>
        @include('partials.public.flash')

        @yield('content')
    </main>

    @include('partials.public.footer')

    {{-- Contact WhatsApp discret, disponible sur tout le site --}}
    <a href="{{ whatsapp_link('Bonjour, je souhaite des informations sur la Clinique Tamarix.') }}"
       class="whatsapp-float" target="_blank" rel="noopener"
       aria-label="Écrire à la clinique sur WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    @stack('scripts')
</body>
</html>
