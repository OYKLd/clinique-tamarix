<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StatisticsService
{
    public function __construct(
        private readonly AvailabilityService $availability,
    ) {}

    /**
     * Indicateurs clés sur une période (CDC §4.2).
     */
    public function keyFigures(Carbon $from, Carbon $to): array
    {
        $counts = Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pending = (int) ($counts[AppointmentStatus::Pending->value] ?? 0);
        $confirmed = (int) ($counts[AppointmentStatus::Confirmed->value] ?? 0);
        $completed = (int) ($counts[AppointmentStatus::Completed->value] ?? 0);
        $cancelled = (int) ($counts[AppointmentStatus::Cancelled->value] ?? 0);
        $total = $pending + $confirmed + $completed + $cancelled;

        return [
            'total' => $total,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'completed' => $completed,
            'cancelled' => $cancelled,
            // Taux d'annulation et taux d'honoration sur les RDV échus
            'cancellation_rate' => $total > 0 ? round($cancelled / $total * 100, 1) : 0.0,
            'completion_rate' => ($completed + $cancelled) > 0
                ? round($completed / ($completed + $cancelled) * 100, 1)
                : 0.0,
            'new_patients' => Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->where('is_new_patient', true)->count(),
            'online_share' => $total > 0
                ? round(Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    ->where('source', 'online')->count() / $total * 100, 1)
                : 0.0,
        ];
    }

    /**
     * Activité par médecin, avec taux de remplissage réel
     * (RDV occupés ÷ créneaux ouverts sur la période).
     */
    public function byDoctor(Carbon $from, Carbon $to): Collection
    {
        return Doctor::with('specialty')
            ->withCount([
                'appointments as total_count' => fn ($q) => $q->whereBetween('date', [$from->toDateString(), $to->toDateString()]),
                'appointments as completed_count' => fn ($q) => $q->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    ->where('status', AppointmentStatus::Completed),
                'appointments as cancelled_count' => fn ($q) => $q->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    ->where('status', AppointmentStatus::Cancelled),
            ])
            ->orderByDesc('total_count')
            ->get()
            ->map(function (Doctor $doctor) use ($from, $to) {
                $capacity = $this->capacityFor($doctor, $from, $to);
                $occupied = $doctor->total_count - $doctor->cancelled_count;

                $doctor->capacity = $capacity;
                $doctor->fill_rate = $capacity > 0 ? round($occupied / $capacity * 100, 1) : 0.0;

                return $doctor;
            });
    }

    /**
     * Répartition par spécialité.
     */
    public function bySpecialty(Carbon $from, Carbon $to): Collection
    {
        return Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->join('specialties', 'appointments.specialty_id', '=', 'specialties.id')
            ->selectRaw('specialties.name, COUNT(*) as total')
            ->groupBy('specialties.name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Pics d'affluence par créneau horaire (CDC §4.2).
     */
    public function byHour(Carbon $from, Carbon $to): Collection
    {
        return Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Affluence par jour de la semaine.
     */
    public function byWeekday(Carbon $from, Carbon $to): Collection
    {
        $labels = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];

        // WEEKDAY() MySQL : 0 = lundi … 6 = dimanche
        $rows = Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('WEEKDAY(date) as weekday, COUNT(*) as total')
            ->groupBy('weekday')
            ->pluck('total', 'weekday');

        return collect($labels)->map(fn (string $label, int $iso) => [
            'label' => $label,
            'total' => (int) ($rows[$iso - 1] ?? 0),
        ])->values();
    }

    /**
     * Évolution du volume de rendez-vous, jour par jour.
     */
    public function dailyTrend(Carbon $from, Carbon $to): Collection
    {
        $rows = Appointment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $trend = collect();
        $cursor = $from->copy();

        while ($cursor->lessThanOrEqualTo($to)) {
            $key = $cursor->toDateString();
            $trend->push([
                'date' => $cursor->format('d/m'),
                'total' => (int) ($rows[$key] ?? 0),
            ]);
            $cursor->addDay();
        }

        return $trend;
    }

    /**
     * Nombre total de créneaux ouverts par un médecin sur la période,
     * absences déduites — base du taux de remplissage.
     */
    private function capacityFor(Doctor $doctor, Carbon $from, Carbon $to): int
    {
        $availabilities = $doctor->availabilities()->active()->get();

        if ($availabilities->isEmpty()) {
            return 0;
        }

        $absences = $doctor->absences()->get();
        $capacity = 0;
        $cursor = $from->copy();

        while ($cursor->lessThanOrEqualTo($to)) {
            $isAbsent = $absences->contains(
                fn ($absence) => $cursor->betweenIncluded($absence->start_date, $absence->end_date)
            );

            if (! $isAbsent) {
                foreach ($availabilities->where('weekday', $cursor->isoWeekday()) as $availability) {
                    $start = Carbon::parse($availability->start_time);
                    $end = Carbon::parse($availability->end_time);
                    $capacity += (int) floor($start->diffInMinutes($end) / $availability->slot_duration);
                }
            }

            $cursor->addDay();
        }

        return $capacity;
    }
}
