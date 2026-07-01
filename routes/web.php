<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InfoSocieteController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PaieController;
use App\Http\Controllers\SalarieController;
use App\Http\Controllers\SalarieHistoryController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TypeSalarieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'log_activity'])->group(function () {
    Route::get("/", [MainController::class, 'index'])->name("dashboard");

    // Journal des actions utilisateurs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_log.index');

    // Dashboards
    Route::get("/dashboard/voyage", [DashboardController::class, 'voyage'])->name("dashboard.voyage");
    Route::get("/dashboard/vehicule", [DashboardController::class, 'vehicule'])->name("dashboard.vehicule");
    Route::get("/dashboard/comptablite", [DashboardController::class, 'comptablite'])->name("dashboard.comptablite");
    Route::get("/dashboard/carburant", [DashboardController::class, 'carburant'])->name("dashboard.carburant");
    Route::get("/dashboard/pneu", [DashboardController::class, 'pneu'])->name("dashboard.pneu");
    Route::get("/dashboard/chauffeur", [DashboardController::class, 'chauffeur'])->name("dashboard.chauffeur");

    // Info société
    Route::get("/infosociete", [InfoSocieteController::class, 'index'])->name('infosociete.index');
    Route::put("/infosociete/{infosociete}", [InfoSocieteController::class, 'update'])->name('infosociete.update');

    // Sauvegardes
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/',                    [BackupController::class, 'index'])->name('index');
        Route::post('/',                   [BackupController::class, 'store'])->name('store');
        Route::get('/{backup}/download',   [BackupController::class, 'download'])->name('download');
        Route::delete('/{backup}',         [BackupController::class, 'destroy'])->name('destroy');
    });

    // Utilisateurs & Groupes
    Route::resource("user", UserController::class);
    Route::resource("/group_user", UserGroupController::class);

    // Salariés / RH
    Route::resource("/type_salarie", TypeSalarieController::class);
    Route::get("/salarie/history-global", [SalarieHistoryController::class, 'index'])->name('salarie.history_global');
    Route::get("/salarie/{salarie}/history", [SalarieController::class, 'history'])->name('salarie.history');
    Route::resource("/salarie", SalarieController::class);

    // Paie
    Route::prefix('paie')->name('paie.')->group(function () {
        Route::get('/', [PaieController::class, 'index'])->name('index');
        Route::get('/preview', [PaieController::class, 'preview'])->name('preview');
        Route::post('/generate', [PaieController::class, 'generate'])->name('generate');
        Route::put('/{paie}', [PaieController::class, 'update'])->name('update');
        Route::post('/{paie}/element', [PaieController::class, 'storeElement'])->name('element.store');
        Route::delete('/element/{element}', [PaieController::class, 'destroyElement'])->name('element.destroy');
        Route::post('/{paie}/pay', [PaieController::class, 'pay'])->name('pay');
        Route::get('/mass-print', [PaieController::class, 'massPrint'])->name('mass-print');
        Route::get('/print-list', [PaieController::class, 'printList'])->name('print-list');
        Route::get('/{paie}/print', [PaieController::class, 'print'])->name('print');
        Route::delete('/{paie}', [PaieController::class, 'destroy'])->name('destroy');
    });

    // SMS
    Route::get('/sms', [SmsController::class, 'create'])->name('sms.create');
    Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::post('/sms/send-bulk', [SmsController::class, 'sendBulk'])->name('sms.send-bulk');
    Route::get('/sms/balance', [SmsController::class, 'getBalance'])->name('sms.balance');

});
