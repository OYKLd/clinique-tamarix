<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Tableau de bord') — Clinique Tamarix</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="admin-body">

    <div class="d-flex">
        {{-- Barre latérale (fixe sur grand écran, offcanvas sur mobile) --}}
        <aside class="admin-sidebar d-none d-lg-flex flex-column flex-shrink-0 p-3">
            @include('partials.admin.sidebar')
        </aside>

        <div class="offcanvas offcanvas-start admin-sidebar p-3" tabindex="-1" id="adminSidebar" style="max-width:250px;">
            <div class="offcanvas-header p-0 pb-2 justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
            </div>
            @include('partials.admin.sidebar')
        </div>

        {{-- Contenu principal --}}
        <div class="flex-grow-1 min-vh-100 d-flex flex-column" style="min-width:0;">
            <header class="admin-topbar d-flex align-items-center justify-content-between px-3 px-md-4 py-2">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary d-lg-none" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-label="Ouvrir le menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="h5 mb-0">@yield('title', 'Tableau de bord')</h1>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5 text-secondary"></i>
                        <span class="d-none d-md-inline">
                            {{ auth()->user()->name }}
                            <small class="text-muted">({{ auth()->user()->role->label() }})</small>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                                <i class="bi bi-globe me-2"></i>Voir le site
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Se déconnecter
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="p-3 p-md-4 flex-grow-1">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
