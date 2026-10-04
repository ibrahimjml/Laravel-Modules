<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeCommandCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-command
        {module : The module name}
        {name : The command class name}
        {--command=command:name : The terminal command that will be used to invoke the class}
        {--force : Overwrite the command if it already exists}';

    protected $description = 'Create a new Artisan command for a module';

    protected $type = 'Console command';

    protected function getStub()
    {
        return $this->resolveStub('console-command');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Console';
    }

    protected function buildClass($name)
    {
        return str_replace('{{ command }}', (string) $this->option('command'), parent::buildClass($name));
    }
}
