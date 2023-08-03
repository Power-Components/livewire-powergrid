<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\{Collection, Js};
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
            $attribute = strval(data_get($button, 'dynamicProperties.attribute'));
            $value     = strval(data_get($button, 'dynamicProperties.value'));

            $value = str($value)->replace('{primaryKey}', $row->{$this->component->primaryKey});

            //            if (View::exists($component)) {
            //                return Blade::render($component, ...$button);
            //            }

            $attributes = new ComponentAttributeBag([
                'class' => data_get($button, 'class'),
                'id'    => data_get($button, 'id'),
            ]);

            if (method_exists($this->component, 'actionRules')) {
                $applyRules = $this->applyRules($button);

                // attribute
                if ($attributeBag = data_get($applyRules, 'attribute')) {
                    $attributes = $attributes->merge($attributeBag->getAttributes());
                }

                // blade
                if ($html = data_get($applyRules, 'blade')) {
                    return (object) [
                        'render-action.' . $index . '.' . data_get($button, 'action') => $html,
                    ];
                }
            }

            if (filled($attribute) && filled($value)) {
                $attributes->merge([
                    $attribute => $value,
                ]);
            }

            $attributeString = implode(
                ' ',
                array_map(
                    fn ($key, $value) => "$key=\"$value\"",
                    array_keys($attributes->getAttributes()),
                    $attributes->getAttributes()
                )
            );

            $slot = strval(data_get($button, 'slot'));

            $element = "<{$component} {$attributeString}>{$slot}</{$component}>";

            return (object) [
                'render-action.' . $index . '.' . data_get($button, 'action') => Blade::render($element),
            ];
        });
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
            strval(data_get($button, 'class'))
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

        $attributeBag = new ComponentAttributeBag([]);

        if ($rules) {
            foreach ($rules as $rule) {
                //                $ruleRedirect       = (array) data_get($rule, 'redirect', []);
                //                $ruleDisabled       = boolval(data_get($rule, 'disable', false));
                //                $ruleHide           = boolval(data_get($rule, 'hide', false));
                //                $ruleEmit           = (array) data_get($rule, 'dispatch', []);
                //                $ruleEmitTo         = (array) data_get($rule, 'dispatchTo', []);
                //                $ruleCaption        = strval(data_get($rule, 'caption'));

                // setAttributes
                $ruleAttributes = (array)(data_get($rule, 'setAttribute', []));
                $attributeValue = null;

                /** @var array $ruleAttribute */
                foreach ($ruleAttributes as $ruleAttribute) {
                    if (is_string($ruleAttribute['value'])) {
                        $attributeValue = $ruleAttribute['value'];
                    }

                    if (is_array($ruleAttribute['value'])) {
                        if (is_array($ruleAttribute['value'][1])) {
                            $attributeValue = $ruleAttribute['value'][0] . '(' . Js::from($ruleAttribute['value'][1]) . ')';
                        } else {
                            $attributeValue = $ruleAttribute['value'][0] . '(' . $ruleAttribute['value'][1] . ')';
                        }
                    }

                    $attributeBag = $attributeBag->merge([$ruleAttribute['attribute'] => $attributeValue]);
                }
            }
        }

        return [
            'attribute' => $attributeBag,
        ];
    }
}
