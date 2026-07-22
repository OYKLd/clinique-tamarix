<footer class="footer pt-5">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-4">
                <div class="bg-white rounded-3 px-3 py-2 d-inline-block mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Clinique Tamarix" height="58" width="128">
                </div>
                <p class="small mb-3">
                    Clinique médico-chirurgicale moderne à Abidjan : consultations spécialisées,
                    chirurgie, imagerie, laboratoire et urgences 24h/24.
                </p>
                <div class="d-flex gap-3 fs-5">
                    @if (setting('facebook_url'))
                        <a href="{{ setting('facebook_url') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if (setting('instagram_url'))
                        <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if (setting('linkedin_url'))
                        <a href="{{ setting('linkedin_url') }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    @endif
                    <a href="{{ whatsapp_link() }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h5>Liens rapides</h5>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="{{ route('home') }}">Accueil</a></li>
                    <li><a href="{{ route('about') }}">La Clinique</a></li>
                    <li><a href="{{ route('services') }}">Nos Services</a></li>
                    <li><a href="{{ route('team') }}">Équipe médicale</a></li>
                    <li><a href="{{ route('articles.index') }}">Actualités</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h5>Rendez-vous</h5>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="{{ route('appointments.create') }}">Prendre rendez-vous</a></li>
                    <li><a href="{{ route('appointments.track') }}">Suivre / annuler mon rendez-vous</a></li>
                </ul>
                <h5 class="mt-4">Horaires</h5>
                <p class="small mb-0">{{ setting('clinic_hours') }}</p>
            </div>

            <div class="col-lg-3">
                <h5>Contact</h5>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><i class="bi bi-geo-alt me-2"></i>{{ setting('clinic_address') }}</li>
                    <li>
                        <i class="bi bi-telephone me-2"></i>
                        <a href="tel:{{ preg_replace('/\s+/', '', setting('clinic_phone', '')) }}">{{ setting('clinic_phone') }}</a>
                    </li>
                    <li>
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:{{ setting('clinic_email') }}">{{ setting('clinic_email') }}</a>
                    </li>
                    <li class="mt-2">
                        <span class="badge text-bg-danger">
                            <i class="bi bi-heart-pulse me-1"></i>Urgences 24h/24 : {{ setting('emergency_phone') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom py-3 d-flex flex-wrap justify-content-between gap-2">
            <span>© {{ date('Y') }} Clinique Médico-Chirurgicale Tamarix. Tous droits réservés.</span>
            <span>
                <a href="{{ route('legal.mentions') }}">Mentions légales</a>
                <span class="mx-2">·</span>
                <a href="{{ route('legal.privacy') }}">Politique de confidentialité</a>
            </span>
        </div>
    </div>
</footer>
