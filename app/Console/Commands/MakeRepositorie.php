<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositorie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository Class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (File::exists($path)){
            $this->error('Repository {$name} already exists!');
            return;
        }

        $content = <<<EOD
        <?php

        namespace App\Repositories;

        class {$name}
        {
            // Your Repository methods go here
        }
        EOD;

        File::ensureDirectoryExists(app_path('Repositories'));
        File::put($path, $content);

        $this->info("Repository {$name} created successfully.");

    }
}
