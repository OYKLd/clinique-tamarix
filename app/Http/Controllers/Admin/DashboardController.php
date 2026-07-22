<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Le médecin ne voit que sa propre activité
        $scope = fn ($query) => $user->role === UserRole::Medecin && $user->doctor
            ? $query->where('doctor_id', $user->doctor->id)
            : $query;

        $today = $scope(Appointment::query())->forDate(today());

        return view('admin.dashboard', [
            'pendingCount' => $scope(Appointment::query())->upcoming()->where('status', AppointmentStatus::Pending)->count(),
            'todayCount' => (clone $today)->active()->count(),
            'todayAppointments' => (clone $today)->active()
                ->with(['patient', 'doctor', 'specialty'])
                ->orderBy('start_time')
                ->take(8)
                ->get(),
            'upcomingCount' => $scope(Appointment::query())->upcoming()->active()->count(),
            'unreadMessages' => $user->role === UserRole::Medecin ? null : ContactMessage::unread()->count(),
        ]);
    }
}
