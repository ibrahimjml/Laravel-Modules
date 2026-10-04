<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeObserverCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-observer
        {module : The module name}
        {name : The observer name}
        {--model= : The model that the observer applies to}
        {--force : Overwrite the observer if it already exists}';

    protected $description = 'Create a new observer for a module';

    protected $type = 'Observer';

    protected function getStub()
    {
        return $this->resolveStub($this->option('model') ? 'observer' : 'observer.plain');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Observers';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if (! $model = $this->option('model')) {
            return $stub;
        }

        $modelClass = $this->qualifyModuleClass($model, 'Models');

        return str_replace(
            ['{{ namespacedModel }}', '{{ model }}', '{{ modelVariable }}'],
            [$modelClass, class_basename($modelClass), lcfirst(class_basename($modelClass))],
            $stub,
        );
    }
}
