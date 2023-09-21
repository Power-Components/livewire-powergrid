<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportLegacyModels\{EloquentCollectionSynth, EloquentModelSynth};
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Commands\{CreateCommand, PublishCommand, UpdateCommand};
use PowerComponents\LivewirePowerGrid\Components\Actions\Macros;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterManager;
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager;
use PowerComponents\LivewirePowerGrid\PowerGridManager;
use PowerComponents\LivewirePowerGrid\Themes\ThemeManager;

/** @codeCoverageIgnore */
class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PublishCommand::class]);
            $this->commands([UpdateCommand::class]);
            $this->commands([CreateCommand::class]);
        }

        $this->publishViews();
        $this->publishConfigs();
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', $this->packageName);

        Livewire::propertySynthesizer(EloquentModelSynth::class);
        Livewire::propertySynthesizer(EloquentCollectionSynth::class);
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
        $this->app->alias(RuleManager::class, 'rule');
        $this->app->alias(FilterManager::class, 'filter');

        Macros::boot();
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
