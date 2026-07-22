<?php

namespace App\Exports;

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
        private readonly ?string $status = null,
        private readonly ?int $doctorId = null,
    ) {}

    public function query()
    {
        return Appointment::query()
            ->with(['patient', 'doctor', 'specialty'])
            ->whereBetween('date', [$this->from->toDateString(), $this->to->toDateString()])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->doctorId, fn ($q) => $q->where('doctor_id', $this->doctorId))
            ->orderBy('date')
            ->orderBy('start_time');
    }

    public function headings(): array
    {
        return [
            'Code de suivi', 'Date', 'Heure', 'Patient', 'Téléphone',
            'Spécialité', 'Médecin', 'Statut', 'Nouveau patient', 'Origine', 'Motif',
        ];
    }

    /** @param Appointment $appointment */
    public function map($appointment): array
    {
        return [
            $appointment->tracking_code,
            $appointment->date->format('d/m/Y'),
            substr($appointment->start_time, 0, 5),
            $appointment->patient->full_name,
            $appointment->patient->phone,
            $appointment->specialty->name,
            $appointment->doctor->full_name,
            $appointment->status->label(),
            $appointment->is_new_patient ? 'Oui' : 'Non',
            $appointment->source === 'online' ? 'Site web' : 'Accueil',
            $appointment->reason ?: '—',
        ];
    }

    public function title(): string
    {
        return 'Rendez-vous';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D537F']],
            ],
        ];
    }
}
