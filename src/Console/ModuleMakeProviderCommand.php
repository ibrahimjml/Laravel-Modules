<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeProviderCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-provider
        {module : The module name}
        {name : The service provider name}
        {--force : Overwrite the provider if it already exists}';

    protected $description = 'Create a new service provider for a module';

    protected $type = 'Provider';

    protected function getStub()
    {
        return $this->resolveStub('provider');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Providers';
    }
}
