<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeEventCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-event
        {module : The module name}
        {name : The event name}
        {--force : Overwrite the event if it already exists}';

    protected $description = 'Create a new event for a module';

    protected $type = 'Event';

    protected function getStub()
    {
        return $this->resolveStub('event');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Events';
    }
}
