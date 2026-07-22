@extends('layouts.public')

@section('title', 'Prendre rendez-vous en ligne — Clinique Tamarix Abidjan')
@section('meta_description', 'Réservez votre consultation à la Clinique Tamarix en moins d\'une minute : choisissez votre spécialité, votre médecin et votre créneau. Confirmation par WhatsApp.')

@section('content')
    <x-page-header title="Prendre rendez-vous"
                   subtitle="Réservez en moins d'une minute — sans créer de compte" />

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('appointments.store') }}" id="bookingForm" class="card border-0 shadow-sm">
                        @csrf

                        {{-- Pot de miel anti-spam --}}
                        <div class="d-none" aria-hidden="true">
                            <label>Ne pas remplir <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="card-body p-4 p-md-5 d-grid gap-4">

                            {{-- 1. Spécialité --}}
                            <div>
                                <label for="specialite" class="form-label fw-semibold">
                                    <span class="badge rounded-pill text-bg-secondary me-1">1</span> Spécialité
                                </label>
                                <select class="form-select form-select-lg" id="specialite" name="specialite" required
                                        data-old="{{ old('specialite', $preselectedSpecialty) }}">
                                    <option value="" selected disabled>Choisissez une spécialité…</option>
                                    @foreach ($specialties as $specialty)
                                        <option value="{{ $specialty->slug }}">{{ $specialty->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Médecin --}}
                            <div>
                                <label for="medecin" class="form-label fw-semibold">
                                    <span class="badge rounded-pill text-bg-secondary me-1">2</span> Médecin
                                </label>
                                <select class="form-select form-select-lg" id="medecin" name="medecin" required disabled
                                        data-old="{{ old('medecin', $preselectedDoctor) }}">
                                    <option value="" selected disabled>Choisissez d'abord une spécialité</option>
                                </select>
                            </div>

                            {{-- 3. Date --}}
                            <div>
                                <label for="date" class="form-label fw-semibold">
                                    <span class="badge rounded-pill text-bg-secondary me-1">3</span> Date
                                </label>
                                <select class="form-select form-select-lg" id="date" name="date" required disabled
                                        data-old="{{ old('date') }}">
                                    <option value="" selected disabled>Choisissez d'abord un médecin</option>
                                </select>
                            </div>

                            {{-- 4. Créneau --}}
                            <div>
                                <label for="heure" class="form-label fw-semibold">
                                    <span class="badge rounded-pill text-bg-secondary me-1">4</span> Créneau horaire
                                </label>
                                <select class="form-select form-select-lg" id="heure" name="heure" required disabled
                                        data-old="{{ old('heure') }}">
                                    <option value="" selected disabled>Choisissez d'abord une date</option>
                                </select>
                            </div>

                            {{-- 5. Coordonnées --}}
                            <div id="patientFields" class="d-none">
                                <hr class="mb-4">
                                <p class="fw-semibold mb-3">
                                    <span class="badge rounded-pill text-bg-secondary me-1">5</span> Vos coordonnées
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                               id="first_name" name="first_name" value="{{ old('first_name') }}"
                                               required maxlength="80" autocomplete="given-name">
                                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                               id="last_name" name="last_name" value="{{ old('last_name') }}"
                                               required maxlength="80" autocomplete="family-name">
                                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="phone" class="form-label">Téléphone (WhatsApp de préférence) <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone') }}"
                                               required maxlength="30" placeholder="+225 07 00 00 00 00" autocomplete="tel">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <a class="small" data-bs-toggle="collapse" href="#reasonCollapse" role="button"
                                           aria-expanded="{{ old('reason') ? 'true' : 'false' }}" aria-controls="reasonCollapse">
                                            <i class="bi bi-plus-circle me-1"></i>Préciser un motif de consultation (facultatif)
                                        </a>
                                        <div class="collapse {{ old('reason') ? 'show' : '' }} mt-2" id="reasonCollapse">
                                            <input type="text" class="form-control" name="reason"
                                                   value="{{ old('reason') }}" maxlength="255"
                                                   placeholder="Ex. : douleur abdominale, suivi de grossesse…">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input @error('whatsapp_consent') is-invalid @enderror"
                                                   type="checkbox" id="whatsapp_consent" name="whatsapp_consent"
                                                   value="1" @checked(old('whatsapp_consent')) required>
                                            <label class="form-check-label small" for="whatsapp_consent">
                                                J'accepte de recevoir les notifications liées à mon rendez-vous
                                                (confirmation, rappel) par WhatsApp, SMS ou e-mail.
                                            </label>
                                            @error('whatsapp_consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Récapitulatif --}}
                                <div class="bg-cream rounded-3 p-3 mt-4 small" id="recap" hidden>
                                    <strong><i class="bi bi-card-checklist me-1"></i>Récapitulatif :</strong>
                                    <span id="recapText"></span>
                                </div>

                                <button type="submit" class="btn-rdv btn-lg w-100 mt-4">
                                    <i class="bi bi-check2-circle me-2"></i>Confirmer ma demande de rendez-vous
                                </button>
                                <p class="text-center text-muted small mt-3 mb-0">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Votre demande sera confirmée par notre accueil. Vous recevrez un
                                    accusé de réception immédiat avec votre code de suivi.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    const routes = {
        doctors: @json(route('booking.doctors')),
        dates: @json(route('booking.dates')),
        slots: @json(route('booking.slots')),
    };

    const selects = {
        specialite: document.getElementById('specialite'),
        medecin: document.getElementById('medecin'),
        date: document.getElementById('date'),
        heure: document.getElementById('heure'),
    };
    const patientFields = document.getElementById('patientFields');
    const recap = document.getElementById('recap');
    const recapText = document.getElementById('recapText');

    function resetSelect(select, placeholder) {
        select.innerHTML = '';
        const option = new Option(placeholder, '', true, true);
        option.disabled = true;
        select.add(option);
        select.disabled = true;
    }

    function fill(select, items, valueKey, labelKey, placeholder) {
        resetSelect(select, placeholder);
        items.forEach(item => select.add(new Option(item[labelKey], item[valueKey])));
        select.disabled = false;
    }

    function hidePatientFields() {
        patientFields.classList.add('d-none');
        recap.hidden = true;
    }

    async function fetchJson(url, params) {
        const query = new URLSearchParams(params).toString();
        const response = await fetch(url + '?' + query, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    }

    async function loadDoctors(preselect) {
        resetSelect(selects.medecin, 'Chargement…');
        resetSelect(selects.date, 'Choisissez d\'abord un médecin');
        resetSelect(selects.heure, 'Choisissez d\'abord une date');
        hidePatientFields();

        try {
            const data = await fetchJson(routes.doctors, { specialite: selects.specialite.value });
            resetSelect(selects.medecin, 'Choisissez votre médecin…');
            selects.medecin.add(new Option('Peu importe — le premier disponible', 'any'));
            data.doctors.forEach(doctor => selects.medecin.add(new Option(doctor.name, doctor.slug)));
            selects.medecin.disabled = false;

            if (preselect) {
                selects.medecin.value = preselect;
                if (selects.medecin.value === preselect) {
                    await loadDates();
                }
            }
        } catch {
            resetSelect(selects.medecin, 'Erreur de chargement — réessayez');
            selects.medecin.disabled = false;
        }
    }

    async function loadDates(preselect) {
        resetSelect(selects.date, 'Chargement…');
        resetSelect(selects.heure, 'Choisissez d\'abord une date');
        hidePatientFields();

        try {
            const data = await fetchJson(routes.dates, {
                specialite: selects.specialite.value,
                medecin: selects.medecin.value,
            });
            if (data.dates.length === 0) {
                resetSelect(selects.date, 'Aucune date disponible — appelez-nous');
                selects.date.disabled = false;
                return;
            }
            fill(selects.date, data.dates, 'date', 'label', 'Choisissez une date…');

            if (preselect) {
                selects.date.value = preselect;
                if (selects.date.value === preselect) {
                    await loadSlots();
                }
            }
        } catch {
            resetSelect(selects.date, 'Erreur de chargement — réessayez');
            selects.date.disabled = false;
        }
    }

    async function loadSlots(preselect) {
        resetSelect(selects.heure, 'Chargement…');
        hidePatientFields();

        try {
            const data = await fetchJson(routes.slots, {
                specialite: selects.specialite.value,
                medecin: selects.medecin.value,
                date: selects.date.value,
            });
            if (data.slots.length === 0) {
                resetSelect(selects.heure, 'Plus de créneau ce jour — choisissez une autre date');
                selects.heure.disabled = false;
                return;
            }
            resetSelect(selects.heure, 'Choisissez un créneau…');
            data.slots.forEach(slot => selects.heure.add(new Option(slot.start + ' – ' + slot.end, slot.start)));
            selects.heure.disabled = false;

            if (preselect) {
                selects.heure.value = preselect;
                if (selects.heure.value === preselect) {
                    showPatientFields();
                }
            }
        } catch {
            resetSelect(selects.heure, 'Erreur de chargement — réessayez');
            selects.heure.disabled = false;
        }
    }

    function showPatientFields() {
        patientFields.classList.remove('d-none');
        const specialtyLabel = selects.specialite.selectedOptions[0].text;
        const doctorLabel = selects.medecin.value === 'any'
            ? 'premier médecin disponible'
            : selects.medecin.selectedOptions[0].text;
        const dateLabel = selects.date.selectedOptions[0].text;
        recapText.textContent = ` ${specialtyLabel}, ${doctorLabel}, le ${dateLabel} à ${selects.heure.value}.`;
        recap.hidden = false;
        patientFields.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    selects.specialite.addEventListener('change', () => loadDoctors());
    selects.medecin.addEventListener('change', () => loadDates());
    selects.date.addEventListener('change', () => loadSlots());
    selects.heure.addEventListener('change', showPatientFields);

    // Pré-remplissage (lien direct ou retour de validation)
    (async function init() {
        const oldSpecialty = selects.specialite.dataset.old;
        if (!oldSpecialty) return;
        selects.specialite.value = oldSpecialty;
        if (selects.specialite.value !== oldSpecialty) return;

        const oldDoctor = selects.medecin.dataset.old || null;
        const oldDate = selects.date.dataset.old || null;
        const oldTime = selects.heure.dataset.old || null;

        await loadDoctors();
        if (oldDoctor) {
            selects.medecin.value = oldDoctor;
            if (selects.medecin.value === oldDoctor) {
                await loadDates();
                if (oldDate) {
                    selects.date.value = oldDate;
                    if (selects.date.value === oldDate) {
                        await loadSlots();
                        if (oldTime) {
                            selects.heure.value = oldTime;
                            if (selects.heure.value === oldTime) showPatientFields();
                        }
                    }
                }
            }
        }
    })();
})();
</script>
@endpush
