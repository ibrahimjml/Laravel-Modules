<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeServiceCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-service
        {module : The module name}
        {name : The service name}
        {--force : Overwrite the service if it already exists}';

    protected $description = 'Create a new service for a module';

    protected $type = 'Service';

    protected function getStub()
    {
        return $this->resolveStub('service');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Services';
    }
}
