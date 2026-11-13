<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * The database schema is owned by the nonstop_backend repo. This app only keeps a copy
     * of its migrations under tests/schema so the test database can be built in memory.
     * Refresh the copy whenever the backend schema changes.
     */
    protected function migrateUsing()
    {
        return ['--seed' => $this->shouldSeed(), '--path' => base_path('tests/schema'), '--realpath' => true];
    }

    protected function migrateFreshUsing()
    {
        return ['--drop-views' => $this->shouldDropViews(), '--drop-types' => $this->shouldDropTypes(), '--path' => base_path('tests/schema'), '--realpath' => true];
    }
}
