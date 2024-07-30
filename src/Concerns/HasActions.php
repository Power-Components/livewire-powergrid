<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\{Blade, Cache, File, View};
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Button;

trait HasActions
{
    public function prepareActionsResources(): string
    {
        $records = $this->getResourceIconsJson();

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

        return json_encode($icons);
    }

    public function storeActionsInJSWindow(mixed $data): void
    {
        if (!method_exists($this, 'actions')) {
            return;
        }

        $actionsHtml = [];

        $data->each(function ($row) use (&$actionsHtml) {
            if (!isset($_COOKIE['pg_cookie_' . $this->tableName . '_row_' . data_get($row, $this->realPrimaryKey)])) {
                $actions = collect($this->actions($row)) // @phpstan-ignore-line
                    ->transform(function (Button $action) use ($row) {
                        return [
                            'slot'           => $action->slot,
                            'icon'           => $action->icon,
                            'iconAttributes' => $action->iconAttributes,
                            'attributes'     => $action->attributes,
                            'rules'          => method_exists($this, 'actionRules') ? collect($this->actionRules($row)) // @phpstan-ignore-line
                                ->where('forAction', $action->action)
                                ->transform(function ($rule) use ($row) {
                                    $when  = data_get($rule, 'rule.when');
                                    $apply = $when($row); // @phpstan-ignore-line

                                    data_forget($rule, 'rule.when');

                                    if ($bladeComponent = data_get($rule, 'rule.bladeComponent')) {
                                        $html = Blade::render(
                                            '<x-dynamic-component
                                                    :component="$component"
                                                    :attributes="$params"
                                                    />',
                                            [
                                                'component' => data_get($bladeComponent, 'component'),
                                                'params'    => new ComponentAttributeBag(
                                                    array_merge((array) data_get($bladeComponent, 'params'))
                                                ),
                                            ],
                                        );
                                    }

                                    return [
                                        'apply'       => $apply,
                                        'column'      => $rule->column,
                                        'rule'        => data_get($rule, 'rule'),
                                        'replaceHtml' => $html ?? '',
                                    ];
                                }) : [],
                        ];
                    });

                $actionsHtml[data_get($row, $this->realPrimaryKey)] = $actions->toArray();
            }
        });

        $actionsHtml = json_encode($actionsHtml);

        $this->js(<<<JS
            this[`pgActions-\${\$wire.id}`] = $actionsHtml
        JS);

        $actionsHtml = [];
    }

    public function prepareActionRulesForRows(mixed $row, object $loop): array
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

                    $showToggleDetail      = data_get($rule, 'rule.toggleableVisibility') === 'show' && (bool) data_get($this->setUp, 'detail.showCollapseIcon');
                    $toggleableVisibility  = $apply ? data_get($rule, 'rule.toggleableVisibility') : [];
                    $editOnClickVisibility = $apply ? data_get($rule, 'rule.editOnClickVisibility') : [];
                    $fieldHideEditOnClick  = $apply && (bool) data_get($rule, 'rule.fieldHideEditOnClick');
                    $fieldHideToggleable   = $apply && (bool) data_get($rule, 'rule.fieldHideToggleable');

                    return [
                        'apply'      => (bool) $apply,
                        'applyLoop'  => (bool) $applyLoop,
                        'attributes' => $attributes,

                        'toggleableVisibility'  => $toggleableVisibility,
                        'toggleDetailView'      => powerGridThemeRoot() . ($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                        'editOnClickVisibility' => $editOnClickVisibility,
                        'fieldHideEditOnClick'  => $fieldHideEditOnClick,
                        'fieldHideToggleable'   => $fieldHideToggleable,
                    ];
                })
                ->toArray();
        };

        $cacheKey = "pg-prepare-action-rules-for-rows-{$this->getId()}-{$row->{$this->realPrimaryKey}}";

        return Cache::remember($cacheKey, intval(config('livewire-powergrid.cache_ttl')), fn () => $closure($row, $loop));
    }
}
