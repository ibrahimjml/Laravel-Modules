<?php

namespace Ibrahimjml\LaravelModules\Database;

use Illuminate\Database\Migrations\MigrationRepositoryInterface;

/**
 * Decorates the application migration repository and hides every migration
 * that is not part of the given scope.
 *
 * Laravel resolves the migrations to rollback from the migrations table of the
 * whole application, ignoring the given paths, so rolling back a single module
 * would also roll back (or report as missing) migrations belonging to the
 * application or to another module.
 */
class ScopedMigrationRepository implements MigrationRepositoryInterface
{
    /**
     * @param  array<string, true>  $migrations  The migration names the scope is limited to, keyed by name.
     */
    public function __construct(
        private readonly MigrationRepositoryInterface $repository,
        private readonly array $migrations,
    ) {
    }

    /** The completed migrations within the scope. */
    public function getRan()
    {
        return array_values(array_filter(
            $this->repository->getRan(),
            fn ($migration): bool => $this->inScope($migration),
        ));
    }

    /** The scoped migrations, most recent first, limited to the given number of steps. */
    public function getMigrations($steps)
    {
        $migrations = $this->scopedMigrations();

        return $steps > 0 ? array_slice($migrations, 0, (int) $steps) : $migrations;
    }

    /** The scoped migrations of the given batch. */
    public function getMigrationsByBatch($batch)
    {
        return array_values(array_filter(
            $this->repository->getMigrationsByBatch($batch),
            fn ($migration): bool => $this->inScope($migration->migration),
        ));
    }

    /** The scoped migrations of the most recent batch. */
    public function getLast()
    {
        $migrations = $this->scopedMigrations();

        if ($migrations === []) {
            return [];
        }

        $batch = (int) $migrations[0]->batch;

        return array_values(array_filter(
            $migrations,
            fn ($migration): bool => (int) $migration->batch === $batch,
        ));
    }

    /** The batch number of every scoped migration, keyed by migration name. */
    public function getMigrationBatches()
    {
        return array_filter(
            $this->repository->getMigrationBatches(),
            fn ($batch, $migration): bool => $this->inScope($migration),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    public function log($file, $batch)
    {
        $this->repository->log($file, $batch);
    }

    public function delete($migration)
    {
        $this->repository->delete($migration);
    }

    public function getNextBatchNumber()
    {
        return $this->repository->getNextBatchNumber();
    }

    public function createRepository()
    {
        $this->repository->createRepository();
    }

    public function repositoryExists()
    {
        return $this->repository->repositoryExists();
    }

    public function deleteRepository()
    {
        $this->repository->deleteRepository();
    }

    public function setSource($name)
    {
        $this->repository->setSource($name);
    }

    /**
     * @return array<int, object>
     */
    private function scopedMigrations(): array
    {
        // Migrations are rolled back from the last one to the first one, so the
        // ascending order of the repository has to be reversed here.
        $batches = array_reverse($this->getMigrationBatches(), true);

        return array_map(
            fn (string $migration, int $batch): object => (object) [
                'migration' => $migration,
                'batch' => $batch,
            ],
            array_keys($batches),
            array_values($batches),
        );
    }

    private function inScope(string $migration): bool
    {
        return isset($this->migrations[$migration]);
    }
}
