@props(['title', 'subtitle' => null])

<section class="page-header">
    <div class="container">
        <h1 class="h2 mb-1">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mb-2 opacity-75">{{ $subtitle }}</p>
        @endif
        <nav aria-label="Fil d'Ariane">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>
    </div>
</section>
