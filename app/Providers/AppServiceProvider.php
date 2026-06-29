<?php

namespace App\Providers;

use App\Interfaces\ActivityLoggerInterface;
use App\Listeners\LogAuthenticationActivity;
use App\Models\Remorque;
use App\Repositories\PlanningCalendarRepository;
use App\Services\ActivityLogService;
use App\Services\LibelleMaintenanceService;
use App\Services\PDFService\PDFService;
use App\Services\PlanningCalendarService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PDFService::class, function ($app) {
            return new PDFService();
        });


        $this->app->singleton(PlanningCalendarService::class, function ($app) {
            return new PlanningCalendarService(
                $app->make(PlanningCalendarRepository::class),
                $app->make(LibelleMaintenanceService::class)
            );
        });

        // Liaison de l'interface vers l'implémentation (DIP).
        // Tout consommateur typant ActivityLoggerInterface recevra ActivityLogService.
        $this->app->bind(ActivityLoggerInterface::class, ActivityLogService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        // Production: le manifest Vite est généré dans public/build/
        // (comportement par défaut de Laravel, rien à configurer)

        // Souscription du listener d'authentification (login/logout/failed)
        Event::subscribe(LogAuthenticationActivity::class);

        // Enregistrement des Observers
        \App\Models\Salarie::observe(\App\Observers\SalarieObserver::class);

        Relation::morphMap([
            'vehicule' => \App\Models\Vehicule::class,
            'remorque' => \App\Models\Remorque::class,
        ]);
    }
}
