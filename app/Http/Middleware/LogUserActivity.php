<?php

namespace App\Http\Middleware;

use App\Interfaces\ActivityLoggerInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journalise automatiquement les actions HTTP des utilisateurs authentifiés
 * (consultations, exports, impressions, envois mail) qui ne passent pas par
 * un évènement Eloquent — le trait LogsActivity couvre déjà create/update/delete.
 *
 * SRP : se contente de capter la requête et de déléguer au logger.
 * OCP : pour ignorer une route, on l'ajoute simplement à $skipPatterns.
 */
class LogUserActivity
{
    /**
     * Routes à ne pas journaliser (bruit / endpoints internes).
     */
    protected array $skipPatterns = [
        'livewire/*',
        'sanctum/*',
        '_debugbar/*',
        'horizon/*',
    ];

    /**
     * Mapping route name → action lisible. Toute route absente reste loggée
     * sous l'action "viewed" si la méthode est GET.
     */
    protected array $actionMap = [
        'login'                          => 'login',
        'logout'                         => 'logout',
        'password.update'                => 'password_changed',
        'factureclient.print'            => 'printed',
        'devisclient.print'              => 'printed',
        'article_boncommande.print'      => 'printed',
        'factureclient.sendMail'         => 'mail_sent',
        'devisclients.sendMail'          => 'mail_sent',
        'article_boncommande.sendMail'   => 'mail_sent',
    ];

    public function __construct(private readonly ActivityLoggerInterface $logger) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! auth()->check()) {
            return $response;
        }
        if ($this->shouldSkip($request)) {
            return $response;
        }
        if (! $this->shouldLog($request)) {
            return $response;
        }

        $routeName = optional($request->route())->getName();
        $action    = $this->actionMap[$routeName] ?? $this->guessAction($request);
        $module    = $this->guessModule($routeName);

        $this->logger->log(
            action: $action,
            module: $module,
            description: $this->buildDescription($action, $routeName, $request)
        );

        return $response;
    }

    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->skipPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Pour ne pas saturer la table on ne journalise par défaut que :
     *  - les écritures (POST / PUT / PATCH / DELETE)
     *  - les routes nommées explicitement dans $actionMap
     *  - les exports / prints / sendMail
     */
    protected function shouldLog(Request $request): bool
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }
        $routeName = optional($request->route())->getName();
        if ($routeName && isset($this->actionMap[$routeName])) {
            return true;
        }
        if ($routeName && (str_contains($routeName, 'export')
            || str_contains($routeName, 'print')
            || str_contains($routeName, 'sendMail'))) {
            return true;
        }
        return false;
    }

    protected function guessAction(Request $request): string
    {
        $routeName = optional($request->route())->getName() ?? '';
        if (str_contains($routeName, 'export'))   return 'exported';
        if (str_contains($routeName, 'print'))    return 'printed';
        if (str_contains($routeName, 'sendMail')) return 'mail_sent';

        return match ($request->method()) {
            'POST'   => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default  => 'viewed',
        };
    }

    protected function guessModule(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }
        $segment = explode('.', $routeName)[0] ?? null;
        return $segment ?: null;
    }

    protected function buildDescription(string $action, ?string $routeName, Request $request): string
    {
        $route = $routeName ?: $request->path();
        return ucfirst(str_replace('_', ' ', $action)) . ' [' . $route . ']';
    }
}
