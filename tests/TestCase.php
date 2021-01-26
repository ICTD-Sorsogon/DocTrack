<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\SeedDatabaseAfterRefresh;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

        /**
     * Add custom setup traits
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();
        if (isset($uses[SeedDatabaseAfterRefresh::class])) {
            $this->seedDatabaseAfterRefresh();
        }

        return $uses;
    }
}
