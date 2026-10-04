<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMigrateCommand extends Command
{
    protected $signature = 'module:migrate
        {module? : The module name}
        {--seed : Seed the database after migrating}';

    protected $description = "Run a module's database migrations";

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

            $this->components->info("Migrating module [{$module->name}]");

            $this->call('migrate', [
                '--path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $path),
                '--realpath' => false,
            ]);

            if ($this->option('seed')) {
                $this->call('module:seed', ['module' => $module->name]);
            }
        }

        return self::SUCCESS;
    }
}
