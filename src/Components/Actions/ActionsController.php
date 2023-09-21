<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\{Arr, Collection};
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\{Button, PowerGridComponent, Traits\UnDot};

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

                // attribute
                if ($attributesRule = (array) data_get($applyRules, 'attributes')) {
                    $attributes = $attributes->merge((new ComponentAttributeBag($attributesRule))->getAttributes());

                    if ($remove = strval(data_get($applyRules, 'remove'))) {
                        $attributes = $attributes->except($remove);
                    }

                    if ($componentRule = strval(data_get($applyRules, 'component'))) {
                        $component = $componentRule;
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
        $rules = (array) $this->prepareRules->get(
            strval(data_get($button, 'action'))
        );

        $bladeComponent = (array) data_get($rules, 'action.bladeComponent', []);

        if ($bladeComponent) {
            return [
                'blade' => Blade::render('<x-dynamic-component
                :component="$component"
                :attributes="$params"
                />', [
                    'component' => $bladeComponent['component'],
                    'params'    => new ComponentAttributeBag((array) $bladeComponent['params']),
                ]),
            ];
        }

        $output = [];

        if ($rules) {
            /** @var array $ruleData */
            foreach ($rules as $ruleData) {
                foreach ($ruleData as $key => $value) {
                    $ruleClass = "PowerComponents\\LivewirePowerGrid\\Components\\Rules\\Support\\" . ucfirst($key) . 'Rule';

                    if (class_exists($ruleClass)) {
                        $ruleInstance = new $ruleClass();
                        /** @phpstan-ignore-next-line */
                        $ruleInstance->apply($ruleData, $output);
                    }
                }
            }
        }

        return $output;
    }
}
