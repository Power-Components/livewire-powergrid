<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Providers\PowerGridServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PowerGridServiceProvider::class
        ];
    }
}
