<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeRequestCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-request
        {module : The module name}
        {name : The form request name}
        {--force : Overwrite the request if it already exists}';

    protected $description = 'Create a new form request for a module';

    protected $type = 'Request';

    protected function getStub()
    {
        return $this->resolveStub('request');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Requests';
    }
}
