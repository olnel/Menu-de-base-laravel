<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
class MakeInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:interface {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface Class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Interfaces/{$name}.php");

        if (File::exists($path)){
            $this->error('Interface {$name} already exists!');
            return;
        }

        $content = <<<EOD
        <?php

        namespace App\Interfaces;

        Interface {$name}
        {
            // Your interface methods go here
        }
        EOD;

        File::ensureDirectoryExists(app_path('Interfaces'));
        File::put($path, $content);

        $this->info("Interface {$name} created successfully.");

    }
}
