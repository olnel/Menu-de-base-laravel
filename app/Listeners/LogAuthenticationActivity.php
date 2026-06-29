<?php

namespace App\Listeners;

use App\Interfaces\ActivityLoggerInterface;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * Listener dédié aux événements d'authentification — login / logout / failed.
 * Le middleware `log_activity` ne couvre pas ces cas (login = utilisateur non authentifié,
 * logout = session déjà invalidée).
 *
 * SRP : un seul rôle, journaliser l'authentification.
 */
class LogAuthenticationActivity
{
    public function __construct(private readonly ActivityLoggerInterface $logger) {}

    public function handleLogin(Login $event): void
    {
        // Force la session auth en place pour que le service récupère l'user.
        auth()->setUser($event->user);
        $this->logger->log(
            action: 'login',
            module: 'auth',
            description: "Connexion utilisateur"
        );
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            auth()->setUser($event->user);
        }
        $this->logger->log(
            action: 'logout',
            module: 'auth',
            description: "Déconnexion utilisateur"
        );
    }

    public function handleFailed(Failed $event): void
    {
        $this->logger->log(
            action: 'login_failed',
            module: 'auth',
            description: "Échec de connexion: " . ($event->credentials['email'] ?? 'inconnu')
        );
    }

    public function subscribe($events): array
    {
        return [
            Login::class  => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}
