<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateRollbackCommand extends Command
{
    protected $signature = 'module:migrate-rollback
        {module? : The module name}
        {--step=0 : The number of migrations to rollback}
        {--force : Force the operation to run when in production}';

    protected $description = "Rollback a module's database migrations";

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

            $this->components->info("Rolling back module [{$module->name}] migrations");

            $this->call('migrate:rollback', [
                '--path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                '--realpath' => false,
                '--step' => (int) $this->option('step'),
                '--force' => (bool) $this->option('force'),
            ]);
        }

        return self::SUCCESS;
    }
}
