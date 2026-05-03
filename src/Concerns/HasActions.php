<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\{Blade, Cache};
use Illuminate\View\ComponentAttributeBag;

trait HasActions
{
    public function prepareActionRulesForRows(mixed $row, ?object $loop = null): array
    {
        if (! method_exists($this, 'actionRules')) {
            return [];
        }

        $closure = function ($row, $loop) {
            /** @var array $rules */
            $rules = $this->actionRules($row);

            return collect($rules)
                ->transform(function ($rule) use ($row, $loop) {
                    $closureWhen = data_get($rule, 'rule.when');
                    $closureLoop = data_get($rule, 'rule.loop');
                    $attributes = data_get($rule, 'rule.setAttribute');

                    $apply = is_callable($closureWhen) ? $closureWhen($row) : false;
                    $applyLoop = is_callable($closureLoop) ? $closureLoop($loop) : false;

                    if (is_array($attributes) && isset($attributes['attribute']) && isset($attributes['value'])) {
                        $attributes = [
                            $attributes['attribute'] => $attributes['value'],
                        ];
                    }

                    $showToggleDetail = $apply && data_get($rule, 'rule.toggleDetailVisibility')
                        ? data_get($rule, 'rule.toggleDetailVisibility') === 'show'
                        : (bool) data_get($this->setUp, 'detail.showCollapseIcon');

                    $toggleableVisibility = $apply ? data_get($rule, 'rule.toggleableVisibility') : [];
                    $editOnClickVisibility = $apply ? data_get($rule, 'rule.editOnClickVisibility') : [];
                    $fieldHideEditOnClick = $apply && (bool) data_get($rule, 'rule.fieldHideEditOnClick');
                    $fieldHideToggleable = $apply && (bool) data_get($rule, 'rule.fieldHideToggleable');
                    $disabled = $apply && (bool) data_get($rule, 'rule.disable');
                    $hide = $apply && (bool) data_get($rule, 'rule.hide');
                    $detailView = (array) data_get($rule, 'rule.detailView', []);

                    if ($apply || $applyLoop) {
                        return [
                            'forAction' => strval(data_get($rule, 'forAction')),
                            'apply' => (bool) $apply,
                            'applyLoop' => (bool) $applyLoop,
                            'attributes' => $attributes,
                            'disable' => $disabled,
                            'hide' => $hide,
                            'toggleableVisibility' => $toggleableVisibility,
                            'toggleDetailView' => theme('root').($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                            'editOnClickVisibility' => $editOnClickVisibility,
                            'fieldHideEditOnClick' => $fieldHideEditOnClick,
                            'fieldHideToggleable' => $fieldHideToggleable,
                            ...$detailView,
                        ];
                    }

                    return [
                        'toggleableVisibility' => $toggleableVisibility,
                        'toggleDetailView' => theme('root').($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                    ];
                })
                ->toArray();
        };

        $value = strval(data_get($row, $this->realPrimaryKey()));
        $cacheKey = "pg-prepare-action-rules-for-rows-{$this->getId()}-{$value}}";

        if (intval(config('livewire-powergrid.cache_ttl') > 0)) {
            /** @var array $formattedRules */
            $formattedRules = Cache::remember($cacheKey, intval(config('livewire-powergrid.cache_ttl')), function () use ($closure, $row, $loop) {
                $value = $closure($row, $loop);

                return array_filter($value, function ($item) {
                    return ! empty($item);
                });
            });
        } else {
            $value = $closure($row, $loop);

            $formattedRules = array_filter($value, function ($item) {
                return ! empty($item);
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
                            'params' => new ComponentAttributeBag(
                                array_merge((array) data_get($bladeComponent, 'params'))
                            ),
                        ],
                    );

                    data_forget($rule, 'rule.bladeComponent');
                }

                return [
                    'action' => data_get($rule, 'forAction'),
                    'apply' => $apply,
                    'column' => $rule->column, // @phpstan-ignore-line
                    'rule' => data_get($rule, 'rule'),
                    'replaceHtml' => $html ?? '',
                ];
            })
            ->all();
    }
}
