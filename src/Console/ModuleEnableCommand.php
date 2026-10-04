<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleEnableCommand extends Command
{
    protected $signature = 'module:enable {name : The module name}';

    protected $description = 'Enable a module';

    public function handle(ModuleManager $manager): int
    {
        $module = $manager->find($this->argument('name'));

        if (! $module) {
            $this->components->error("Module [{$this->argument('name')}] does not exist.");

            return self::FAILURE;
        }

        $manager->enable($module->name);

        $this->components->info("Module [{$module->name}] enabled.");

        return self::SUCCESS;
    }
}
