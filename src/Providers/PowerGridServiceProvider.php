<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Support\Facades\{Blade, View};
use Illuminate\Support\ServiceProvider;
use PowerComponents\LivewirePowerGrid\Commands\{CreateCommand, DemoCommand, PublishCommand};
use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Themes\ThemeManager;

class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot(): void
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

        View::composer('livewire-powergrid::assets.styles', function ($view) {
            $view->cssPath = __DIR__ . '/../../dist/powergrid.css';
        });
        View::composer('livewire-powergrid::assets.scripts', function ($view) {
            $view->jsPath  = __DIR__ . '/../../dist/powergrid.js';
        });
    }

    public function register(): void
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

    private function publishViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', $this->packageName);

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/' . $this->packageName),
        ], $this->packageName . '-views');
    }

    private function publishConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../../resources/config/livewire-powergrid.php' => config_path($this->packageName . '.php'),
        ], 'livewire-powergrid-config');

        $this->publishes([
            __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/' . $this->packageName),
        ], $this->packageName . '-lang');
    }

    private function createDirectives(): void
    {
        Blade::directive('powerGridStyles', function () {
            return "<?php echo view('livewire-powergrid::assets.styles')->render(); ?>";
        });

        Blade::directive('powerGridScripts', function () {
            return "<?php echo view('livewire-powergrid::assets.scripts')->render(); ?>";
        });

        View::composer('livewire-powergrid::assets.styles', function ($view) {
            $view->cssPath = __DIR__ . '/../dist/powergrid.css';
        });
    }
}
