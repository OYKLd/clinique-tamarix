<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Journal d'audit : traçabilité des actions (CDC §4.2).
     */
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('utilisateur'), fn (Builder $q) => $q->where('user_id', $request->integer('utilisateur')))
            ->when($request->filled('action'), fn (Builder $q) => $q->where('action', 'like', $request->string('action') . '%'))
            ->when($request->filled('du'), fn (Builder $q) => $q->whereDate('created_at', '>=', $request->date('du')))
            ->when($request->filled('au'), fn (Builder $q) => $q->whereDate('created_at', '<=', $request->date('au')))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'users' => User::orderBy('name')->get(),
            'actions' => ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
