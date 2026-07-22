@php($user = auth()->user())

<a href="{{ route('admin.dashboard') }}" class="d-block bg-white rounded-3 p-2 text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="Clinique Tamarix" style="max-height:44px;max-width:100%;">
</a>

<nav class="nav flex-column gap-1">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>

    <span class="sidebar-heading mt-3 mb-1">Activité</span>

    @if (Route::has('admin.appointments.index'))
        <a class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}" href="{{ route('admin.appointments.index') }}">
            <i class="bi bi-calendar2-week"></i> Rendez-vous
        </a>
    @endif

    @if (Route::has('admin.calendar'))
        <a class="nav-link {{ request()->routeIs('admin.calendar') ? 'active' : '' }}" href="{{ route('admin.calendar') }}">
            <i class="bi bi-calendar3"></i> Calendrier
        </a>
    @endif

    @if (Route::has('admin.patients.index') && $user->hasRole(\App\Enums\UserRole::Accueil, \App\Enums\UserRole::Administration, \App\Enums\UserRole::Direction))
        <a class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}" href="{{ route('admin.patients.index') }}">
            <i class="bi bi-people"></i> Patients
        </a>
    @endif

    @if (Route::has('admin.contact-messages.index') && $user->hasRole(\App\Enums\UserRole::Accueil, \App\Enums\UserRole::Administration, \App\Enums\UserRole::Direction))
        <a class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}" href="{{ route('admin.contact-messages.index') }}">
            <i class="bi bi-envelope"></i> Messages
        </a>
    @endif

    @if (Route::has('admin.notifications.index') && $user->hasRole(\App\Enums\UserRole::Accueil, \App\Enums\UserRole::Administration, \App\Enums\UserRole::Direction))
        <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
            <i class="bi bi-whatsapp"></i> Notifications
        </a>
    @endif

    @if ($user->hasRole(\App\Enums\UserRole::Administration, \App\Enums\UserRole::Direction))
        <span class="sidebar-heading mt-3 mb-1">Gestion</span>

        @if (Route::has('admin.doctors.index'))
            <a class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}" href="{{ route('admin.doctors.index') }}">
                <i class="bi bi-person-badge"></i> Médecins
            </a>
        @endif

        @if (Route::has('admin.specialties.index'))
            <a class="nav-link {{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}" href="{{ route('admin.specialties.index') }}">
                <i class="bi bi-clipboard2-pulse"></i> Spécialités
            </a>
        @endif

        @if (Route::has('admin.articles.index'))
            <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">
                <i class="bi bi-journal-text"></i> Articles
            </a>
        @endif

        @if (Route::has('admin.users.index'))
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="bi bi-person-gear"></i> Utilisateurs
            </a>
        @endif

        @if (Route::has('admin.settings.edit'))
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
                <i class="bi bi-gear"></i> Paramètres
            </a>
        @endif
    @endif

    @if ($user->hasRole(\App\Enums\UserRole::Administration, \App\Enums\UserRole::Direction))
        <span class="sidebar-heading mt-3 mb-1">Pilotage</span>

        @if (Route::has('admin.stats'))
            <a class="nav-link {{ request()->routeIs('admin.stats') ? 'active' : '' }}" href="{{ route('admin.stats') }}">
                <i class="bi bi-bar-chart"></i> Statistiques
            </a>
        @endif

        @if (Route::has('admin.activity-logs.index'))
            <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
                <i class="bi bi-clock-history"></i> Journal des actions
            </a>
        @endif
    @endif
</nav>
