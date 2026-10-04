<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeControllerCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-controller
        {module : The module name}
        {name : The controller name}
        {--plain : Generate an empty controller class}
        {--force : Overwrite the controller if it already exists}';

    protected $description = 'Create a new controller for a module';

    protected $type = 'Controller';

    protected function getStub()
    {
        return $this->resolveStub($this->option('plain') ? 'controller.plain' : 'controller');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }
}
