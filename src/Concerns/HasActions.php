<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\{Blade, Cache, File, View};
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\DataSourceBase;

trait HasActions
{
    public bool $withoutResourcesActions = false;

    public function prepareActionsResources(): ?string
    {
        if ($this->withoutResourcesActions) {
            return null;
        }

        $closure = fn () => $this->getResourceIconsJson();

        if (intval(config('livewire-powergrid.cache_ttl')) > 0) {
            $records = strval(Cache::remember('pg-resource-icons-json', intval(config('livewire-powergrid.cache_ttl')), $closure));
        } else {
            $records = (string) $closure();
        }

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

    public function storeActionsRowInJSWindow(): void
    {
        if (!method_exists($this, 'actions')) {
            return;
        }

        $actionsHtml = json_encode(DataSourceBase::$actionsHtml);

        $this->js(<<<JS
            this[`pgActions_\${\$wire.id}`] = $actionsHtml
        JS);
    }

    public function storeActionsHeaderInJSWindow(): void
    {
        $actionsHtml = collect($this->header())
            ->transform(function (Button $action) {
                $can = data_get($action, 'can');

                return [
                    'action'         => $action->action,
                    'slot'           => $action->slot,
                    'tag'            => $action->tag,
                    'icon'           => $action->icon,
                    'iconAttributes' => $action->iconAttributes,
                    'attributes'     => $action->attributes,
                    'rules'          => [],
                    'can'            => $can instanceof \Closure ? $can() : $can,
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

                    $showToggleDetail = $apply && data_get($rule, 'rule.toggleDetailVisibility')
                        ? data_get($rule, 'rule.toggleDetailVisibility') === 'show'
                        : (bool) data_get($this->setUp, 'detail.showCollapseIcon');

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
                            'toggleDetailView'      => $this->theme['root'] . ($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                            'editOnClickVisibility' => $editOnClickVisibility,
                            'fieldHideEditOnClick'  => $fieldHideEditOnClick,
                            'fieldHideToggleable'   => $fieldHideToggleable,
                        ];
                    }

                    return [
                        'toggleableVisibility' => $toggleableVisibility,
                        'toggleDetailView'     => $this->theme['root'] . ($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                    ];
                })
                ->toArray();
        };

        $value    = strval(data_get($row, $this->realPrimaryKey));
        $cacheKey = "pg-prepare-action-rules-for-rows-{$this->getId()}-{$value}}";

        if (intval(config('livewire-powergrid.cache_ttl') > 0)) {
            /** @var array $formattedRules */
            $formattedRules = Cache::remember($cacheKey, intval(config('livewire-powergrid.cache_ttl')), function () use ($closure, $row, $loop) {
                $value = $closure($row, $loop);

                return array_filter($value, function ($item) {
                    return !empty($item);
                });
            });
        } else {
            $value = $closure($row, $loop);

            $formattedRules = array_filter($value, function ($item) {
                return !empty($item);
            });
        }

        return $formattedRules;
    }

    public function resolveActionRules(mixed $row): array
    {
        return collect($this->actionRules($row)) // @phpstan-ignore-line
            ->transform(function ($rule) use ($row) {
                $when = data_get($rule, 'rule.when');
                $loop = data_get($rule, 'rule.loop');

                $apply = $when ? $when($row) : false; // @phpstan-ignore-line

                if (data_get($rule, 'rule.loop')) {
                    $apply = $loop ? $loop($row) : false; // @phpstan-ignore-line
                }

                data_forget($rule, 'rule.when');

                if ($bladeComponent = data_get($rule, 'rule.bladeComponent')) {
                    $html = Blade::render(
                        '<x-dynamic-component :component="$component" :attributes="$params" />',
                        [
                            'component' => data_get($bladeComponent, 'component'),
                            'params'    => new ComponentAttributeBag(
                                array_merge((array) data_get($bladeComponent, 'params'))
                            ),
                        ],
                    );

                    data_forget($rule, 'rule.bladeComponent');
                }

                return [
                    'action'      => data_get($rule, 'forAction'),
                    'apply'       => $apply,
                    'column'      => $rule->column, // @phpstan-ignore-line
                    'rule'        => data_get($rule, 'rule'),
                    'replaceHtml' => $html ?? '',
                ];
            })
            ->all();
    }
}
