<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Services\AppointmentNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentTrackingController extends Controller
{
    private const SESSION_KEY = 'tracked_appointment_id';

    public function __construct(
        private readonly AppointmentNotifier $notifier,
    ) {}

    /**
     * Page « Suivre / Annuler mon rendez-vous » : formulaire de recherche
     * et, si un rendez-vous a été retrouvé, son statut en temps réel.
     */
    public function show(Request $request): View
    {
        $appointment = null;

        if ($request->session()->has(self::SESSION_KEY)) {
            $appointment = Appointment::with(['doctor', 'specialty', 'patient'])
                ->find($request->session()->get(self::SESSION_KEY));
        }

        return view('appointments.track', ['appointment' => $appointment]);
    }

    /**
     * Recherche du rendez-vous par téléphone + code de suivi.
     * Les identifiants transitent en POST, jamais dans l'URL.
     */
    public function search(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'tracking_code' => ['required', 'string', 'max:20'],
        ], [], [
            'phone' => 'numéro de téléphone',
            'tracking_code' => 'code de suivi',
        ]);

        $phone = Patient::normalizePhone($validated['phone']);
        $code = strtoupper(trim($validated['tracking_code']));

        $appointment = Appointment::where('tracking_code', $code)
            ->whereHas('patient', fn ($query) => $query->where('phone', $phone))
            ->first();

        if (! $appointment) {
            return back()
                ->withInput()
                ->with('error', 'Aucun rendez-vous ne correspond à ce numéro de téléphone et ce code de suivi. Vérifiez votre saisie.');
        }

        $request->session()->put(self::SESSION_KEY, $appointment->id);

        return redirect()->route('appointments.track');
    }

    /**
     * Annulation en libre-service : libère le créneau et notifie le patient.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $appointment = Appointment::with('patient')
            ->find($request->session()->get(self::SESSION_KEY));

        if (! $appointment) {
            return redirect()->route('appointments.track')
                ->with('error', 'Session expirée. Merci de rechercher à nouveau votre rendez-vous.');
        }

        if (! $appointment->canBeCancelled()) {
            return redirect()->route('appointments.track')
                ->with('error', 'Ce rendez-vous ne peut plus être annulé en ligne. Contactez l\'accueil si besoin.');
        }

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancelled_by' => 'patient',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Annulé par le patient en libre-service',
        ]);

        ActivityLog::record('appointment.cancelled', $appointment, 'Annulation par le patient depuis la page de suivi');
        $this->notifier->cancelled($appointment);

        return redirect()->route('appointments.track')
            ->with('success', 'Votre rendez-vous a bien été annulé. Le créneau a été libéré. À très bientôt !');
    }

    /**
     * Efface le rendez-vous consulté de la session (recherche d'un autre RDV).
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('appointments.track');
    }
}
