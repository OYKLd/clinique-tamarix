<?php

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Services\AppointmentNotifier;
use Illuminate\Console\Command;

/**
 * Rappel J-1 des rendez-vous confirmés (CDC §5.1).
 * Exécutée quotidiennement par le planificateur.
 */
class SendAppointmentReminders extends Command
{
    protected $signature = 'tamarix:rappels-j1';

    protected $description = 'Envoie le rappel WhatsApp aux patients ayant un rendez-vous confirmé demain';

    public function handle(AppointmentNotifier $notifier): int
    {
        $tomorrow = today()->addDay();

        $appointments = Appointment::with(['patient', 'doctor', 'specialty'])
            ->whereDate('date', $tomorrow)
            ->where('status', AppointmentStatus::Confirmed)
            // Évite un doublon si la commande est relancée le même jour
            ->whereDoesntHave('notificationLogs', fn ($query) => $query->where('template', 'rappel_j1'))
            ->get();

        foreach ($appointments as $appointment) {
            $notifier->reminder($appointment);
        }

        $this->info(sprintf(
            '%d rappel(s) mis en file pour le %s.',
            $appointments->count(),
            $tomorrow->format('d/m/Y'),
        ));

        return self::SUCCESS;
    }
}
