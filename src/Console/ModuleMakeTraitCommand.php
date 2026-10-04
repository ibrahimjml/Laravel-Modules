<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeTraitCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-trait
        {module : The module name}
        {name : The trait name}
        {--force : Overwrite the trait if it already exists}';

    protected $description = 'Create a new trait for a module';

    protected $type = 'Trait';

    protected function getStub()
    {
        return $this->resolveStub('trait');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Traits';
    }
}
