<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use LaraDumps\LaraDumps\LaraDumpsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PowerComponents\LivewirePowerGrid\Providers\PowerGridServiceProvider;
use PowerComponents\LivewirePowerGrid\Tests\Actions\TestDatabase;

class TestCase extends BaseTestCase
{
    protected static $isRunningTests = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearViewsCache();
        TestDatabase::up();
    }

    /**
     * Delete PowerGrid cached views
     *
     * @return void
     */
    protected function clearViewsCache(): void
    {
        if (self::$isRunningTests === true) {
            return;
        }

        $viewsFolder = base_path() . '/resources/views/vendor/livewire-powergrid/';

        $viewsFolderPath = str_replace('/', DIRECTORY_SEPARATOR, $viewsFolder);

        File::deleteDirectory($viewsFolderPath);

        self::$isRunningTests = true;
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('app.key', 'base64:RygUQvaR926QuH4d5G6ZDf9ToJEEeO2p8qDSCq6emPk=');

        $app['config']->set('database.connections.testbench', [
            'driver'   => env('DB_DRIVER'),
            'host'     => env('DB_HOST'),
            'port'     => env('DB_PORT'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'database' => env('DB_DATABASE'),
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            PowerGridServiceProvider::class,
            LaraDumpsServiceProvider::class,
        ];
    }
}
