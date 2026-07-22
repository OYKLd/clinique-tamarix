@extends('layouts.public')

@section('title', 'Actualités & Conseils santé — Clinique Tamarix')
@section('meta_description', 'Conseils santé, actualités et communiqués de la Clinique Médico-Chirurgicale Tamarix à Abidjan.')

@section('content')
    <x-page-header title="Actualités & Conseils santé" subtitle="Prévention, informations et vie de la clinique" />

    <section class="section">
        <div class="container">

            {{-- Filtre par catégorie --}}
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
                <a href="{{ route('articles.index') }}"
                   class="btn btn-sm rounded-pill px-3 {{ $currentCategory === '' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    Tout
                </a>
                @foreach (\App\Enums\ArticleCategory::cases() as $category)
                    <a href="{{ route('articles.index', ['categorie' => $category->value]) }}"
                       class="btn btn-sm rounded-pill px-3 {{ $currentCategory === $category->value ? 'btn-secondary' : 'btn-outline-secondary' }}">
                        {{ $category->label() }}
                    </a>
                @endforeach
            </div>

            @if ($articles->isEmpty())
                <p class="text-center text-muted">Aucun article publié pour le moment.</p>
            @else
                <div class="row g-4">
                    @foreach ($articles as $article)
                        <div class="col-md-6 col-lg-4">
                            <article class="card card-hover h-100 border-0 shadow-sm overflow-hidden">
                                @if ($article->image)
                                    <img src="{{ asset('storage/' . $article->image) }}" alt="" class="card-img-top" style="height:180px;object-fit:cover;">
                                @else
                                    <div class="bg-blue-soft d-flex align-items-center justify-content-center" style="height:180px;">
                                        <i class="bi bi-journal-medical display-4 text-secondary opacity-50"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <span class="badge text-bg-light text-primary mb-2">{{ $article->category->label() }}</span>
                                    <h2 class="h6">
                                        <a href="{{ route('articles.show', $article) }}" class="text-dark stretched-link text-decoration-none">
                                            {{ $article->title }}
                                        </a>
                                    </h2>
                                    <p class="small text-muted mb-0">{{ Str::limit($article->excerpt, 110) }}</p>
                                </div>
                                <div class="card-footer bg-transparent border-0 small text-muted pb-3">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at->translatedFormat('d F Y') }}
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $articles->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
@endsection
