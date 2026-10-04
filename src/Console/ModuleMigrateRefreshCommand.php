<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateRefreshCommand extends Command
{
    protected $signature = 'module:migrate-refresh
        {module? : The module name}
        {--seed : Seed the database after refreshing}
        {--force : Force the operation to run when in production}';

    protected $description = "Rollback and re-run a module's database migrations";

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

            $this->components->info("Refreshing module [{$module->name}] migrations");

            $this->call('migrate:refresh', [
                '--path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                '--realpath' => false,
                '--force' => (bool) $this->option('force'),
            ]);

            if ($this->option('seed')) {
                $this->call('module:seed', ['module' => $module->name]);
            }
        }

        return self::SUCCESS;
    }
}
