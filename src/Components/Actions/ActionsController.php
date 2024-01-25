<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\{Button, Components\Concerns\UnDot, PowerGridComponent};

class ActionsController
{
    use UnDot;

    public function __construct(
        protected PowerGridComponent $component,
        protected Collection $prepareRules
    ) {
    }

    public function execute(array $actions, null|object|array $row = null): Collection
    {
        $actions = collect($actions);

        /** @phpstan-ignore-next-line */
        return $actions->mapWithKeys(function (array|Button $button, $index) use ($row) {
            $component = null;

            $attributes = $this->extractAttributes($button, $row);

            $slotRule = null;

            if (method_exists($this->component, 'actionRules')) {
                $applyRules = $this->applyRules($button);

                if (isset($applyRules['remove'])) {
                    $attributes = $attributes->except($applyRules['remove']);
                }

                if (isset($applyRules['component'])) {
                    $component = $applyRules['component'];
                }

                foreach ($applyRules as $key => $applyRule) {
                    if (is_array($applyRule) && count($applyRule) > 0) {
                        $attributes = $attributes->merge([
                            $key => implode(' ', $applyRule),
                        ]);
                    } else {
                        $attributes = $attributes->merge([
                            $key => $applyRule,
                        ]);
                    }
                }

                $slotRule = data_get($applyRules, 'slot');

                // blade
                if ($html = data_get($applyRules, 'blade')) {
                    return (object)[
                        'render-action.' . $index . '.' . data_get($button, 'action') => $html,
                    ];
                }

                if ((bool) data_get($applyRules, 'hide')) {
                    return (object)[
                        'render-action.' . $index . '.' . data_get($button, 'action') => null,
                    ];
                }
            }

            $dynamicProperties = new DynamicProperties($button, $index, $row, $attributes);

            $instance = new \ReflectionClass($dynamicProperties);

            foreach ($instance->getMethods(\ReflectionMethod::IS_PUBLIC) as $allowedProperty) {
                if ($allowedProperty->getName() == '__construct') {
                    continue;
                }

                $allowed = $dynamicProperties->{$allowedProperty->getName()}();

                if (is_null($allowed)) {
                    continue;
                }

                return (object) $allowed;
            }

            $attributeString = implode(
                ' ',
                array_map(
                    fn ($key, $value) => "$key=\"$value\"",
                    array_keys($attributes->getAttributes()),
                    $attributes->getAttributes()
                )
            );

            $slot = filled($slotRule) ? strval($slotRule) : strval(data_get($button, 'slot'));

            if (is_null($component)) {
                $component = strval(data_get($button, 'dynamicProperties.' . key((array) data_get($button, 'dynamicProperties')) . '.component', 'button'));
            }

            $element = "<{$component} {$attributeString}>{$slot}</{$component}>";

            return (object)[
                'render-action.' . $index . '.' . data_get($button, 'action') => Blade::render($element),
            ];
        });
    }

    protected function extractAttributes(array|Button $button, null|object|array $row = null): ComponentAttributeBag
    {
        $attributes = new ComponentAttributeBag();

        if (filled(data_get($button, 'class'))) {
            $attributes = $attributes->merge([
                'class' => data_get($button, 'class'),
            ]);
        }

        foreach ((array) data_get($button, 'dynamicProperties') as $dynamicProperties) {
            $attribute = strval(data_get($dynamicProperties, 'attribute'));
            $value     = data_get($dynamicProperties, 'value');

            if ($value instanceof \Closure) {
                $value = $value($this->component, $row);
            }

            if (filled($attribute) && filled($value)) {
                $attributes = $attributes->merge([
                    $attribute => $value,
                ]);
            }
        }

        return $attributes;
    }

    public function recoverFromButton(Button $button, Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $actions = $this->unDotActionsFromRow($row, 'render-action');

        $actions->each(function ($key) use (&$actionRules, $button) {
            $key = (array) $key;

            if (isset($key[$button->action])) {
                $action = (array) $key[$button->action];

                if (isset($action[0])) {
                    $actionRules['custom-action'] = $action[0];
                } else {
                    $actionRules['custom-action'] = $action;
                }
            }
        });

        return $actionRules;
    }

    public function applyRules(Button|array $button): array
    {
        $output = collect();

        $actionRules  = collect();
        $filterAction = strval(data_get($button, 'action'));

        $this->prepareRules->each(function ($rule, $target) use ($actionRules, $filterAction) {
            if (str_contains($target, $filterAction)) {
                $actionRules->push(data_get($rule, "action"));
            }
        });

        if ($actionRules->count()) {
            $getRulesOutPutFromClass = function ($key, $value) {
                $output    = [];
                $ruleClass = "PowerComponents\\LivewirePowerGrid\\Components\\Rules\\Support\\" . ucfirst($key) . 'Rule';

                if (class_exists($ruleClass)) {
                    $ruleInstance = new $ruleClass();
                    /** @phpstan-ignore-next-line */
                    $output[] = $ruleInstance->apply($value);
                }

                return $output;
            };

            $actionRules->each(function (?array $rule) use ($getRulesOutPutFromClass, $output) {
                if ($rule) {
                    collect($rule)->each(function ($rulesData, $key) use ($getRulesOutPutFromClass, $output) {
                        $output->push($getRulesOutPutFromClass(ucfirst($key), $rulesData));
                    });
                }
            });
        }

        $mergedAttributes = [];

        $output->each(function ($rule) use (&$mergedAttributes) {
            if (isset($rule[0]['attributes'])) {
                $mergedAttributes = array_merge_recursive($mergedAttributes, $rule[0]['attributes']);
            } else {
                $mergedAttributes = array_merge($rule[0] ?? []);
            }
        });

        return $mergedAttributes;
    }
}
