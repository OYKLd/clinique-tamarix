<?php

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Console\Command;

/**
 * Bascule automatique des rendez-vous échus en historique (CDC §4.1).
 *
 * Les rendez-vous confirmés dont la date est passée deviennent « honorés » ;
 * ceux restés « en attente » sont clos en annulation (non confirmés à temps),
 * ce qui évite qu'ils encombrent indéfiniment la file de l'accueil.
 */
class CompletePastAppointments extends Command
{
    protected $signature = 'tamarix:cloturer-rdv-passes';

    protected $description = 'Bascule les rendez-vous échus en honorés ou annulés';

    public function handle(): int
    {
        $completed = Appointment::where('status', AppointmentStatus::Confirmed)
            ->whereDate('date', '<', today())
            ->update([
                'status' => AppointmentStatus::Completed,
                'completed_at' => now(),
            ]);

        $abandoned = Appointment::where('status', AppointmentStatus::Pending)
            ->whereDate('date', '<', today())
            ->update([
                'status' => AppointmentStatus::Cancelled,
                'cancelled_by' => 'clinic',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Demande non confirmée avant la date du rendez-vous',
            ]);

        $this->info("{$completed} rendez-vous marqué(s) honoré(s), {$abandoned} demande(s) non confirmée(s) clôturée(s).");

        return self::SUCCESS;
    }
}
