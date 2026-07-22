@extends('layouts.admin')

@section('title', $article->exists ? 'Modifier l\'article' : 'Nouvel article')

@section('content')
    <x-admin.errors />

    <form method="POST"
          action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($article->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <x-admin.card title="Contenu" icon="bi-journal-text">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title"
                               value="{{ old('title', $article->title) }}" required maxlength="180">
                    </div>
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Chapô <span class="text-muted small">(résumé affiché dans les listes)</span></label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2" maxlength="400">{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label for="content" class="form-label">
                            Contenu <span class="text-muted small">(HTML simple accepté : &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;strong&gt;)</span>
                        </label>
                        <textarea class="form-control font-monospace" id="content" name="content" rows="16" required
                                  style="font-size:0.9rem;">{{ old('content', $article->content) }}</textarea>
                    </div>
                </x-admin.card>
            </div>

            <div class="col-lg-4">
                <x-admin.card title="Publication" icon="bi-send" class="mb-4">
                    <div class="mb-3">
                        <label for="category" class="form-label">Catégorie</label>
                        <select class="form-select" id="category" name="category" required>
                            @foreach (\App\Enums\ArticleCategory::cases() as $category)
                                <option value="{{ $category->value }}"
                                    @selected(old('category', $article->category?->value) === $category->value)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="published_at" class="form-label">Date de publication</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at"
                               value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}">
                        <div class="form-text">Laissez vide pour publier immédiatement.</div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
                               @checked(old('is_published', $article->is_published))>
                        <label class="form-check-label" for="is_published">Publier sur le site</label>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image d'illustration</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                        @if ($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" alt="" class="img-fluid rounded mt-2">
                        @endif
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-check2 me-1"></i>{{ $article->exists ? 'Enregistrer' : 'Créer l\'article' }}
                        </button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </x-admin.card>

                <x-admin.card title="Référencement (SEO)" icon="bi-search">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Titre SEO <span class="text-muted small">(70 car. max)</span></label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                               value="{{ old('meta_title', $article->meta_title) }}" maxlength="70">
                    </div>
                    <div class="mb-0">
                        <label for="meta_description" class="form-label">Description SEO <span class="text-muted small">(160 car. max)</span></label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                  maxlength="160">{{ old('meta_description', $article->meta_description) }}</textarea>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>
@endsection
