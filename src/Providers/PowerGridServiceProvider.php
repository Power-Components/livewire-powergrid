<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use PowerComponents\LivewirePowerGrid\Commands\CreateCommand;
use PowerComponents\LivewirePowerGrid\Commands\PublishCommand;
use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Themes\ThemeManager;

class PowerGridServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([CreateCommand::class]);
            $this->commands([PublishCommand::class]);
        }

        $this->loadViews();
        $this->loadConfigs();
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'livewire-powergrid');
        $this->createDirectives();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../resources/config/livewire-powergrid.php', 'livewire-powergrid'
        );

        $file = __DIR__ . '/../functions.php';
        if (file_exists($file)) {
            require_once($file);
        }

        $this->app->alias(PowerGridManager::class, 'powergrid');
        $this->app->alias(ThemeManager::class, 'theme');
    }

    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'livewire-powergrid');

        $this->publishes([__DIR__ . '/../../resources/views' => resource_path('views/vendor/livewire-powergrid')], 'livewire-powergrid-views');
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

    private function loadConfigs()
    {
        $this->publishes([
            __DIR__ . '/../../resources/config/livewire-powergrid.php' => config_path('livewire-powergrid.php'),
        ], 'livewire-powergrid-config');

        $this->publishes([
            __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/livewire-powergrid')
        ], 'livewire-powergrid-lang');
    }
}
