<?php

namespace Ibrahimjml\LaravelModules;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Ibrahimjml\LaravelModules\Console\ModuleDisableCommand;
use Ibrahimjml\LaravelModules\Console\ModuleEnableCommand;
use Ibrahimjml\LaravelModules\Console\ModuleListCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeCommandCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeControllerCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeEnumCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeEventCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeJobCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeListenerCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeMailCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeMigrationCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeModelCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeNotificationCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeObserverCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakePolicyCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeProviderCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeRequestCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeSeederCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeServiceCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMakeTraitCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMigrateCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMigrateRefreshCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMigrateResetCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMigrateRollbackCommand;
use Ibrahimjml\LaravelModules\Console\ModuleMigrateStatusCommand;
use Ibrahimjml\LaravelModules\Console\ModuleSeedCommand;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string>
     */
    private array $commands = [
        ModuleMakeCommand::class,
        ModuleMakeControllerCommand::class,
        ModuleMakeModelCommand::class,
        ModuleMakeRequestCommand::class,
        ModuleMakeProviderCommand::class,
        ModuleMakeSeederCommand::class,
        ModuleMakeCommandCommand::class,
        ModuleMakeMigrationCommand::class,
        ModuleMakeServiceCommand::class,
        ModuleMakeTraitCommand::class,
        ModuleMakeEventCommand::class,
        ModuleMakeListenerCommand::class,
        ModuleMakeEnumCommand::class,
        ModuleMakeJobCommand::class,
        ModuleMakeNotificationCommand::class,
        ModuleMakeMailCommand::class,
        ModuleMakeObserverCommand::class,
        ModuleMakePolicyCommand::class,
        ModuleListCommand::class,
        ModuleEnableCommand::class,
        ModuleDisableCommand::class,
        ModuleMigrateCommand::class,
        ModuleMigrateRollbackCommand::class,
        ModuleMigrateResetCommand::class,
        ModuleMigrateRefreshCommand::class,
        ModuleMigrateStatusCommand::class,
        ModuleSeedCommand::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/modules.php', 'modules');

        $this->app->singleton(ModuleManager::class, fn (): ModuleManager => new ModuleManager(
            config('modules.path'),
            config('modules.statuses_file'),
        ));

        foreach ($this->manager()->enabled() as $module) {
            $provider = $module->providerClass();

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    public function boot(): void
    {
        foreach ($this->manager()->enabled() as $module) {
            $this->loadModuleRoutes($module);
            $this->loadModuleResources($module);
        }

        $this->registerBladeDirectives();

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
            $this->registerPublishing();
        }
    }

    private function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/modules.php' => config_path('modules.php'),
        ], 'modules-config');

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs/modules'),
        ], 'modules-stubs');

        $this->publishes([
            __DIR__.'/../resources/js/vite-module.js' => base_path('vite-module.js'),
        ], 'modules-vite');
    }

    /**
     * Registers "@moduleVite('Blog')" to render a module's own JS/CSS entry points.
     */
    private function registerBladeDirectives(): void
    {
        Blade::directive('moduleVite', function (string $expression): string {
            return "<?php echo app(\Illuminate\Foundation\Vite::class)("
                ."app(\Ibrahimjml\LaravelModules\ModuleManager::class)->find({$expression})?->viteEntries() ?? []"
                ."); ?>";
        });
    }

    private function manager(): ModuleManager
    {
        return $this->app->make(ModuleManager::class);
    }

    private function loadModuleRoutes(Module $module): void
    {
        foreach (['Routes/web.php', 'Routes/api.php'] as $route) {
            $path = $module->path($route);

            if (File::exists($path)) {
                $this->loadRoutesFrom($path);
            }
        }
    }

    private function loadModuleResources(Module $module): void
    {
        $lowerName = $module->lowerName();

        if (File::isDirectory($module->path('Resources/views'))) {
            $this->loadViewsFrom($module->path('Resources/views'), $lowerName);
        }

        if (File::isDirectory($module->path('Resources/lang'))) {
            $this->loadTranslationsFrom($module->path('Resources/lang'), $lowerName);
        }

        if (File::isDirectory($module->path('Database/Migrations'))) {
            $this->loadMigrationsFrom($module->path('Database/Migrations'));
        }
    }
}
