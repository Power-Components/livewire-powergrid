<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PowerComponents\LivewirePowerGrid\Providers\PowerGridServiceProvider;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

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

        $databases = ['testbench', ':memory:', 'powergridtest', 'tempdb', 'sqlsrv'];

        foreach ($databases as $database) {
            $app['config']->set('database.connections.' . $database, [
                'driver'   => env('DB_DRIVER'),
                'host'     => env('DB_HOST'),
                'port'     => env('DB_PORT'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'database' => env('DB_DATABASE'),
                'prefix'   => '',
            ]);
        }

        $app['config']->set('livewire-powergrid.exportable.default', 'openspout_v4');
        $app['config']->set(
            'livewire-powergrid.exportable.openspout_v4',
            [
                'xlsx' => \PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v4\ExportToXLS::class,
                'csv'  => \PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v4\ExportToCsv::class,
            ]
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            PowerGridServiceProvider::class,
        ];
    }
}
