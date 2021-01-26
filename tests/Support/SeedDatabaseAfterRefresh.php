<?php

namespace Tests\Support;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait SeedDatabaseAfterRefresh
{
    public function seedDatabaseAfterRefresh()
    {
        if (! $this->isRefreshDatabaseUsed()) {
            return;
        }

        $this->usingInMemoryDatabase()
            ? $this->runSeed()
            : $this->runTestDatabaseSeed();
    }

    protected function runSeed()
    {
        $this->artisan('db:seed');

        $this->app[Kernel::class]->setArtisan(null);
    }

    public function runTestDatabaseSeed()
    {
        if (SeedDatabaseAfterRefreshState::$seeded) {
            return;
        }

        $this->runSeed();

        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            $database->connection($name)->commit();
            $database->connection($name)->beginTransaction();
        }

        SeedDatabaseAfterRefreshState::$seeded = true;
    }

    protected function isRefreshDatabaseUsed()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        return (isset($uses[RefreshDatabase::class]));
    }
}
