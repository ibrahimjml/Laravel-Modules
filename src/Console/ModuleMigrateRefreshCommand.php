<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\Database\ModuleMigrator;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateRefreshCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'module:migrate-refresh
        {module? : The module name}
        {--step=0 : The number of migrations to revert and re-run}
        {--seed : Seed the database after refreshing}
        {--force : Force the operation to run when in production}';

    protected $description = "Rollback and re-run a module's database migrations";

    public function handle(ModuleManager $manager, ModuleMigrator $migrator): int
    {
        $modules = $this->argument('module')
            ? collect([$manager->find($this->argument('module'))])->filter()
            : $manager->enabled();

        if ($modules->isEmpty()) {
            $this->components->error('No matching modules found.');

            return self::FAILURE;
        }

        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        foreach ($modules as $module) {
            $path = $module->path('Database/Migrations');

            if (! File::isDirectory($path)) {
                continue;
            }

            $this->components->info("Refreshing module [{$module->name}] migrations");

            $moduleMigrator = $migrator->scopedTo($path);

            $moduleMigrator->setOutput($this->output);

            if ((int) $this->option('step') > 0) {
                $moduleMigrator->rollback($path, [
                    'step' => (int) $this->option('step'),
                    'batch' => 0,
                ]);
            } else {
                $moduleMigrator->reset($path);
            }

            $this->call('migrate', [
                '--path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                '--realpath' => false,
                '--force' => true,
            ]);

            if ($this->option('seed')) {
                $this->call('module:seed', ['module' => $module->name]);
            }
        }

        return self::SUCCESS;
    }
}
