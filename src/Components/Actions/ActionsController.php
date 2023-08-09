<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
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

    public function execute(array $actions, object|array $row): Collection
    {
        $actions = collect($actions);

        /** @phpstan-ignore-next-line */
        return $actions->mapWithKeys(function (array|Button $button, $index) use ($row) {
            $component = strval(data_get($button, 'dynamicProperties.component', 'button'));

            //            if (View::exists($component)) {
            //                return Blade::render($component, ...$button);
            //            }

            $render = data_get($button, 'dynamicProperties.render');

            if ($render instanceof \Closure) {
                return (object)[
                    'render-action.' . $index . '.' . data_get($button, 'action') => $render($row),
                ];
            }

            if (is_string($render)) {
                return (object)[
                    'render-action.' . $index . '.' . data_get($button, 'action') => $render,
                ];
            }

            $attributes = $this->extractAttributes($button, $row);

            $slotRule = null;

            if (method_exists($this->component, 'actionRules')) {
                $applyRules = $this->applyRules($button);

                // attribute
                if ($attributesRule = (array) data_get($applyRules, 'attributes')) {
                    $attributes = $attributes->merge((new ComponentAttributeBag($attributesRule))->getAttributes());
                }

                $slotRule = data_get($applyRules, 'slot');

                // blade
                if ($html = data_get($applyRules, 'blade')) {
                    return (object)[
                        'render-action.' . $index . '.' . data_get($button, 'action') => $html,
                    ];
                }
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

            $element = "<{$component} {$attributeString}>{$slot}</{$component}>";

            return (object)[
                'render-action.' . $index . '.' . data_get($button, 'action') => Blade::render($element),
            ];
        });
    }

    protected function extractAttributes(array|Button $button, object|array $row): ComponentAttributeBag
    {
        $attributes = new ComponentAttributeBag();

        if (filled(data_get($button, 'class'))) {
            $attributes = $attributes->merge([
                'class' => data_get($button, 'class'),
            ]);
        }

        if (filled(data_get($button, 'id'))) {
            $attributes = $attributes->merge([
                'id' => data_get($button, 'id'),
            ]);
        }

        foreach ((array) data_get($button, 'dynamicProperties') as $dynamicProperties) {
            $attribute = strval(data_get($dynamicProperties, 'attribute'));
            $value     = strval(data_get($dynamicProperties, 'value'));

            $value = str($value)->replace('{primaryKey}', data_get($row, $this->component->primaryKey));

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
                        $ruleInstance->apply($ruleData, $output);
                    }
                }
            }
        }

        return $output;
    }
}
