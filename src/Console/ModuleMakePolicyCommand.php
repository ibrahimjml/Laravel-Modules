<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakePolicyCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-policy
        {module : The module name}
        {name : The policy name}
        {--model= : The model that the policy applies to}
        {--force : Overwrite the policy if it already exists}';

    protected $description = 'Create a new policy for a module';

    protected $type = 'Policy';

    protected function getStub()
    {
        return $this->resolveStub($this->option('model') ? 'policy' : 'policy.plain');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Policies';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if (! $model = $this->option('model')) {
            return $stub;
        }

        $modelClass = $this->qualifyModuleClass($model, 'Models');
        $userModel = $this->userProviderModel() ?: 'App\Models\User';

        return str_replace(
            ['{{ namespacedModel }}', '{{ model }}', '{{ modelVariable }}', '{{ namespacedUserModel }}', '{{ user }}'],
            [$modelClass, class_basename($modelClass), lcfirst(class_basename($modelClass)), $userModel, class_basename($userModel)],
            $stub,
        );
    }
}
