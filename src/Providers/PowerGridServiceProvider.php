<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use PowerComponents\LivewirePowerGrid\Commands\CreateCommand;
use PowerComponents\LivewirePowerGrid\Commands\DemoCommand;
use PowerComponents\LivewirePowerGrid\Commands\PublishCommand;
use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Themes\ThemeManager;

class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([CreateCommand::class]);
            $this->commands([PublishCommand::class]);
            $this->commands([DemoCommand::class]);
        }

        $this->publishViews();
        $this->publishConfigs();
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', $this->packageName);
        $this->createDirectives();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../resources/config/livewire-powergrid.php',
            $this->packageName
        );

        $file = __DIR__ . '/../functions.php';
        if (file_exists($file)) {
            require_once($file);
        }

        $this->app->alias(PowerGridManager::class, 'powergrid');
        $this->app->alias(ThemeManager::class, 'theme');
    }

    private function publishViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', $this->packageName);

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/' . $this->packageName),
        ], $this->packageName . '-views');
    }

    private function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../../resources/config/livewire-powergrid.php' => config_path($this->packageName . '.php'),
        ], 'livewire-powergrid-config');

        $this->publishes([
            __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/' . $this->packageName),
        ], $this->packageName . '-lang');
    }

    private function createDirectives()
    {
        Blade::directive('powerGridStyles', function () {
            return "<?php echo view('livewire-powergrid::assets.styles')->render(); ?>";
        });

        Blade::directive('powerGridScripts', function () {
            return "<?php echo view('livewire-powergrid::assets.scripts')->render(); ?>";
        });
    }
}
