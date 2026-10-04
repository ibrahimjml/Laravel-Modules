<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Ibrahimjml\LaravelModules\Module;
use Ibrahimjml\LaravelModules\ModuleManager;

abstract class ModuleGeneratorCommand extends GeneratorCommand
{
    private ?Module $resolvedModule = null;

    private bool $moduleResolved = false;

    public function handle(): bool
    {
        if (! $this->module()) {
            $this->components->error("Module [{$this->argument('module')}] does not exist.");

            return false;
        }

        return (bool) parent::handle();
    }

    protected function module(): ?Module
    {
        if (! $this->moduleResolved) {
            $this->resolvedModule = $this->manager()->find($this->argument('module'));
            $this->moduleResolved = true;
        }

        return $this->resolvedModule;
    }

    protected function manager(): ModuleManager
    {
        return $this->laravel->make(ModuleManager::class);
    }

    protected function rootNamespace(): string
    {
        return $this->module()->namespace().'\\';
    }

    protected function getPath($name): string
    {
        $relative = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->module()->path(str_replace('\\', '/', $relative).'.php');
    }

    protected function resolveStub(string $name): string
    {
        return $this->manager()->stub($name);
    }

    /**
     * Qualify a user-supplied class name (e.g. for --model/--event options) against
     * the current module's namespace, defaulting to the given sub-namespace.
     */
    protected function qualifyModuleClass(string $name, string $subNamespace): string
    {
        $name = str_replace('/', '\\', $name);

        if (str_starts_with($name, '\\')) {
            return trim($name, '\\');
        }

        return str_starts_with($name, $this->module()->namespace())
            ? $name
            : $this->module()->namespace()."\\{$subNamespace}\\{$name}";
    }
}
