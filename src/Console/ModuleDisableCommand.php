<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleDisableCommand extends Command
{
    protected $signature = 'module:disable {name : The module name}';

    protected $description = 'Disable a module';

    public function handle(ModuleManager $manager): int
    {
        $module = $manager->find($this->argument('name'));

        if (! $module) {
            $this->components->error("Module [{$this->argument('name')}] does not exist.");

            return self::FAILURE;
        }

        $manager->disable($module->name);

        $this->components->info("Module [{$module->name}] disabled.");

        return self::SUCCESS;
    }
}
