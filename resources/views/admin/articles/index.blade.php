@extends('layouts.admin')

@section('title', 'Articles')

@section('content')
    <x-admin.card title="Actualités & conseils santé" icon="bi-journal-text">
        <x-slot:action>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-lg me-1"></i>Nouvel article
            </a>
        </x-slot:action>

        @if ($articles->isEmpty())
            <p class="text-muted text-center py-4 mb-0">Aucun article. Créez le premier conseil santé.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th class="d-none d-md-table-cell">Catégorie</th>
                            <th class="d-none d-lg-table-cell">Auteur</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($article->title, 60) }}</strong>
                                    @if ($article->published_at)
                                        <br><span class="small text-muted">{{ $article->published_at->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge text-bg-light">{{ $article->category->label() }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell small text-muted">{{ $article->author?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $article->is_published ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $article->is_published ? 'Publié' : 'Brouillon' }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($article->is_published)
                                        <a href="{{ route('articles.show', $article) }}" target="_blank"
                                           class="btn btn-sm btn-outline-secondary" title="Voir sur le site">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="d-inline"
                                          onsubmit="return confirm('Supprimer définitivement cet article ?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $articles->links('pagination::bootstrap-5') }}</div>
        @endif
    </x-admin.card>
@endsection
