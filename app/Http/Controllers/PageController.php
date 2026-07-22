<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'specialties' => Specialty::active()->ordered()->take(8)->get(),
            'doctors' => Doctor::active()->with('specialty')->orderBy('sort_order')->take(4)->get(),
            'articles' => Article::published()->take(3)->get(),
            'healthTip' => Specialty::active()->whereNotNull('health_tip')->inRandomOrder()->first(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function services(): View
    {
        return view('pages.services', [
            'specialties' => Specialty::active()->ordered()->withCount('activeDoctors')->get(),
        ]);
    }

    public function team(Request $request): View
    {
        $specialties = Specialty::active()->ordered()->has('doctors')->get();

        $doctors = Doctor::active()
            ->with('specialty')
            ->when($request->filled('specialite'), function ($query) use ($request) {
                $query->whereHas('specialty', fn ($q) => $q->where('slug', $request->string('specialite')));
            })
            ->orderBy('sort_order')
            ->orderBy('last_name')
            ->get();

        return view('pages.team', [
            'specialties' => $specialties,
            'doctors' => $doctors,
            'currentSpecialty' => $request->string('specialite')->toString(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function storeContact(ContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return redirect()
            ->route('contact')
            ->with('success', 'Votre message a bien été envoyé. Notre équipe vous répondra dans les meilleurs délais.');
    }
}
