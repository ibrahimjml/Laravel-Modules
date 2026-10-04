<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Ibrahimjml\LaravelModules\ModuleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleMakeMigrationCommand extends Command
{
    protected $signature = 'module:make-migration
        {module : The module name}
        {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}';

    protected $description = 'Create a new migration for a module';

    public function handle(ModuleManager $manager, Filesystem $files): int
    {
        $module = $manager->find($this->argument('module'));

        if (! $module) {
            $this->components->error("Module [{$this->argument('module')}] does not exist.");

            return self::FAILURE;
        }

        $name = Str::snake(trim($this->argument('name')));
        $table = $this->option('table');
        $create = $this->option('create') ?: false;

        if (! $table && is_string($create)) {
            $table = $create;
            $create = true;
        }

        if (! $table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        $creator = new MigrationCreator($files, config('modules.stubs'));

        $path = $creator->create($name, $module->path('Database/Migrations'), $table, $create);

        $this->components->info(sprintf('Migration [%s] created successfully.', $path));

        return self::SUCCESS;
    }
}
