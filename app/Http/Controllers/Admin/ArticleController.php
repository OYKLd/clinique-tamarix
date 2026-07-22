<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\ActivityLog;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        return view('admin.articles.index', [
            'articles' => Article::with('author')->latest('created_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.form', [
            'article' => new Article(['category' => ArticleCategory::ConseilSante, 'is_published' => false]),
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $article = Article::create($this->prepare($request));

        ActivityLog::record('article.created', $article, "Article « {$article->title} » créé");

        return redirect()->route('admin.articles.index')
            ->with('success', "L'article « {$article->title} » a été créé.");
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.form', ['article' => $article]);
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($this->prepare($request, $article));

        ActivityLog::record('article.updated', $article, "Article « {$article->title} » modifié");

        return redirect()->route('admin.articles.index')->with('success', 'Article mis à jour.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $title = $article->title;

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        ActivityLog::record('article.deleted', null, "Article « {$title} » supprimé");

        return back()->with('success', "L'article « {$title} » a été supprimé.");
    }

    private function prepare(ArticleRequest $request, ?Article $article = null): array
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['user_id'] = $article?->user_id ?? auth()->id();

        // Publication immédiate si aucune date n'est précisée
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = $article?->published_at ?? now();
        }

        $base = Str::slug($data['title']);
        $slug = $base;
        $suffix = 2;

        while (Article::where('slug', $slug)->when($article, fn ($q) => $q->where('id', '!=', $article->id))->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        $data['slug'] = $slug;

        if ($request->hasFile('image')) {
            if ($article?->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        } else {
            unset($data['image']);
        }

        return $data;
    }
}
