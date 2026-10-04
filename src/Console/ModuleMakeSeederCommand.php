<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeSeederCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-seeder
        {module : The module name}
        {name : The seeder name}
        {--force : Overwrite the seeder if it already exists}';

    protected $description = 'Create a new seeder for a module';

    protected $type = 'Seeder';

    protected function getStub()
    {
        return $this->resolveStub('seeder');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Database\Seeders';
    }
}
