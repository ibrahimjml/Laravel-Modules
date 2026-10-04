<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Support\Str;

class ModuleMakeModelCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-model
        {module : The module name}
        {name : The model name}
        {--m|migration : Create a migration file for the model}
        {--force : Overwrite the model if it already exists}';

    protected $description = 'Create a new model for a module';

    protected $type = 'Model';

    protected function getStub()
    {
        return $this->resolveStub('model');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }

    public function handle(): bool
    {
        if (! parent::handle()) {
            return false;
        }

        if ($this->option('migration')) {
            $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

            $this->call('module:make-migration', [
                'module' => $this->argument('module'),
                'name' => "create_{$table}_table",
                '--create' => $table,
            ]);
        }

        return true;
    }
}
