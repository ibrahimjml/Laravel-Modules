<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeListenerCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-listener
        {module : The module name}
        {name : The listener name}
        {--event= : The event class being listened for}
        {--force : Overwrite the listener if it already exists}';

    protected $description = 'Create a new event listener for a module';

    protected $type = 'Listener';

    protected function getStub()
    {
        return $this->resolveStub($this->option('event') ? 'listener.typed' : 'listener');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Listeners';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if (! $event = $this->option('event')) {
            return $stub;
        }

        $eventClass = $this->qualifyModuleClass($event, 'Events');

        return str_replace(
            ['{{ event }}', '{{ eventNamespace }}'],
            [class_basename($eventClass), $eventClass],
            $stub,
        );
    }
}
