<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicArticleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public — Clinique Tamarix
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/la-clinique', [PageController::class, 'about'])->name('about');
Route::get('/nos-services', [PageController::class, 'services'])->name('services');
Route::get('/equipe-medicale', [PageController::class, 'team'])->name('team');

Route::get('/actualites', [PublicArticleController::class, 'index'])->name('articles.index');
Route::get('/actualites/{article:slug}', [PublicArticleController::class, 'show'])->name('articles.show');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])
    ->middleware('throttle:5,10')
    ->name('contact.store');

/*
| Prise de rendez-vous en ligne
*/
Route::get('/prendre-rendez-vous', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('/prendre-rendez-vous', [AppointmentController::class, 'store'])
    ->middleware('throttle:10,10')
    ->name('appointments.store');
Route::get('/rendez-vous/confirmation/{trackingCode}', [AppointmentController::class, 'confirmation'])
    ->name('appointments.confirmation');

Route::prefix('api/rendez-vous')->middleware('throttle:60,1')->group(function () {
    Route::get('/medecins', [AppointmentController::class, 'doctors'])->name('booking.doctors');
    Route::get('/dates', [AppointmentController::class, 'dates'])->name('booking.dates');
    Route::get('/creneaux', [AppointmentController::class, 'slots'])->name('booking.slots');
});

/*
| Suivi / annulation en libre-service, sans compte
*/
Route::get('/suivre-mon-rendez-vous', [AppointmentTrackingController::class, 'show'])->name('appointments.track');
Route::post('/suivre-mon-rendez-vous', [AppointmentTrackingController::class, 'search'])
    ->middleware('throttle:10,10')
    ->name('appointments.track.search');
Route::post('/suivre-mon-rendez-vous/annuler', [AppointmentTrackingController::class, 'cancel'])
    ->middleware('throttle:5,10')
    ->name('appointments.track.cancel');
Route::post('/suivre-mon-rendez-vous/nouvelle-recherche', [AppointmentTrackingController::class, 'reset'])
    ->name('appointments.track.reset');

/*
|--------------------------------------------------------------------------
| Back-office — Tableau de bord
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('/connexion', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/deconnexion', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Consultation des rendez-vous : tous les rôles (le médecin ne voit que les siens)
        Route::get('/rendez-vous', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/rendez-vous/{appointment}', [App\Http\Controllers\Admin\AppointmentController::class, 'show'])
            ->whereNumber('appointment')->name('appointments.show');
        Route::get('/calendrier', [App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar');

        // Actions sur les rendez-vous : accueil, administration, direction
        Route::middleware('role:accueil,administration,direction')->group(function () {
            Route::post('/rendez-vous/{appointment}/confirmer', [App\Http\Controllers\Admin\AppointmentController::class, 'confirm'])->name('appointments.confirm');
            Route::post('/rendez-vous/{appointment}/annuler', [App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('appointments.cancel');
            Route::post('/rendez-vous/{appointment}/honorer', [App\Http\Controllers\Admin\AppointmentController::class, 'complete'])->name('appointments.complete');
            Route::post('/rendez-vous/{appointment}/reporter', [App\Http\Controllers\Admin\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');

            // Fiches patients
            Route::get('/patients', [App\Http\Controllers\Admin\PatientController::class, 'index'])->name('patients.index');
            Route::get('/patients/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'show'])->name('patients.show');
            Route::put('/patients/{patient}/notes', [App\Http\Controllers\Admin\PatientController::class, 'updateNotes'])->name('patients.notes');

            // Journal des notifications WhatsApp
            Route::get('/notifications', [App\Http\Controllers\Admin\NotificationLogController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/{notification}/relancer', [App\Http\Controllers\Admin\NotificationLogController::class, 'retry'])->name('notifications.retry');

            // Messages du formulaire de contact
            Route::get('/messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::get('/messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
            Route::delete('/messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
        });

        // Gestion du contenu et de la structure : administration et direction
        Route::middleware('role:administration,direction')->group(function () {
            // Médecins, disponibilités et absences
            Route::resource('/medecins', App\Http\Controllers\Admin\DoctorController::class)
                ->parameters(['medecins' => 'doctor'])
                ->except('show')
                ->names('doctors');
            Route::post('/medecins/{doctor}/disponibilites', [App\Http\Controllers\Admin\DoctorController::class, 'storeAvailability'])->name('doctors.availabilities.store');
            Route::delete('/medecins/{doctor}/disponibilites/{availability}', [App\Http\Controllers\Admin\DoctorController::class, 'destroyAvailability'])->name('doctors.availabilities.destroy');
            Route::post('/medecins/{doctor}/absences', [App\Http\Controllers\Admin\DoctorController::class, 'storeAbsence'])->name('doctors.absences.store');
            Route::delete('/medecins/{doctor}/absences/{absence}', [App\Http\Controllers\Admin\DoctorController::class, 'destroyAbsence'])->name('doctors.absences.destroy');

            Route::resource('/specialites', App\Http\Controllers\Admin\SpecialtyController::class)
                ->parameters(['specialites' => 'specialty'])
                ->except('show')
                ->names('specialties');

            Route::resource('/articles', App\Http\Controllers\Admin\ArticleController::class)
                ->except('show')
                ->names('articles');

            Route::resource('/utilisateurs', App\Http\Controllers\Admin\UserController::class)
                ->parameters(['utilisateurs' => 'user'])
                ->except('show')
                ->names('users');

            Route::get('/parametres', [App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
            Route::put('/parametres', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

            Route::get('/journal', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

            // Statistiques et exports pour le reporting à la direction
            Route::get('/statistiques', [App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('stats');
            Route::get('/statistiques/export-excel', [App\Http\Controllers\Admin\StatisticsController::class, 'exportExcel'])->name('stats.excel');
            Route::get('/statistiques/export-pdf', [App\Http\Controllers\Admin\StatisticsController::class, 'exportPdf'])->name('stats.pdf');
        });
    });
});

/*
| Référencement
*/
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

/*
| Webhook WhatsApp (Meta) — hors authentification et hors CSRF
*/
Route::get('/webhooks/whatsapp', [App\Http\Controllers\WhatsappWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [App\Http\Controllers\WhatsappWebhookController::class, 'handle']);

/*
| Pages légales
*/
Route::view('/mentions-legales', 'pages.legal.mentions')->name('legal.mentions');
Route::view('/politique-de-confidentialite', 'pages.legal.privacy')->name('legal.privacy');
