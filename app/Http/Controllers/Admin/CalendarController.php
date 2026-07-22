<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Vue calendrier : jour, semaine ou mois.
     */
    public function index(Request $request): View
    {
        $view = in_array($request->query('vue'), ['jour', 'semaine', 'mois'], true)
            ? $request->query('vue')
            : 'semaine';

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))
            : today();

        [$start, $end] = match ($view) {
            'jour' => [$date->copy(), $date->copy()],
            'semaine' => [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()],
            'mois' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
        };

        $user = $request->user();

        $appointments = Appointment::with(['patient', 'doctor', 'specialty'])
            ->when(
                $user->role === UserRole::Medecin,
                fn ($q) => $q->where('doctor_id', $user->doctor?->id ?? 0),
            )
            ->when(
                $request->filled('medecin') && $user->role !== UserRole::Medecin,
                fn ($q) => $q->where('doctor_id', $request->integer('medecin')),
            )
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Appointment $appointment) => $appointment->date->toDateString());

        return view('admin.calendar', [
            'view' => $view,
            'date' => $date,
            'start' => $start,
            'end' => $end,
            'appointmentsByDay' => $appointments,
            'doctors' => Doctor::orderBy('last_name')->get(),
            'previous' => match ($view) {
                'jour' => $date->copy()->subDay(),
                'semaine' => $date->copy()->subWeek(),
                'mois' => $date->copy()->subMonth(),
            },
            'next' => match ($view) {
                'jour' => $date->copy()->addDay(),
                'semaine' => $date->copy()->addWeek(),
                'mois' => $date->copy()->addMonth(),
            },
        ]);
    }
}
