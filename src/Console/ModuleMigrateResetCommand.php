<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateResetCommand extends Command
{
    protected $signature = 'module:migrate-reset
        {module? : The module name}
        {--force : Force the operation to run when in production}';

    protected $description = "Reset a module's database migrations";

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

            $this->components->info("Resetting module [{$module->name}] migrations");

            $this->call('migrate:reset', [
                '--path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                '--realpath' => false,
                '--force' => (bool) $this->option('force'),
            ]);
        }

        return self::SUCCESS;
    }
}
