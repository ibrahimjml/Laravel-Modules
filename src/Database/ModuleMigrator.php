<?php

namespace Ibrahimjml\LaravelModules\Database;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

/**
 * Builds migrators that are aware of the migration files of a single
 * directory only, so migrations of other modules or of the application
 * are never rolled back or reset along with them.
 */
class ModuleMigrator
{
    public function __construct(
        private readonly Migrator $migrator,
        private readonly ConnectionResolverInterface $resolver,
        private readonly Filesystem $files,
        private readonly Dispatcher $events,
    ) {
    }

    /**
     * Get a migrator scoped to the migration files found in the given path.
     */
    public function scopedTo(string $path): Migrator
    {
        $migrator = new Migrator(
            new ScopedMigrationRepository(
                $this->migrator->getRepository(),
                array_fill_keys(array_keys($this->migrator->getMigrationFiles([$path])), true),
            ),
            $this->resolver,
            $this->files,
            $this->events,
        );

        $migrator->setConnection($this->migrator->getConnection());

        return $migrator;
    }
}
