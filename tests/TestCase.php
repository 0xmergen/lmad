<?php

declare(strict_types=1);

namespace Lmad\Tests;

use Lmad\LmadServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LmadServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Setup test environment
    }
}
