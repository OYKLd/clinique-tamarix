<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::published()
            ->when($request->filled('categorie'), fn ($q) => $q->where('category', $request->string('categorie')))
            ->paginate(9)
            ->withQueryString();

        return view('articles.index', [
            'articles' => $articles,
            'currentCategory' => $request->string('categorie')->toString(),
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published && $article->published_at?->isPast(), 404);

        return view('articles.show', [
            'article' => $article,
            'related' => Article::published()
                ->where('id', '!=', $article->id)
                ->where('category', $article->category)
                ->take(3)
                ->get(),
        ]);
    }
}
