<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\Cache;

trait HasActions
{
    public function prepareActionRulesForRows(mixed $row, object $loop): array
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

        return Cache::remember($cacheKey, 3600, fn () => $closure($row, $loop));
    }
}
