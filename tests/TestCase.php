<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Providers\PowerGridServiceProvider;
use Livewire\LivewireServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            PowerGridServiceProvider::class
        ];
    }
}
