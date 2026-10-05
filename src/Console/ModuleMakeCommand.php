<?php

namespace Ibrahimjml\LaravelModules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Ibrahimjml\LaravelModules\ModuleManager;

class ModuleMakeCommand extends Command
{
    protected $signature = 'module:make
        {name : The name of the module}
        {--force : Overwrite the module if it already exists}';

    protected $description = 'create a new module';

    public function handle(ModuleManager $manager): int
    {
        $studly = Str::studly($this->argument('name'));
        $lower = Str::lower($studly);
        $path = config('modules.path')."/{$studly}";

        if (File::isDirectory($path) && ! $this->option('force')) {
            $this->components->error("Module [{$studly}] already exists.");

            return self::FAILURE;
        }

        foreach ([
            'Config', 'Console', 'Database/Migrations', 'Database/Seeders', 'Database/Factories',
            'Http/Controllers', 'Http/Requests', 'Http/Middleware', 'Models', 'Providers',
            'Actions', 'Events', 'Listeners', 'Observers', 'Policies', 'Enums', 'Traits', 'Services',
            'Resources/views', 'Resources/lang/en', 'Resources/assets/js', 'Resources/assets/css', 'Routes',
        ] as $directory) {
            File::ensureDirectoryExists("{$path}/{$directory}");
        }

        $replacements = ['namespace' => config('modules.namespace'), 'studly' => $studly, 'lower' => $lower];

        File::put("{$path}/module.json", $this->render($manager, 'module.json', $replacements));
        File::put("{$path}/composer.json", $this->render($manager, 'composer', [
            ...$replacements,
            ...$this->composerReplacements($studly, $lower, $path),
        ]));
        File::put("{$path}/Providers/{$studly}ServiceProvider.php", $this->render($manager, 'service-provider', $replacements));
        File::put("{$path}/Routes/web.php", $this->render($manager, 'routes-web', $replacements));
        File::put("{$path}/Routes/api.php", $this->render($manager, 'routes-api', $replacements));
        File::put("{$path}/Config/config.php", $this->render($manager, 'config', $replacements));
        File::put("{$path}/Resources/assets/js/{$lower}.js", $this->render($manager, 'asset-js', $replacements));
        File::put("{$path}/Resources/assets/css/{$lower}.css", $this->render($manager, 'asset-css', $replacements));
        File::put("{$path}/vite.config.js", $this->render($manager, 'vite-config', $replacements));

        // The module directory now exists on disk; drop the cached (pre-creation) module list.
        $manager->forget();
        $manager->register($studly);

        $this->call('module:make-seeder', [
            'module' => $studly,
            'name' => "{$studly}DatabaseSeeder",
        ]);

        $this->components->info("Module [{$studly}] created successfully.");

        return self::SUCCESS;
    }

    /**
     * @param array<string, string> $replacements
     */
    private function render(ModuleManager $manager, string $stub, array $replacements): string
    {
        $content = File::get($manager->stub($stub));

        foreach ($replacements as $search => $replace) {
            $content = str_replace(["{{ {$search} }}", "\${$search}\$"], $replace, $content);
        }

        return $content;
    }

    /**
     * Tokens for the composer stub, falling back to the host app's composer.json.
     *
     * @return array<string, string>
     */
    private function composerReplacements(string $studly, string $lower, string $directory): array
    {
        $composer = $this->hostComposer();
        $author = $composer['authors'][0] ?? [];

        return [
            'VENDOR' => (string) (config('modules.composer.vendor')
                ?? Str::before((string) ($composer['name'] ?? $lower), '/')),
            'LOWER_NAME' => $lower,
            'STUDLY_NAME' => $studly,
            'MODULE_NAMESPACE' => config('modules.namespace'),
            'AUTHOR_NAME' => (string) (config('modules.composer.author') ?? $author['name'] ?? ''),
            'AUTHOR_EMAIL' => (string) (config('modules.composer.email') ?? $author['email'] ?? ''),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function hostComposer(): array
    {
        return File::exists(base_path('composer.json'))
            ? (json_decode(File::get(base_path('composer.json')), true) ?? [])
            : [];
    }
}
