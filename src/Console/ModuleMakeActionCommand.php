<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeActionCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-action
        {module : The module name}
        {name : The action name}
        {--force : Overwrite the action if it already exists}';

    protected $description = 'Create a new action for a module';

    protected $type = 'Action';

    protected function getStub()
    {
        return $this->resolveStub('action');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Actions';
    }
}
