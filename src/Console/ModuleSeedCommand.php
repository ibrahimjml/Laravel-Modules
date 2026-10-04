<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleSeedCommand extends Command
{
    protected $signature = 'module:seed {module? : The module name}';

    protected $description = "Seed the database using a module's seeder";

    public function handle(ModuleManager $manager): int
    {
        $modules = $this->argument('module')
            ? collect([$manager->find($this->argument('module'))])->filter()
            : $manager->enabled();

        if ($modules->isEmpty()) {
            $this->components->error('No matching modules found.');

            return self::FAILURE;
        }

        foreach ($modules as $module) {
            $seederClass = $module->namespace().'\\Database\\Seeders\\'.$module->name.'DatabaseSeeder';

            if (! class_exists($seederClass)) {
                $this->components->warn("No seeder found for module [{$module->name}].");

                continue;
            }

            $this->components->info("Seeding module [{$module->name}]");

            $this->call('db:seed', ['--class' => $seederClass]);
        }

        return self::SUCCESS;
    }
}
