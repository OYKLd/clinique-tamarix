@extends('layouts.public')

@section('title', ($article->meta_title ?: $article->title) . ' — Clinique Tamarix')
@section('meta_description', $article->meta_description ?: Str::limit(strip_tags($article->excerpt ?: $article->content), 155))

@section('content')
    <section class="page-header">
        <div class="container">
            <span class="badge text-bg-light text-primary mb-2">{{ $article->category->label() }}</span>
            <h1 class="h2 mb-2">{{ $article->title }}</h1>
            <p class="small opacity-75 mb-2">
                <i class="bi bi-calendar3 me-1"></i>Publié le {{ $article->published_at->translatedFormat('d F Y') }}
            </p>
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Actualités</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($article->title, 40) }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @if ($article->image)
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}"
                             class="img-fluid rounded-4 shadow-sm mb-4">
                    @endif
                    <article class="article-content">
                        {!! $article->content !!}
                    </article>

                    <div class="health-tip mt-5">
                        <strong><i class="bi bi-calendar2-heart me-2"></i>Besoin d'un avis médical ?</strong>
                        <p class="mb-2 mt-1">Nos médecins vous reçoivent rapidement, sur simple rendez-vous en ligne.</p>
                        <a href="{{ route('appointments.create') }}" class="fw-semibold small">
                            Prendre rendez-vous <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <aside class="col-lg-4">
                    @if ($related->isNotEmpty())
                        <h2 class="h5 mb-3">À lire aussi</h2>
                        <div class="d-grid gap-3">
                            @foreach ($related as $item)
                                <a href="{{ route('articles.show', $item) }}" class="card card-hover border-0 shadow-sm text-decoration-none">
                                    <div class="card-body">
                                        <h3 class="h6 text-dark mb-1">{{ $item->title }}</h3>
                                        <span class="small text-muted">{{ $item->published_at->translatedFormat('d F Y') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
