<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeMailCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-mail
        {module : The module name}
        {name : The mail name}
        {--force : Overwrite the mail if it already exists}';

    protected $description = 'Create a new mail for a module';

    protected $type = 'Mail';

    protected function getStub()
    {
        return $this->resolveStub('mail');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Mails';
    }
}
