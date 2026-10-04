<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Ibrahimjml\LaravelModules\Module;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list';

    protected $description = 'List all modules';

    public function handle(ModuleManager $manager): int
    {
        $modules = $manager->all();

        if ($modules->isEmpty()) {
            $this->components->info('No modules found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Name', 'Status', 'Version', 'Path'],
            $modules->map(fn (Module $module): array => [
                $module->name,
                $manager->isEnabled($module->name) ? 'Enabled' : 'Disabled',
                $module->version(),
                str_replace(base_path().DIRECTORY_SEPARATOR, '', $module->path()),
            ])->values()->all(),
        );

        return self::SUCCESS;
    }
}
