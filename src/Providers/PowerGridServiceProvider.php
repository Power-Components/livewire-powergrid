<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Container\Container;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\Facades\{Blade, Event};
use Illuminate\Support\{Js, ServiceProvider};
use Laravel\Scout\Builder;
use Laravel\Scout\Contracts\PaginatesEloquentModels;
use Livewire\Features\SupportLegacyModels\{EloquentCollectionSynth, EloquentModelSynth};
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Commands\CheckDependenciesCommand;
use PowerComponents\LivewirePowerGrid\Commands\{CreateCommand, PublishCommand, UpdateCommand};
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterManager;
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager;
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;
use PowerComponents\LivewirePowerGrid\Themes\ThemeManager;
use PowerComponents\LivewirePowerGrid\{Button,
    Components\Rules\RuleActions,
    Livewire\LazyChild,
    Livewire\PerformanceCard,
    PowerGridManager};

/** @codeCoverageIgnore */
class PowerGridServiceProvider extends ServiceProvider
{
    private string $packageName = 'livewire-powergrid';

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([UpdateCommand::class]);
            $this->commands([PublishCommand::class]);
            $this->commands([CheckDependenciesCommand::class]);
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

        Livewire::component('lazy-child', LazyChild::class);

        Event::listen(MigrationsEnded::class, fn () => PowerGridTableCache::forgetAll());

        if (class_exists(\Laravel\Pulse\Facades\Pulse::class)) {
            Livewire::component('powergrid-performance-card', PerformanceCard::class);
        }

        $this->macros();
        $this->actionMacros();
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

    private function macros(): void
    {
        if (class_exists(\Laravel\Scout\Builder::class)) {
            Builder::macro('paginateSafe', function ($perPage = null, $pageName = 'page', $page = null) {
                $engine = $this->engine(); // @phpstan-ignore-line

                if ($engine instanceof PaginatesEloquentModels) {
                    return $engine->paginate($this, $perPage, $page)->appends('query', $this->query);
                }

                $page = $page ?: Paginator::resolveCurrentPage($pageName);

                $perPage = $perPage ?: $this->model->getPerPage();

                $results = $this->model->newCollection( //@phpstan-ignore-line
                    $engine->map(
                        $this,
                        $rawResults = $engine->paginate($this, $perPage, $page),
                        $this->model
                    )->all()
                );

                return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
                    'items'       => $results,
                    'total'       => $engine->getTotalCount($rawResults),
                    'perPage'     => $perPage,
                    'currentPage' => $page,
                    'options'     => [
                        'path'     => Paginator::resolveCurrentPath(),
                        'pageName' => $pageName,
                    ],
                ])->appends('query', $this->query);
            });
        }
    }

    public function actionMacros(): void
    {
        Button::macro('class', function (string $classes) {
            $this->attributes = array_merge([
                'class' => $classes,
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('call', function (string $method, array $params) {
            $this->attributes = array_merge([
                'wire:click' => "\$call('{$method}', " . Js::from($params) . ")",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('dispatch', function (string $event, array $params) {
            $this->attributes = array_merge([
                'wire:click' => "\$dispatch('{$event}', " . Js::from($params) . ")",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('dispatchTo', function (string $component, string $event, array $params) {
            $this->attributes = array_merge([
                'wire:click' => "\$dispatchTo('{$component}', '{$event}', " . Js::from($params) . ")",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('dispatchSelf', function (string $event, array $params) {
            $this->attributes = array_merge([
                'wire:click' => "\$dispatchSelf('{$event}', " . Js::from($params) . ")",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('parent', function (string $method, array $params) {
            $this->attributes = array_merge([
                'wire:click' => "\$parent.{$method}(" . Js::from($params) . ")",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('openModal', function (string $component, array $params) {
            $encoded = Js::from([
                'component' => $component,
                'arguments' => $params,
            ]);

            $this->attributes = array_merge([
                'wire:click' => "\$dispatch('openModal', $encoded)",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('disable', function () {
            $this->attributes = array_merge([
                'disabled' => 'disabled',
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('tooltip', function (string $value) {
            $this->attributes = array_merge([
                'title' => $value,
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('route', function (string $route, array $params, string $target) {
            $this->attributes = array_merge([
                'href'   => route($route, $params),
                'target' => $target,
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('id', function (string $id) {
            $this->attributes = array_merge([
                'id' => $id,
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('hideWhen', function (\Closure $closure) {
            $this->hideWhen = $closure;

            return $this;
        });

        Button::macro('can', function (\Closure $closure) {
            $this->can = $closure;

            return $this;
        });

        Button::macro('confirm', function (?string $message = null) {
            $this->attributes = array_merge([
                'wire:confirm' => $message ?? trans('livewire-powergrid::datatable.buttons_macros.confirm.message'),
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('confirmPrompt', function (?string $message = null, string $confirmValue = 'Confirm') {
            $message      = $message ?? trans('livewire-powergrid::datatable.buttons_macros.confirm_prompt.message', ['confirm_value' => $confirmValue]);
            $confirmValue = trim($confirmValue);

            $this->attributes = array_merge([
                'wire:confirm.prompt' => "$message | $confirmValue",
                ...$this->attributes,
            ]);

            return $this;
        });

        Button::macro('toggleDetail', function (int|string $rowId) {
            $this->attributes = array_merge([
                'wire:click' => "toggleDetail('$rowId')",
                ...$this->attributes,
            ]);

            return $this;
        });

        RuleActions::macro('dispatch', function (string $event, array $params) {
            $params = Js::from($params);

            $value = "\$dispatch('{$event}', {$params})";

            $this->setAttribute('wire:click', $value);

            return $this;
        });
    }
}
