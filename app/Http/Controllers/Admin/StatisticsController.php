<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AppointmentsExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Doctor;
use App\Services\StatisticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly StatisticsService $stats,
    ) {}

    public function index(Request $request): View
    {
        [$from, $to] = $this->resolvePeriod($request);

        return view('admin.statistics.index', [
            'from' => $from,
            'to' => $to,
            'figures' => $this->stats->keyFigures($from, $to),
            'byDoctor' => $this->stats->byDoctor($from, $to),
            'bySpecialty' => $this->stats->bySpecialty($from, $to),
            'byHour' => $this->stats->byHour($from, $to),
            'byWeekday' => $this->stats->byWeekday($from, $to),
            'trend' => $this->stats->dailyTrend($from, $to),
            'doctors' => Doctor::orderBy('last_name')->get(),
        ]);
    }

    /**
     * Export Excel des rendez-vous de la période.
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->resolvePeriod($request);

        ActivityLog::record('export.excel', null, "Export Excel des RDV du {$from->format('d/m/Y')} au {$to->format('d/m/Y')}");

        $filename = sprintf('tamarix-rendez-vous-%s-%s.xlsx', $from->format('Ymd'), $to->format('Ymd'));

        return Excel::download(
            new AppointmentsExport($from, $to, $request->query('statut'), $request->integer('medecin') ?: null),
            $filename,
        );
    }

    /**
     * Rapport PDF de synthèse pour la direction.
     */
    public function exportPdf(Request $request): Response
    {
        [$from, $to] = $this->resolvePeriod($request);

        ActivityLog::record('export.pdf', null, "Rapport PDF du {$from->format('d/m/Y')} au {$to->format('d/m/Y')}");

        $pdf = Pdf::loadView('admin.statistics.pdf', [
            'from' => $from,
            'to' => $to,
            'figures' => $this->stats->keyFigures($from, $to),
            'byDoctor' => $this->stats->byDoctor($from, $to),
            'bySpecialty' => $this->stats->bySpecialty($from, $to),
            'byHour' => $this->stats->byHour($from, $to),
            'generatedBy' => $request->user()->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(sprintf('tamarix-rapport-%s-%s.pdf', $from->format('Ymd'), $to->format('Ymd')));
    }

    /**
     * Période analysée : 30 derniers jours par défaut.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolvePeriod(Request $request): array
    {
        $from = $request->filled('du')
            ? Carbon::parse($request->query('du'))->startOfDay()
            : today()->subDays(29);

        $to = $request->filled('au')
            ? Carbon::parse($request->query('au'))->startOfDay()
            : today();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
