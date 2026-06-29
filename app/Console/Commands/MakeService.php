<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service Class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name_service = $name."Service";
        $servicePath = app_path("Services/{$name}Service.php");

        if (File::exists($servicePath)) {
            $this->error("Service {$name}Service already exists!");
            return;
        }

        // Demander si l'utilisateur veut créer un repository
        if ($this->confirm('Do you want to create a corresponding repository?', true)) {
            $repositoryName = "{$name}Repository";
            $repositoryPath = app_path("Repositories/{$repositoryName}.php");

            if (!File::exists($repositoryPath)) {
                $repositoryContent = <<<EOD
                <?php

                namespace App\Repositories;

                class {$repositoryName}
                {
                    // Your Repository methods go here
                }
                EOD;

                File::ensureDirectoryExists(app_path('Repositories'));
                File::put($repositoryPath, $repositoryContent);
                $this->info("Repository {$repositoryName} created successfully.");
            } else {
                $this->line("Repository {$repositoryName} already exists.");
            }
        }

        // Création du service
        $serviceContent = <<<EOD
        <?php

        namespace App\Services;

        class {$name_service}
        {
            // Your service methods go here
        }
        EOD;

        File::ensureDirectoryExists(app_path('Services'));
        File::put($servicePath, $serviceContent);

        $this->info("Service {$name_service} created successfully.");
    }
}
