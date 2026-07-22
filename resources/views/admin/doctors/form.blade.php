@extends('layouts.admin')

@section('title', $doctor->exists ? 'Modifier — ' . $doctor->full_name : 'Nouveau médecin')

@section('content')
    <x-admin.errors />

    <div class="row g-4">
        <div class="{{ $doctor->exists ? 'col-lg-6' : 'col-lg-8 mx-auto' }}">
            <x-admin.card title="Fiche du médecin" icon="bi-person-badge">
                <form method="POST"
                      action="{{ $doctor->exists ? route('admin.doctors.update', $doctor) : route('admin.doctors.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if ($doctor->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-4 col-md-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   value="{{ old('title', $doctor->title) }}" required maxlength="20">
                        </div>
                        <div class="col-8 col-md-4">
                            <label for="first_name" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="{{ old('first_name', $doctor->first_name) }}" required maxlength="80">
                        </div>
                        <div class="col-md-5">
                            <label for="last_name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="{{ old('last_name', $doctor->last_name) }}" required maxlength="80">
                        </div>

                        <div class="col-md-8">
                            <label for="specialty_id" class="form-label">Spécialité</label>
                            <select class="form-select" id="specialty_id" name="specialty_id" required>
                                @foreach ($specialties as $specialty)
                                    <option value="{{ $specialty->id }}" @selected(old('specialty_id', $doctor->specialty_id) == $specialty->id)>
                                        {{ $specialty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', $doctor->sort_order ?? 0) }}" min="0" max="999">
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone <span class="text-muted small">(interne)</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="{{ old('phone', $doctor->phone) }}" maxlength="30">
                        </div>
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">Compte back-office <span class="text-muted small">(facultatif)</span></label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Aucun compte rattaché</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id', $doctor->user_id) == $user->id)>
                                        {{ $user->name }} — {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="bio" class="form-label">Biographie <span class="text-muted small">(affichée sur le site)</span></label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" maxlength="2000">{{ old('bio', $doctor->bio) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label for="photo" class="form-label">Photo <span class="text-muted small">(JPG/PNG/WebP, 2 Mo max)</span></label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                            @if ($doctor->photo)
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <img src="{{ asset('storage/' . $doctor->photo) }}" alt=""
                                         class="rounded" style="width:64px;height:64px;object-fit:cover;">
                                    <span class="small text-muted">Photo actuelle — choisissez un fichier pour la remplacer.</span>
                                </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', $doctor->is_active))>
                                <label class="form-check-label" for="is_active">
                                    Médecin actif <span class="text-muted small">(visible sur le site et réservable en ligne)</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-check2 me-1"></i>{{ $doctor->exists ? 'Enregistrer' : 'Créer le médecin' }}
                            </button>
                            <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </x-admin.card>
        </div>

        @if ($doctor->exists)
            <div class="col-lg-6">
                {{-- Disponibilités hebdomadaires --}}
                <x-admin.card title="Disponibilités hebdomadaires" icon="bi-clock" class="mb-4">
                    @if ($doctor->availabilities->isEmpty())
                        <p class="text-muted small">Aucune disponibilité : ce médecin n'est pas réservable en ligne.</p>
                    @else
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Jour</th><th>Horaires</th><th>Créneau</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($doctor->availabilities as $availability)
                                        <tr>
                                            <td>{{ $availability->weekdayName() }}</td>
                                            <td>{{ substr($availability->start_time, 0, 5) }} – {{ substr($availability->end_time, 0, 5) }}</td>
                                            <td>{{ $availability->slot_duration }} min</td>
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('admin.doctors.availabilities.destroy', [$doctor, $availability]) }}"
                                                      onsubmit="return confirm('Supprimer cette plage horaire ?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.doctors.availabilities.store', $doctor) }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Jour</label>
                            <select class="form-select form-select-sm" name="weekday" required>
                                @foreach (\App\Models\Availability::WEEKDAYS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small">Début</label>
                            <input type="time" class="form-control form-control-sm" name="start_time" value="08:00" required>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small">Fin</label>
                            <input type="time" class="form-control form-control-sm" name="end_time" value="12:30" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Durée créneau</label>
                            <select class="form-select form-select-sm" name="slot_duration" required>
                                @foreach ([15, 20, 30, 45, 60] as $duration)
                                    <option value="{{ $duration }}" @selected($duration === 30)>{{ $duration }} min</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-secondary w-100"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </form>
                </x-admin.card>

                {{-- Absences --}}
                <x-admin.card title="Congés, gardes et absences" icon="bi-calendar-x">
                    @if ($doctor->absences->isEmpty())
                        <p class="text-muted small">Aucune absence enregistrée.</p>
                    @else
                        <ul class="list-group list-group-flush mb-3">
                            @foreach ($doctor->absences->sortBy('start_date') as $absence)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="small">
                                        <strong>{{ $absence->start_date->format('d/m/Y') }} → {{ $absence->end_date->format('d/m/Y') }}</strong>
                                        @if ($absence->reason)<br><span class="text-muted">{{ $absence->reason }}</span>@endif
                                    </span>
                                    <form method="POST" action="{{ route('admin.doctors.absences.destroy', [$doctor, $absence]) }}"
                                          onsubmit="return confirm('Supprimer cette absence ?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <form method="POST" action="{{ route('admin.doctors.absences.store', $doctor) }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Du</label>
                            <input type="date" class="form-control form-control-sm" name="start_date" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Au</label>
                            <input type="date" class="form-control form-control-sm" name="end_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Motif</label>
                            <input type="text" class="form-control form-control-sm" name="reason" maxlength="150" placeholder="Congés, garde…">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-secondary w-100"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </form>
                </x-admin.card>
            </div>
        @endif
    </div>
@endsection
