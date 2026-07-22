<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Specialty;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Plan du site XML (CDC §3.5 — référencement naturel).
     */
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('appointments.create'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => route('services'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('team'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('about'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('articles.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => route('contact'), 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => route('appointments.track'), 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => route('legal.mentions'), 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['loc' => route('legal.privacy'), 'priority' => '0.2', 'changefreq' => 'yearly'],
        ];

        foreach (Article::published()->get() as $article) {
            $urls[] = [
                'loc' => route('articles.show', $article),
                'lastmod' => $article->updated_at->toAtomString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ];
        }

        // Pages de réservation pré-filtrées par spécialité : utiles au référencement local
        foreach (Specialty::active()->has('activeDoctors')->get() as $specialty) {
            $urls[] = [
                'loc' => route('appointments.create', ['specialite' => $specialty->slug]),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
