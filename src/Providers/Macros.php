<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\Js;
use Laravel\Scout\Contracts\PaginatesEloquentModels;
use Laravel\Scout\{Builder, Builder as ScoutBuilder};
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleActions;
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use PowerComponents\LivewirePowerGrid\{Button, Column, PowerGridComponent};

class Macros
{
    public static function columns(): void
    {
        Column::macro('withSum', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.sum.label', $label);
            data_set($this->properties, 'summarize.sum.header', $header);
            data_set($this->properties, 'summarize.sum.footer', $footer);

            return $this;
        });

        Column::macro('withCount', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.count.label', $label);
            data_set($this->properties, 'summarize.count.header', $header);
            data_set($this->properties, 'summarize.count.footer', $footer);

            return $this;
        });

        Column::macro('withAvg', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.avg.label', $label);
            data_set($this->properties, 'summarize.avg.header', $header);
            data_set($this->properties, 'summarize.avg.footer', $footer);

            return $this;
        });

        Column::macro('withMin', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.min.label', $label);
            data_set($this->properties, 'summarize.min.header', $header);
            data_set($this->properties, 'summarize.min.footer', $footer);

            return $this;
        });

        Column::macro('withMax', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.max.label', $label);
            data_set($this->properties, 'summarize.max.header', $header);
            data_set($this->properties, 'summarize.max.footer', $footer);

            return $this;
        });

        Column::macro('searchableRaw', function (string $sql = ''): Column {
            /** @var Column $this */
            $field = $this->dataField ?: $this->field;

            $this->rawQueries[] = [
                'method'   => 'orWhereRaw',
                'sql'      => $sql,
                'bindings' => [function (PowerGridComponent $component) use ($field): string {
                    $search      = $component->search;
                    $fieldMethod = 'beforeSearch' . str($field)->camel()->ucfirst();

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

        Column::macro('searchableJson', function (string $tableName) {
            $this->rawQueries[] = [
                'method'   => 'orWhereRaw',
                'sql'      => $tableName ? "LOWER(`$tableName`.`$this->dataField`) like ?" : "LOWER(`$this->dataField`) like ?",
                'bindings' => [function (PowerGridComponent $component) {
                    $search = htmlspecialchars($component->search, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    return "%" . strtolower($search) . "%";
                }],
                'enabled' => function (PowerGridComponent $component) {
                    return filled($component->search);
                },
            ];

            return $this;
        });

        Column::macro('naturalSort', function (bool $when = false, ?string $tableName = null) {
            $this->enableSort();

            if ($when) {
                $this->rawQueries[] = [
                    'method' => 'orderByRaw',
                    'sql'    => $tableName
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
        Button::macro('class', function (string $classes) {
            $this->attributes([
                'class' => $classes,
            ]);

            return $this;
        });

        Button::macro('call', function (string $method, array $params) {
            $this->attributes([
                'wire:click' => "\$call('{$method}', " . Js::from($params) . ")",
            ]);

            return $this;
        });

        Button::macro('dispatch', function (string $event, array $params) {
            $this->attributes([
                'wire:click' => "\$dispatch('{$event}', " . Js::from($params) . ")",
            ]);

            return $this;
        });

        Button::macro('dispatchTo', function (string $component, string $event, array $params) {
            $this->attributes([
                'wire:click' => "\$dispatchTo('{$component}', '{$event}', " . Js::from($params) . ")",
            ]);

            return $this;
        });

        Button::macro('dispatchSelf', function (string $event, array $params) {
            $this->attributes([
                'wire:click' => "\$dispatchSelf('{$event}', " . Js::from($params) . ")",
            ]);

            return $this;
        });

        Button::macro('parent', function (string $method, array $params) {
            $this->attributes([
                'wire:click' => "\$parent.{$method}(" . Js::from($params) . ")",
            ]);

            return $this;
        });

        Button::macro('openModal', function (string $component, array $params) {
            $encoded = Js::from([
                'component' => $component,
                'arguments' => $params,
            ]);

            $this->attributes([
                'wire:click' => "\$dispatch('openModal', $encoded)",
            ]);

            return $this;
        });

        Button::macro('disable', function (bool $disable = true) {
            if ($disable) {
                $this->attributes([
                    'disabled' => 'disabled',
                ]);
            }

            return $this;
        });

        Button::macro('tooltip', function (string $value) {
            $this->attributes([
                'title' => $value,
            ]);

            return $this;
        });

        Button::macro('route', function (string $route, array $params, string $target = '_self') {
            $this->tag('a');

            $this->attributes([
                'href'   => route($route, $params),
                'target' => $target,
            ]);

            return $this;
        });

        Button::macro('id', function (?string $id = null) {
            $this->attributes([
                'id' => $id,
            ]);

            return $this;
        });

        Button::macro('can', function (bool|\Closure $closure) {
            $this->can = $closure;

            return $this;
        });

        Button::macro('confirm', function (?string $message = null) {
            $this->attributes([
                'wire:confirm' => $message ?? trans('livewire-powergrid::datatable.buttons_macros.confirm.message'),
            ]);

            return $this;
        });

        Button::macro('confirmPrompt', function (?string $message = null, string $confirmValue = 'Confirm') {
            $message      = $message ?? trans('livewire-powergrid::datatable.buttons_macros.confirm_prompt.message', ['confirm_value' => $confirmValue]);
            $confirmValue = trim($confirmValue);

            $this->attributes([
                'wire:confirm.prompt' => "$message | $confirmValue",
            ]);

            return $this;
        });

        Button::macro('toggleDetail', function (int|string $rowId) {
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
                $engine = $this->engine(); // @phpstan-ignore-line

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
}
