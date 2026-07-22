<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['is_active' => true, 'role' => UserRole::Accueil]),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $user = User::create($data);

        ActivityLog::record('user.created', $user, "Compte {$user->email} créé ({$user->role->label()})");

        return redirect()->route('admin.users.index')
            ->with('success', "Le compte de {$user->name} a été créé.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        // Le mot de passe n'est modifié que s'il est renseigné
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Un utilisateur ne peut ni se rétrograder ni se désactiver lui-même
        if ($user->id === auth()->id()) {
            $data['role'] = $user->role;
            $data['is_active'] = true;
        }

        $user->update($data);

        ActivityLog::record('user.updated', $user, "Compte {$user->email} modifié");

        return redirect()->route('admin.users.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->doctor()->exists()) {
            return back()->with('error', 'Ce compte est rattaché à une fiche médecin : détachez-la d\'abord.');
        }

        $email = $user->email;
        $user->delete();

        ActivityLog::record('user.deleted', null, "Compte {$email} supprimé");

        return back()->with('success', "Le compte {$email} a été supprimé.");
    }
}
