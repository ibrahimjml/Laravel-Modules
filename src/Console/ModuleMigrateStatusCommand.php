<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateStatusCommand extends Command
{
    protected $signature = 'module:migrate-status
        {module? : The module name}
        {--pending : Only list pending migrations}';

    protected $description = "Show the status of each of a module's migrations";

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
            $path = $module->path('Database/Migrations');

            if (! File::isDirectory($path)) {
                continue;
            }

            $this->components->info("Migration status for module [{$module->name}]");

            $this->call('migrate:status', [
                '--path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                '--realpath' => false,
                '--pending' => (bool) $this->option('pending'),
            ]);
        }

        return self::SUCCESS;
    }
}
