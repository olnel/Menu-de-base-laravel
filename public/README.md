# TransMada - Système de Gestion de Transport (Multi-tenant)

TransMada est une application de gestion de transport basée sur Laravel 11, Vue 3 (Inertia.js) et Stancl/Tenancy pour le support multi-locataire (multi-tenancy).

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP 8.2+**
- **Composer**
- **Node.js & NPM**
- **MySQL / MariaDB** (ou Laragon sur Windows recommandé)

## 🚀 Installation (Première fois)

### 1. Cloner le projet
```bash
git clone <url-du-depot>
cd TransMada
```

### 2. Installer les dépendances
```bash
composer install
npm install
```

### 3. Configurer l'environnement
Copiez le fichier `.env.example` vers `.env` :
```bash
cp .env.example .env
```
Générez la clé d'application :
```bash
php artisan key:generate
```

### 4. Configuration de la base de données
1. Créez une base de données MySQL nommée `dna_tms` (ou celle définie dans votre `.env`).
2. Mettez à jour vos identifiants `DB_USERNAME` et `DB_PASSWORD` dans le fichier `.env`.

### 5. Configuration des domaines (Hosts)
Comme il s'agit d'un projet multi-tenant, vous devez mapper les domaines locaux dans votre fichier `hosts` (C:\Windows\System32\drivers\etc\hosts sur Windows).

Ajoutez les lignes suivantes :
```text
127.0.0.1   transmada.localhost
127.0.0.1   demo.transmada.localhost
127.0.0.1   everest.transmada.localhost
```

*Note : Adaptez selon les domaines configurés dans `config/tenancy.php`.*

### 6. Migrations et Seeders

#### A. Base de données centrale
Lancez les migrations pour la base centrale :
```bash
php artisan migrate
```

#### B. Créer un Tenant (Locataire)
Vous pouvez utiliser le seeder existant pour créer les tenants `demo` et `everest` :
```bash
php artisan db:seed --class=TenantSeeder
```

Ou manuellement via Tinker :
```bash
php artisan tinker
```
Puis dans Tinker :
```php
$tenant = App\Models\Tenant::create(['id' => 'demo']);
$tenant->domains()->create(['domain' => 'demo.transmada.localhost']);
```

#### C. Migrations et Seeds des Tenants
Une fois le tenant créé, lancez les migrations et les données de test pour les tenants :
```bash
php artisan tenants:migrate
php artisan tenants:seed --class=TenantDataBaseSeeder
```

## 💻 Lancement du projet

### Frontend (Vite)
Lancez le serveur de développement pour les assets :
```bash
npm run dev
```

### Backend (Artisan)
Lancez le serveur Laravel :
```bash
php artisan serve
```
Ou si vous utilisez Laragon, assurez-vous que le serveur Apache/Nginx est démarré et pointe vers le dossier `public`.

## 🛠 Commandes utiles

- **Créer un nouveau tenant** : Utilisez Tinker ou créez une commande personnalisée.
- **Migrations des tenants** : `php artisan tenants:migrate`
- **Seeder spécifique à un tenant** : `php artisan tenants:seed --tenant=ID_DU_TENANT`
- **Exécuter une commande pour tous les tenants** : `php artisan tenants:run --all --command="nom:commande"`

## 📦 Stack Technique
- **Backend** : Laravel 11, Stancl/Tenancy
- **Frontend** : Vue 3, Inertia.js, Ant Design Vue, Tailwind CSS
- **Reporting** : DomPDF, Maatwebsite Excel
- **Image** : Intervention Image
