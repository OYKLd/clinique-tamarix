<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotificationStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendAppointmentNotification;
use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    /**
     * Journal des envois : statut délivré / lu / échec (CDC §5.2).
     */
    public function index(Request $request): View
    {
        $logs = NotificationLog::with(['patient', 'appointment'])
            ->when($request->filled('statut'), fn (Builder $q) => $q->where('status', $request->string('statut')))
            ->when($request->filled('modele'), fn (Builder $q) => $q->where('template', $request->string('modele')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.notifications.index', [
            'logs' => $logs,
            'counts' => NotificationLog::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'isLive' => app(\App\Services\Whatsapp\WhatsappClient::class)->isLive(),
        ]);
    }

    /**
     * Relance manuelle d'un envoi en échec.
     */
    public function retry(NotificationLog $notification): RedirectResponse
    {
        if ($notification->status !== NotificationStatus::Failed) {
            return back()->with('error', 'Seuls les envois en échec peuvent être relancés.');
        }

        $notification->update([
            'status' => NotificationStatus::Queued,
            'error' => null,
        ]);

        SendAppointmentNotification::dispatch($notification->id);

        return back()->with('success', 'Envoi relancé.');
    }
}
