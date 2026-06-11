<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\{Blade, Event};
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Commands\{CreateCommand, PublishCommand};
use PowerComponents\LivewirePowerGrid\Commands\GenerateThemeMetaCommand;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterManager;
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager;
use PowerComponents\LivewirePowerGrid\{DataSource\Processors\Database\Handlers\SearchHandler,
    DataSource\Processors\Database\Handlers\SearchHandlerContract,
    Livewire\Detail,
    PowerGridManager,
    Testing\TestActions};
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;

/** @codeCoverageIgnore */
class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PublishCommand::class]);
            $this->commands([CreateCommand::class]);
            $this->commands([GenerateThemeMetaCommand::class]);
        }

        Blade::directive('theme', function ($expression) {
            return "<?php echo theme($expression); ?>";
        });

        //        $this->app->singleton('powergrid.theme', function ($app) {
        //            $themeClass = strval(config('livewire-powergrid.theme'));
        //
        //            return $app->make($themeClass);
        //        });

        $this->publishViews();
        $this->publishConfigs();
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', $this->packageName);

        Testable::mixin(new TestActions());
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../resources/config/livewire-powergrid.php',
            $this->packageName
        );

        $file = __DIR__.'/../functions.php';

        if (file_exists($file)) {
            require_once $file;
        }

        $this->app->alias(PowerGridManager::class, 'powergrid');
        $this->app->alias(RuleManager::class, 'rule');
        $this->app->alias(FilterManager::class, 'filter');

        Event::listen(MigrationsEnded::class, fn () => PowerGridTableCache::forgetAll());

        Livewire::component('powergrid-detail', Detail::class);

        Macros::columns();
        Macros::actions();
        Macros::builder();

        foreach (PowerGridManager::$plugins as $plugin) {
            $plugin::boot();
        }

        $this->app->bind(SearchHandlerContract::class, function ($app, array $params) {
            return new SearchHandler($params['component']);
        });
    }

    private function publishViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', $this->packageName);
        $this->loadViewsFrom(__DIR__.'/../Plugins', 'powergrid-plugins');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/'.$this->packageName),
        ], $this->packageName.'-views');

        Blade::anonymousComponentPath(
            __DIR__.'/../../resources/views/tests',
            'tests'
        );
    }

    private function publishConfigs(): void
    {
        $this->publishes([
            __DIR__.'/../../resources/config/livewire-powergrid.php' => config_path($this->packageName.'.php'),
        ], 'livewire-powergrid-config');

        $this->publishes([__DIR__.'/../../resources/lang' => lang_path('vendor/'.$this->packageName)], $this->packageName.'-lang');
    }
}
