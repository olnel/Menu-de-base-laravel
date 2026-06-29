<?php

namespace App\Http\Middleware;

use App\Models\InfoSociete;
use App\Services\PlanningCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = $request->user();
        $currentRoute = $request->route()?->getName();

        // Filtrer seulement les privilèges liés à la route actuelle
        $userPrivileges = $user?->privileges ?? [];
        $acceptedRoutes = $user?->accepted_routes ?? [];

        // Vérifier si l'utilisateur a accès à la route actuelle
        $hasCurrentRouteAccess = in_array($currentRoute, $acceptedRoutes);

        $maintenanceNotifications = [];
        if (app()->bound(PlanningCalendarService::class)) {
            $maintenanceNotifications = app(PlanningCalendarService::class)->getFormattedNotifications();
        }


        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    ...$request->user()->toArray(),
                    'accepted_routes' => $request->user()->accepted_routes,
                    'is_dna' => $request->user()->is_dna
                ] : null,
                'modules' => [
                    'flotte', 'chauffeur', 'documents', 'voyage', 'carburant',
                    'stock', 'maintenance', 'client', 'reclamations', 'evaluation',
                    'gps', 'multi_sites'
                ],
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'message' => [
                "success" => Session::get("message.success"),
                "error" => Session::get("message.error")
            ],
            'flash' => [
                "data" => Session::get("data"),
            ],
            "privileges" => [
                'keys' => is_array($userPrivileges) ? array_values($userPrivileges) : [],
                'routes' => is_array($acceptedRoutes) ? array_values($acceptedRoutes) : [],
                'hasCurrentAccess' => $hasCurrentRouteAccess,
                'currentRoute' => $currentRoute,
            ],
            "maintenanceNotifications" => $maintenanceNotifications,
            'devise' => function () {
                try {
                    return InfoSociete::findOrFail(1)->devise ?? 'MGA';
                } catch (\Exception $e) {
                    return 'MGA';
                }
            },
        ];
    }
}
