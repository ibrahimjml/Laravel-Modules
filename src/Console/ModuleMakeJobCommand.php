<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeJobCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-job
        {module : The module name}
        {name : The job name}
        {--sync : Generate a job that does not run on the queue}
        {--force : Overwrite the job if it already exists}';

    protected $description = 'Create a new job for a module';

    protected $type = 'Job';

    protected function getStub()
    {
        return $this->resolveStub($this->option('sync') ? 'job.sync' : 'job');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Jobs';
    }
}
