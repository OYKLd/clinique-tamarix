@extends('layouts.public')

@section('title', $title . ' — Clinique Tamarix')

@section('content')
    <section class="page-header">
        <div class="container">
            <h1 class="h2 mb-1">{{ $title }}</h1>
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="section text-center">
        <div class="container">
            <i class="bi bi-tools display-4 text-secondary"></i>
            <p class="lead mt-3 mb-0">Cette page est en cours de construction.</p>
        </div>
    </section>
@endsection
