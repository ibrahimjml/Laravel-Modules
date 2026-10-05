<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeMiddlewareCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-middleware
        {module : The module name}
        {name : The middleware name}
        {--force : Overwrite the middleware if it already exists}';

    protected $description = 'Create a new middleware for a module';

    protected $type = 'Middleware';

    protected function getStub()
    {
        return $this->resolveStub('middleware');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Middleware';
    }
}
