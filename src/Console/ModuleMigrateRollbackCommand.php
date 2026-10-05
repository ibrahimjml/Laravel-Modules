<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\Database\ModuleMigrator;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateRollbackCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'module:migrate-rollback
        {module? : The module name}
        {--step=0 : The number of migrations to rollback}
        {--batch=0 : The batch of migrations to rollback}
        {--pretend : Dump the SQL queries that would be run}
        {--force : Force the operation to run when in production}';

    protected $description = "Rollback a module's database migrations";

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

            $this->components->info("Rolling back module [{$module->name}] migrations");

            $moduleMigrator = $migrator->scopedTo($path);

            $moduleMigrator->setOutput($this->output);

            $moduleMigrator->rollback($path, [
                'pretend' => (bool) $this->option('pretend'),
                'step' => (int) $this->option('step'),
                'batch' => (int) $this->option('batch'),
            ]);
        }

        return self::SUCCESS;
    }
}
