<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\{Blade, Event};
use Illuminate\Support\{ServiceProvider};
use Laravel\Pulse\Facades\Pulse;
use Livewire\Features\SupportLegacyModels\{EloquentCollectionSynth, EloquentModelSynth};
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Commands\{CreateCommand, PublishCommand, UpdateCommand};
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterManager;
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager;
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;
use PowerComponents\LivewirePowerGrid\{Livewire\LazyChild,
    Livewire\PerformanceCard,
    PowerGridManager,
    Testing\TestActions};

/** @codeCoverageIgnore */
class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([UpdateCommand::class]);
            $this->commands([PublishCommand::class]);
            $this->commands([CreateCommand::class]);
        }

        $this->publishViews();
        $this->publishConfigs();
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', $this->packageName);

        Livewire::propertySynthesizer(EloquentModelSynth::class);
        Livewire::propertySynthesizer(EloquentCollectionSynth::class);

        Testable::mixin(new TestActions());
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../resources/config/livewire-powergrid.php',
            $this->packageName
        );

        $file = __DIR__ . '/../functions.php';

        if (file_exists($file)) {
            require_once $file;
        }

        $this->app->alias(PowerGridManager::class, 'powergrid');
        $this->app->alias(RuleManager::class, 'rule');
        $this->app->alias(FilterManager::class, 'filter');

        Livewire::component('lazy-child', LazyChild::class);

        Event::listen(MigrationsEnded::class, fn () => PowerGridTableCache::forgetAll());

        if (class_exists(Pulse::class)) {
            Livewire::component('powergrid-performance-card', PerformanceCard::class);
        }

        Macros::columns();
        Macros::actions();
        Macros::builder();
    }

    private function publishViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', $this->packageName);

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/' . $this->packageName),
        ], $this->packageName . '-views');

        Blade::anonymousComponentPath(
            __DIR__ . '/../../resources/views/tests',
            'tests'
        );
    }

    private function publishConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../../resources/config/livewire-powergrid.php' => config_path($this->packageName . '.php'),
        ], 'livewire-powergrid-config');

        $this->publishes([__DIR__ . '/../../resources/lang' => lang_path('vendor/' . $this->packageName)], $this->packageName . '-lang');
    }
}
