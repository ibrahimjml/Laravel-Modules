<?php

namespace Ibrahimjml\LaravelModules;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleManager
{
    /** @var Collection<string, Module>|null */
    private ?Collection $modules = null;

    /** @var array<string, bool>|null */
    private ?array $statuses = null;

    public function __construct(
        private readonly string $path,
        private readonly string $statusesFile,
    ) {
    }

    /**
     * @return Collection<string, Module>
     */
    public function all(): Collection
    {
        if ($this->modules !== null) {
            return $this->modules;
        }

        if (! File::isDirectory($this->path)) {
            return $this->modules = collect();
        }

        return $this->modules = collect(File::directories($this->path))
            ->mapWithKeys(function (string $directory): array {
                $manifestPath = "{$directory}/module.json";

                $manifest = File::exists($manifestPath)
                    ? (json_decode(File::get($manifestPath), true) ?? [])
                    : [];

                $name = $manifest['name'] ?? basename($directory);

                return [$name => new Module($name, $directory, $manifest)];
            })
            ->sortKeys();
    }

    public function find(string $name): ?Module
    {
        return $this->all()->get(Str::studly($name));
    }

    /**
     * @return Collection<string, Module>
     */
    public function enabled(): Collection
    {
        return $this->all()->filter(fn (Module $module): bool => $this->isEnabled($module->name));
    }

    /**
     * @return Collection<string, Module>
     */
    public function disabled(): Collection
    {
        return $this->all()->reject(fn (Module $module): bool => $this->isEnabled($module->name));
    }

    public function isEnabled(string $name): bool
    {
        return (bool) ($this->statuses()[$name] ?? true);
    }

    public function enable(string $name): void
    {
        $this->setStatus($name, true);
    }

    public function disable(string $name): void
    {
        $this->setStatus($name, false);
    }

    public function register(string $name): void
    {
        if (! array_key_exists($name, $this->statuses())) {
            $this->setStatus($name, true);
        }
    }

    /**
     * Forget the cached module list and statuses, forcing a rescan on next access.
     */
    public function forget(): void
    {
        $this->modules = null;
        $this->statuses = null;
    }

    /**
     * Resolve a stub file, preferring the host app's published copy and
     * falling back to the one bundled with this package when missing.
     */
    public function stub(string $name): string
    {
        $published = rtrim(config('modules.stubs'), '/')."/{$name}.stub";

        if (File::exists($published)) {
            return $published;
        }

        return __DIR__."/../stubs/{$name}.stub";
    }

    private function setStatus(string $name, bool $enabled): void
    {
        $statuses = $this->statuses();
        $statuses[$name] = $enabled;
        $this->statuses = $statuses;

        File::put(
            $this->statusesFile,
            json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL,
        );
    }

    /**
     * @return array<string, bool>
     */
    private function statuses(): array
    {
        if ($this->statuses !== null) {
            return $this->statuses;
        }

        if (! File::exists($this->statusesFile)) {
            return $this->statuses = [];
        }

        return $this->statuses = json_decode(File::get($this->statusesFile), true) ?? [];
    }
}
