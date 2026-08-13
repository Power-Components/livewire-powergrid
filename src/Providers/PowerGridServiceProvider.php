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
use PowerComponents\LivewirePowerGrid\Contracts\{GridCache, GridConfig, SchemaInspector};
use PowerComponents\LivewirePowerGrid\{DataSource\Processors\Database\Handlers\SearchHandler,
    DataSource\Processors\Database\Handlers\SearchHandlerContract,
    Livewire\Detail,
    PowerGridManager,
    Testing\TestActions};
use PowerComponents\LivewirePowerGrid\Lite\Components as LiteComponents;
use PowerComponents\LivewirePowerGrid\Support\Environment\{LaravelGridCache, LaravelGridConfig, LaravelSchemaInspector};
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

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

        if (! $this->app->bound('powergrid.theme')) {
            $this->app->singleton('powergrid.theme', function ($app) {
                /** @var string $themeClass */
                $themeClass = config('livewire-powergrid.theme', Tailwind::class);

                return $app->make($themeClass);
            });
        }

        $this->publishViews();
        $this->publishConfigs();
        $this->registerLiteComponents();
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

        $this->app->singleton(SchemaInspector::class, LaravelSchemaInspector::class);
        $this->app->singleton(GridConfig::class, LaravelGridConfig::class);
        $this->app->singleton(GridCache::class, LaravelGridCache::class);
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

    private function registerLiteComponents(): void
    {
        Blade::component('pg-table', LiteComponents\Table::class);
        Blade::component('pg-columns', LiteComponents\Columns::class);
        Blade::component('pg-column', LiteComponents\Column::class);
        Blade::component('pg-rows', LiteComponents\Rows::class);
        Blade::component('pg-row', LiteComponents\Row::class);
        Blade::component('pg-cell', LiteComponents\Cell::class);
    }
}
