<?php

namespace Ibrahimjml\LaravelModules;

class Module
{
    /**
     * @param array<string, mixed> $manifest
     */
    public function __construct(
        public readonly string $name,
        private readonly string $directory,
        private readonly array $manifest = [],
    ) {
    }

    public function lowerName(): string
    {
        return strtolower($this->name);
    }

    public function namespace(): string
    {
        return config('modules.namespace').'\\'.$this->name;
    }

    public function path(string $append = ''): string
    {
        return $append === '' ? $this->directory : $this->directory.'/'.ltrim($append, '/');
    }

    public function version(): string
    {
        return (string) ($this->manifest['version'] ?? '1.0.0');
    }

    public function description(): string
    {
        return (string) ($this->manifest['description'] ?? '');
    }

    public function providerClass(): string
    {
        return $this->namespace().'\\Providers\\'.$this->name.'ServiceProvider';
    }

    /**
     * Vite entry points for this module, relative to the project root,
     * e.g. "Modules/Blog/Resources/assets/js/blog.js".
     *
     * @return array<int, string>
     */
    public function viteEntries(): array
    {
        $entries = [];

        foreach (['js', 'css'] as $type) {
            $directory = $this->path("Resources/assets/{$type}");

            if (! is_dir($directory)) {
                continue;
            }

            foreach (glob("{$directory}/*.{$type}") ?: [] as $file) {
                $entries[] = "Modules/{$this->name}/Resources/assets/{$type}/".basename($file);
            }
        }

        return $entries;
    }
}
