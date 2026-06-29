# Système de Journalisation des Actions Utilisateurs

Implémentation SOLID branchée sur l'architecture existante (Controller → Service → Repository, multi-tenant).

## Composants

| Couche | Fichier | Rôle |
|---|---|---|
| Migration | `database/migrations/tenant/2026_05_06_100000_create_activity_logs_table.php` | Table `activity_logs` (par tenant) |
| Modèle | `app/Models/ActivityLog.php` | Représentation Eloquent + scopes de filtre |
| Interface | `app/Interfaces/ActivityLoggerInterface.php` | Contrat (DIP) |
| Repository | `app/Repositories/ActivityLogRepository.php` | Persistence (étend `BaseRepository`) |
| Service | `app/Services/ActivityLogService.php` | Implémente `ActivityLoggerInterface`, étend `BaseService` |
| Trait | `app/Traits/LogsActivity.php` | Auto-log via Eloquent events (created/updated/deleted) |
| Middleware | `app/Http/Middleware/LogUserActivity.php` | Log des actions HTTP non-CRUD (login, exports, prints, sendMail) |
| Controller | `app/Http/Controllers/ActivityLogController.php` | Affichage du journal |
| Vue | `resources/js/Pages/ActivityLog/Index.vue` | UI Inertia |

## Principes SOLID respectés

- **S** — Chaque classe a une responsabilité unique (modèle ≠ repository ≠ service ≠ trait ≠ middleware).
- **O** — Pour brancher un nouveau module au journal, on **ajoute** simplement le trait `LogsActivity` à son modèle. Aucune modification du `ActivityLogService`.
- **L** — Toute classe implémentant `ActivityLoggerInterface` peut remplacer `ActivityLogService` (utile en tests, ou pour basculer sur Elasticsearch demain).
- **I** — `ActivityLoggerInterface` n'expose que des méthodes de log. Pas de méthodes étrangères.
- **D** — Les consommateurs typent l'**interface** (`ActivityLoggerInterface`), jamais la classe concrète. Le binding est fait dans `AppServiceProvider::register()`.

## Comment ajouter le log à un nouveau module

### Cas 1 — Auto-log Eloquent (recommandé)

Dans le modèle :

```php
use App\Traits\LogsActivity;

class MaintenanceCurative extends Basemodel
{
    use LogsActivity;

    public string $logModule = 'maintenance_curative'; // facultatif, sinon dérivé de la classe
    public array  $logExcept = ['updated_at'];          // facultatif
}
```

Les événements `created`, `updated`, `deleted` seront automatiquement journalisés avec :
- `user_id`, `user_name`, `user_email` de l'utilisateur connecté
- `module`
- `subject_type` / `subject_id`
- `old_values` / `new_values` (uniquement les champs modifiés)
- IP, user-agent, URL, méthode HTTP, route name

### Cas 2 — Log manuel depuis un Service

Pour des actions métier qui ne correspondent pas à un événement Eloquent (ex : envoi d'un email, validation d'une réservation) :

```php
use App\Interfaces\ActivityLoggerInterface;

class ReservationService extends BaseService
{
    public function __construct(
        ReservationRepository $repo,
        private readonly ActivityLoggerInterface $logger,
    ) {
        parent::__construct($repo);
    }

    public function valider($reservation): void
    {
        // ... logique métier
        $this->logger->log(
            action: 'validated',
            module: 'reservation',
            subject: $reservation,
            description: "Réservation #{$reservation->numero_reservation} validée"
        );
    }
}
```

Le binding `ActivityLoggerInterface → ActivityLogService` est déjà fait dans `AppServiceProvider`.

### Cas 3 — Log d'une action HTTP générique (consultation, export…)

Aucun code à écrire : le middleware `log_activity` est déjà appliqué au groupe `auth` dans `routes/tenant.php`.
- Toute requête `POST/PUT/PATCH/DELETE` est loguée.
- Les routes contenant `export`, `print` ou `sendMail` sont loguées.
- Pour mapper une route à un libellé d'action lisible, ajouter une entrée dans `LogUserActivity::$actionMap`.
- Pour exclure une route, l'ajouter à `$skipPatterns`.

## Consultation

Route nommée : `activity_log.index` → `/activity-logs`
Filtres disponibles : recherche libre, action, module, utilisateur, date début, date fin.

## Migration

```bash
php artisan tenants:migrate
```

La migration cible `database/migrations/tenant/`, donc elle s'applique automatiquement à tous les tenants existants et futurs (via le pipeline `TenantCreated` de `stancl/tenancy`).
