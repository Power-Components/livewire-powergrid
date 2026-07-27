<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\Js;
use Laravel\Scout\{Builder, Builder as ScoutBuilder};
use Laravel\Scout\Contracts\PaginatesEloquentModels;
use PowerComponents\LivewirePowerGrid\{Button, Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleActions;
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;

class Macros
{
    public static function columns(): void
    {
        // @deprecated since 7.x — withSum/withCount/withAvg/withMin/withMax are deprecated; use withSummary() with a closure instead.
        Column::macro('withSum', function (string $label, bool $header = false, bool $footer = true): Column {
            /** @var Column $this */
            $props = $this->properties;
            data_set($props, 'summarize.sum.label', $label);
            data_set($props, 'summarize.sum.header', $header);
            data_set($props, 'summarize.sum.footer', $footer);
            /** @var array<string, mixed> $props */
            $this->properties = $props;

            return $this;
        });

        Column::macro('withCount', function (string $label, bool $header = false, bool $footer = true): Column {
            /** @var Column $this */
            $props = $this->properties;
            data_set($props, 'summarize.count.label', $label);
            data_set($props, 'summarize.count.header', $header);
            data_set($props, 'summarize.count.footer', $footer);
            /** @var array<string, mixed> $props */
            $this->properties = $props;

            return $this;
        });

        Column::macro('withAvg', function (string $label, bool $header = false, bool $footer = true): Column {
            /** @var Column $this */
            $props = $this->properties;
            data_set($props, 'summarize.avg.label', $label);
            data_set($props, 'summarize.avg.header', $header);
            data_set($props, 'summarize.avg.footer', $footer);
            /** @var array<string, mixed> $props */
            $this->properties = $props;

            return $this;
        });

        Column::macro('withMin', function (string $label, bool $header = false, bool $footer = true): Column {
            /** @var Column $this */
            $props = $this->properties;
            data_set($props, 'summarize.min.label', $label);
            data_set($props, 'summarize.min.header', $header);
            data_set($props, 'summarize.min.footer', $footer);
            /** @var array<string, mixed> $props */
            $this->properties = $props;

            return $this;
        });

        Column::macro('withMax', function (string $label, bool $header = false, bool $footer = true): Column {
            /** @var Column $this */
            $props = $this->properties;
            data_set($props, 'summarize.max.label', $label);
            data_set($props, 'summarize.max.header', $header);
            data_set($props, 'summarize.max.footer', $footer);
            /** @var array<string, mixed> $props */
            $this->properties = $props;

            return $this;
        });

        Column::macro('withSummary', function (string $key, string $label, \Closure $using, bool $header = false, bool $footer = true): Column {
            /** @var Column $this */
            $props = $this->properties;
            data_set($props, "summarize.custom.{$key}.label", $label);
            data_set($props, "summarize.custom.{$key}.header", $header);
            data_set($props, "summarize.custom.{$key}.footer", $footer);
            /** @var array<string, mixed> $props */
            $this->properties = $props;

            // Closure kept out of $properties (not serializable); resolved server-side.
            $this->summaryCallbacks[$key] = $using;

            return $this;
        });

        Column::macro('searchableRaw', function (string $sql = ''): Column {
            /** @var Column $this */
            $field = $this->dataField ?: $this->field;

            $this->rawQueries[] = [
                'method' => 'orWhereRaw',
                'sql' => $sql,
                'bindings' => [function (PowerGridComponent $component) use ($field): string {
                    $search = $component->search;
                    $fieldMethod = 'beforeSearch'.str($field)->camel()->ucfirst();

                    if (method_exists($component, $fieldMethod)) {
                        $search = $component->{$fieldMethod}($field, $search);
                    } elseif (method_exists($component, 'beforeSearch')) {
                        $search = $component->beforeSearch($field, $search);
                    }

                    return "%$search%";
                }],
                'enabled' => function (PowerGridComponent $component) {
                    return filled($component->search);
                },
            ];

            return $this;
        });

        Column::macro('searchableJson', function (string $tableName): Column {
            $this->rawQueries[] = [
                'method' => 'orWhereRaw',
                'sql' => function () use ($tableName) {
                    /** @var string $driver */
                    $driver = config('database.default');
                    $connection = config("database.connections.{$driver}.driver");

                    $quote = $connection === 'pgsql' ? '"' : '`';

                    if ($tableName) {
                        return "LOWER({$quote}{$tableName}{$quote}.{$quote}{$this->dataField}{$quote}) like ?";
                    }

                    return "LOWER({$quote}{$this->dataField}{$quote}) like ?";
                },
                'bindings' => [function (PowerGridComponent $component) {
                    $search = htmlspecialchars($component->search, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    return '%'.strtolower($search).'%';
                }],
                'enabled' => function (PowerGridComponent $component) {
                    return filled($component->search);
                },
            ];

            return $this;
        });

        Column::macro('naturalSort', function (bool $when = false, ?string $tableName = null): Column {
            /** @var Column $this */
            $this->enableSort();

            if ($when) {
                $this->rawQueries[] = [
                    'method' => 'orderByRaw',
                    'sql' => $tableName
                        ? Sql::sortStringAsNumber("`$tableName`.`$this->dataField`")
                        : Sql::sortStringAsNumber($this->dataField),
                    'bindings' => [],
                ];
            }

            return $this;
        });
    }

    public static function actions(): void
    {
        Button::macro('class', function (string $classes): Button {
            $this->attributes([
                'class' => $classes,
            ]);

            return $this;
        });

        Button::macro('call', function (string $method, array $params): Button {
            $this->attributes([
                'wire:click' => "\$call('{$method}', ".Js::from($params).')',
            ]);

            return $this;
        });

        Button::macro('dispatch', function (string $event, array $params): Button {
            $this->attributes([
                'wire:click' => "\$dispatch('{$event}', ".Js::from($params).')',
            ]);

            return $this;
        });

        Button::macro('dispatchTo', function (string $component, string $event, array $params): Button {
            $this->attributes([
                'wire:click' => "\$dispatchTo('{$component}', '{$event}', ".Js::from($params).')',
            ]);

            return $this;
        });

        Button::macro('dispatchSelf', function (string $event, array $params): Button {
            $this->attributes([
                'wire:click' => "\$dispatchSelf('{$event}', ".Js::from($params).')',
            ]);

            return $this;
        });

        Button::macro('parent', function (string $method, array $params): Button {
            $this->attributes([
                'wire:click' => "\$parent.{$method}(".Js::from($params).')',
            ]);

            return $this;
        });

        Button::macro('openModal', function (string $component, array $params): Button {
            $encoded = Js::from([
                'component' => $component,
                'arguments' => $params,
            ]);

            $this->attributes([
                'wire:click' => "\$dispatch('openModal', $encoded)",
            ]);

            return $this;
        });

        Button::macro('disable', function (bool $disable = true): Button {
            if ($disable) {
                $this->attributes([
                    'disabled' => 'disabled',
                ]);
            }

            return $this;
        });

        Button::macro('tooltip', function (string $value): Button {
            $this->attributes([
                'title' => $value,
            ]);

            return $this;
        });

        Button::macro('route', function (string $route, array $params, string $target = '_self'): Button {
            $this->tag('a');

            $this->attributes([
                'href' => route($route, $params),
                'target' => $target,
            ]);

            return $this;
        });

        Button::macro('id', function (?string $id = null): Button {
            $this->attributes([
                'id' => $id,
            ]);

            return $this;
        });

        Button::macro('can', function (bool|\Closure $closure): Button {
            $this->can = $closure;

            return $this;
        });

        Button::macro('confirm', function (?string $message = null): Button {
            $this->attributes([
                'wire:confirm' => $message ?? trans('livewire-powergrid::datatable.buttons_macros.confirm.message'),
            ]);

            return $this;
        });

        Button::macro('confirmPrompt', function (?string $message = null, string $confirmValue = 'Confirm'): Button {
            $message = $message ?? trans('livewire-powergrid::datatable.buttons_macros.confirm_prompt.message', ['confirm_value' => $confirmValue]);
            $confirmValue = trim($confirmValue);

            $this->attributes([
                'wire:confirm.prompt' => "$message | $confirmValue",
            ]);

            return $this;
        });

        Button::macro('toggleDetail', function (int|string $rowId): Button {
            $this->attributes([
                'wire:click' => "toggleDetail('$rowId')",
            ]);

            return $this;
        });

        RuleActions::macro('dispatch', function (string $event, array $params) {
            $params = Js::from($params);

            $value = "\$dispatch('{$event}', {$params})";

            $this->setAttribute('wire:click', $value);

            return $this;
        });

        RuleActions::macro('disable', function () {
            $this->setAttribute('disabled', 'disabled');

            return $this;
        });
    }

    public static function builder(): void
    {
        if (class_exists(ScoutBuilder::class)) {
            Builder::macro('paginateSafe', function ($perPage = null, $pageName = 'page', $page = null) {
                $engine = $this->engine();

                if ($engine instanceof PaginatesEloquentModels) {
                    return $engine->paginate($this, $perPage, $page)->appends('query', $this->query);
                }

                $page = $page ?: Paginator::resolveCurrentPage($pageName);

                $perPage = $perPage ?: $this->model->getPerPage();

                $results = $this->model->newCollection(
                    $engine->map(
                        $this,
                        $rawResults = $engine->paginate($this, $perPage, $page),
                        $this->model
                    )->all()
                );

                return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
                    'items' => $results,
                    'total' => $engine->getTotalCount($rawResults),
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'options' => [
                        'path' => Paginator::resolveCurrentPath(),
                        'pageName' => $pageName,
                    ],
                ])->appends('query', $this->query);
            });
        }
    }
}
