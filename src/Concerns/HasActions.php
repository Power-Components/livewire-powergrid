<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\{Blade, Cache, File, View};
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Button;

trait HasActions
{
    public bool $withoutResourcesActions = false;

    public function prepareActionsResources(): ?string
    {
        if ($this->withoutResourcesActions) {
            return null;
        }

        /** @var string $records */
        $records = !app()->hasDebugModeEnabled() ? Cache::remember('pg-resource-icons-json', intval(config('livewire-powergrid.cache_ttl')), function (): string {
            return $this->getResourceIconsJson();
        }) : $this->getResourceIconsJson();

        $this->js(<<<JS
            this.pgResourceIcons = $records
        JS);

        return $records;
    }

    private function getResourceIconsJson(): string
    {
        $paths          = (array) config('livewire-powergrid.icon_resources.paths');
        $allowed        = (array) config('livewire-powergrid.icon_resources.allowed');
        $iconAttributes = (array) config('livewire-powergrid.icon_resources.attributes');

        $icons = [];

        foreach ($paths as $key => $path) {
            $fullPath = base_path(strval($path));
            $files    = File::allFiles($fullPath);

            foreach ($files as $file) {
                $name = Str::replaceLast('.blade.php', '', $file->getFilename());

                if (blank($allowed) || in_array($name, $allowed)) {
                    $attributes = new ComponentAttributeBag($iconAttributes);

                    $icons["{$key}-{$name}"] = View::file($file->getRealPath(), [
                        'attributes' => $attributes,
                    ])->render();
                }
            }
        }

        return strval(json_encode($icons));
    }

    public function storeActionsRowInJSWindow(mixed $data): void
    {
        if (!method_exists($this, 'actions')) {
            return;
        }

        $actionsHtml = [];

        $data->each(function ($row) use (&$actionsHtml) {
            $hasCookieActionsForRow = isset($_COOKIE['pg_cookie_' . $this->tableName . '_row_' . data_get($row, $this->realPrimaryKey)]);

            if (!$hasCookieActionsForRow) {
                $actions = collect($this->actions($row)) // @phpstan-ignore-line
                    ->transform(function (Button|array $action) use ($row) {
                        return [
                            'slot'           => data_get($action, 'slot'),
                            'tag'            => data_get($action, 'tag'),
                            'icon'           => data_get($action, 'icon'),
                            'iconAttributes' => data_get($action, 'iconAttributes'),
                            'attributes'     => data_get($action, 'attributes'),
                            'rules'          => $this->resolveActionRules($action, $row),
                        ];
                    });

                $actionsHtml[data_get($row, $this->realPrimaryKey)] = $actions->toArray();
            }
        });

        $actionsHtml = json_encode($actionsHtml);

        $this->js(<<<JS
            this[`pgActions_\${\$wire.id}`] = $actionsHtml
        JS);

        $actionsHtml = [];
    }

    public function storeActionsHeaderInJSWindow(): void
    {
        if (!method_exists($this, 'header')) {
            return;
        }

        $actionsHtml = collect($this->header())
        ->transform(function (Button $action) {
            return [
                'slot'           => $action->slot,
                'tag'            => $action->tag,
                'icon'           => $action->icon,
                'iconAttributes' => $action->iconAttributes,
                'attributes'     => $action->attributes,
                'rules'          => [],
            ];
        });

        $actionsHtml = json_encode($actionsHtml);

        $this->js(<<<JS
            this[`pgActionsHeader_\${\$wire.id}`] = $actionsHtml
        JS);
    }

    public function prepareActionRulesForRows(mixed $row, ?object $loop = null): array
    {
        if (!method_exists($this, 'actionRules')) {
            return [];
        }

        $closure = function ($row, $loop) {
            /** @var array $rules */
            $rules = $this->actionRules($row);

            return collect($rules)
                ->transform(function ($rule) use ($row, $loop) {
                    $closureWhen = data_get($rule, 'rule.when');
                    $closureLoop = data_get($rule, 'rule.loop');
                    $attributes  = data_get($rule, 'rule.setAttribute');

                    $apply     = is_callable($closureWhen) ? $closureWhen($row) : false;
                    $applyLoop = is_callable($closureLoop) ? $closureLoop($loop) : false;

                    if (is_array($attributes) && isset($attributes['attribute']) && isset($attributes['value'])) {
                        $attributes = [
                            $attributes['attribute'] => $attributes['value'],
                        ];
                    }

                    $showToggleDetail      = data_get($rule, 'rule.toggleableVisibility') === 'show' || (bool) data_get($this->setUp, 'detail.showCollapseIcon');
                    $toggleableVisibility  = $apply ? data_get($rule, 'rule.toggleableVisibility') : [];
                    $editOnClickVisibility = $apply ? data_get($rule, 'rule.editOnClickVisibility') : [];
                    $fieldHideEditOnClick  = $apply && (bool) data_get($rule, 'rule.fieldHideEditOnClick');
                    $fieldHideToggleable   = $apply && (bool) data_get($rule, 'rule.fieldHideToggleable');
                    $disabled              = $apply && (bool) data_get($rule, 'rule.disable');
                    $hide                  = $apply && (bool) data_get($rule, 'rule.hide');

                    if ($apply || $applyLoop) {
                        return [
                            'forAction'             => strval(data_get($rule, 'forAction')),
                            'apply'                 => (bool) $apply,
                            'applyLoop'             => (bool) $applyLoop,
                            'attributes'            => $attributes,
                            'disable'               => $disabled,
                            'hide'                  => $hide,
                            'toggleableVisibility'  => $toggleableVisibility,
                            'toggleDetailView'      => powerGridThemeRoot() . ($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                            'editOnClickVisibility' => $editOnClickVisibility,
                            'fieldHideEditOnClick'  => $fieldHideEditOnClick,
                            'fieldHideToggleable'   => $fieldHideToggleable,
                        ];
                    }

                    return [
                        'toggleableVisibility' => $toggleableVisibility,
                        'toggleDetailView'     => powerGridThemeRoot() . ($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                    ];
                })
                ->toArray();
        };

        $value    = strval(data_get($row, $this->realPrimaryKey));
        $cacheKey = "pg-prepare-action-rules-for-rows-{$this->getId()}-{$value}}";

        /** @var array $formattedRules */
        $formattedRules = Cache::remember($cacheKey, intval(config('livewire-powergrid.cache_ttl')), function () use ($closure, $row, $loop) {
            $value = $closure($row, $loop);

            return array_filter($value, function ($item) {
                return !empty($item);
            });
        });

        return $formattedRules;
    }

    public function resolveActionRules(Button|array $action, mixed $row): array
    {
        if (!method_exists($this, 'actionRules')) {
            return [];
        }

        return collect($this->actionRules($row)) // @phpstan-ignore-line
            ->transform(function ($rule) use ($row) {
                $when = data_get($rule, 'rule.when');
                $loop = data_get($rule, 'rule.loop');

                $apply = $when ? $when($row) : false; // @phpstan-ignore-line
                $apply = !$apply && $loop ? $loop($row) : false; // @phpstan-ignore-line

                data_forget($rule, 'rule.when');

                if ($bladeComponent = data_get($rule, 'rule.bladeComponent')) {
                    $html = Blade::render(
                        '<x-dynamic-component :component="$component" :attributes="$params" />',
                        [
                            'component' => data_get($bladeComponent, 'component'),
                            'params'    => new ComponentAttributeBag(
                                array_merge((array)data_get($bladeComponent, 'params'))
                            ),
                        ],
                    );
                }

                return [
                    'action'      => data_get($rule, 'forAction'),
                    'apply'       => $apply,
                    'column'      => $rule->column,
                    'rule'        => data_get($rule, 'rule'),
                    'replaceHtml' => $html ?? '',
                ];
            })
            ->toArray();
    }
}
