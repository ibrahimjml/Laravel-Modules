<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeEnumCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-enum
        {module : The module name}
        {name : The enum name}
        {--s|string : Generate a string backed enum}
        {--i|int : Generate an integer backed enum}
        {--force : Overwrite the enum if it already exists}';

    protected $description = 'Create a new enum for a module';

    protected $type = 'Enum';

    protected function getStub()
    {
        return $this->resolveStub($this->isBacked() ? 'enum.backed' : 'enum');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Enums';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if (! $this->isBacked()) {
            return $stub;
        }

        return str_replace('{{ type }}', $this->option('int') ? 'int' : 'string', $stub);
    }

    private function isBacked(): bool
    {
        return (bool) ($this->option('string') || $this->option('int'));
    }
}
