<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Blaze\Blaze;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Commands\{CreateCommand, GenerateThemeMetaCommand, PublishCommand};
use PowerComponents\LivewirePowerGrid\Lite\Components as LiteComponents;
use PowerComponents\LivewirePowerGrid\Livewire\Detail;
use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Support\CompatAliases;
use PowerComponents\LivewirePowerGrid\Support\Synth\PowerGridWireableSynth;
use PowerComponents\LivewirePowerGrid\Testing\TestActions;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;
use PowerComponents\Turbine\Components\Filters\FilterManager;
use PowerComponents\Turbine\Components\Rules\RuleManager;

/** @codeCoverageIgnore */
class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot(): void
    {
        if ($this->app->bound('livewire')) {
            app('livewire')->propertySynthesizer(PowerGridWireableSynth::class);
        }

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
                /** @var string $configured */
                $configured = config('livewire-powergrid.theme', Tailwind::class);

                return $app->make(PowerGridManager::resolveThemeClass($configured));
            });
        }

        $this->publishViews();
        $this->publishConfigs();
        $this->optimizeIconComponents();
        $this->registerLiteComponents();
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', $this->packageName);

        Testable::mixin(new TestActions());
    }

    public function register(): void
    {
        CompatAliases::register();

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

        Livewire::component('powergrid-detail', Detail::class);

        Macros::columns();
        Macros::actions();
        Macros::builder();

        foreach (PowerGridManager::$plugins as $plugin) {
            $plugin::boot();
        }
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

    private function optimizeIconComponents(): void
    {
        if (! $this->app->bound('blaze')) {
            return;
        }

        Blaze::optimize()->in(
            __DIR__.'/../../resources/views/components/icons',
            compile: true,
            memo: true,
            fold: true,
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
