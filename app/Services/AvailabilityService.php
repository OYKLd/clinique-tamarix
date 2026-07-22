<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Horizon de réservation en ligne, en jours.
     */
    public const HORIZON_DAYS = 30;

    /**
     * Créneaux libres d'un médecin pour une date donnée.
     *
     * @return Collection<int, array{start: string, end: string}>
     */
    public function slotsForDoctorOn(Doctor $doctor, Carbon $date): Collection
    {
        if ($date->isPast() && ! $date->isToday()) {
            return collect();
        }

        if ($doctor->isAbsentOn($date)) {
            return collect();
        }

        $availabilities = $doctor->availabilities()
            ->active()
            ->where('weekday', $date->isoWeekday())
            ->get();

        if ($availabilities->isEmpty()) {
            return collect();
        }

        $booked = $doctor->appointments()
            ->active()
            ->forDate($date)
            ->pluck('start_time')
            ->map(fn (string $time) => substr($time, 0, 5))
            ->all();

        $slots = collect();

        foreach ($availabilities as $availability) {
            $cursor = $date->copy()->setTimeFromTimeString($availability->start_time);
            $end = $date->copy()->setTimeFromTimeString($availability->end_time);

            while ($cursor->copy()->addMinutes($availability->slot_duration)->lessThanOrEqualTo($end)) {
                $start = $cursor->format('H:i');

                $isFree = ! in_array($start, $booked, true)
                    && (! $date->isToday() || $cursor->isFuture());

                if ($isFree) {
                    $slots->push([
                        'start' => $start,
                        'end' => $cursor->copy()->addMinutes($availability->slot_duration)->format('H:i'),
                    ]);
                }

                $cursor->addMinutes($availability->slot_duration);
            }
        }

        return $slots->unique('start')->sortBy('start')->values();
    }

    /**
     * Créneaux libres pour une spécialité (tous médecins confondus),
     * chaque horaire étant rattaché au médecin le moins chargé ce jour-là.
     *
     * @return Collection<int, array{start: string, end: string, doctor_id: int}>
     */
    public function slotsForSpecialtyOn(Specialty $specialty, Carbon $date): Collection
    {
        $slots = collect();

        foreach ($specialty->activeDoctors()->get() as $doctor) {
            foreach ($this->slotsForDoctorOn($doctor, $date) as $slot) {
                $slots->push([...$slot, 'doctor_id' => $doctor->id]);
            }
        }

        return $slots
            ->groupBy('start')
            ->map(fn (Collection $group) => $group->first())
            ->sortKeys()
            ->values();
    }

    /**
     * Dates proposables à la réservation pour un médecin.
     *
     * @return Collection<int, array{date: string, label: string}>
     */
    public function availableDatesForDoctor(Doctor $doctor, int $horizon = self::HORIZON_DAYS): Collection
    {
        return $this->collectDates(
            fn (Carbon $date) => $this->slotsForDoctorOn($doctor, $date)->isNotEmpty(),
            $horizon,
        );
    }

    /**
     * Dates proposables pour une spécialité (premier médecin disponible).
     *
     * @return Collection<int, array{date: string, label: string}>
     */
    public function availableDatesForSpecialty(Specialty $specialty, int $horizon = self::HORIZON_DAYS): Collection
    {
        $doctors = $specialty->activeDoctors()->get();

        return $this->collectDates(
            fn (Carbon $date) => $doctors->contains(
                fn (Doctor $doctor) => $this->slotsForDoctorOn($doctor, $date)->isNotEmpty()
            ),
            $horizon,
        );
    }

    private function collectDates(callable $hasSlots, int $horizon): Collection
    {
        $dates = collect();

        for ($offset = 0; $offset <= $horizon; $offset++) {
            $date = today()->addDays($offset);

            if ($hasSlots($date)) {
                $dates->push([
                    'date' => $date->toDateString(),
                    'label' => ucfirst($date->translatedFormat('l j F Y')),
                ]);
            }
        }

        return $dates;
    }
}
