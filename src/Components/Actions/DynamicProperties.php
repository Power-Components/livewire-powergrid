<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Button;

class DynamicProperties
{
    public function __construct(
        public array|Button $button,
        public int $index,
        public null|object|array $row,
        public ComponentAttributeBag $attributes
    ) {
    }

    public function show(): ?array
    {
        $show = data_get($this->button, 'dynamicProperties.show');

        if ($show instanceof \Closure) {
            if (!$show($this->row)) {
                return [
                    'render-action.' . $this->index . '.' . data_get($this->button, 'action') => null,
                ];
            }
        }

        return null;
    }

    public function hide(): ?array
    {
        $hide = data_get($this->button, 'dynamicProperties.hide');

        if ($hide instanceof \Closure) {
            if ($hide($this->row)) {
                return [
                    'render-action.' . $this->index . '.' . data_get($this->button, 'action') => null,
                ];
            }
        }

        return null;
    }

    public function render(): ?array
    {
        $render = data_get($this->button, 'dynamicProperties.render');

        if ($render instanceof \Closure) {
            return [
                'render-action.' . $this->index . '.' . data_get($this->button, 'action') => $render($this->row),
            ];
        }

        if (is_string($render)) {
            return [
                'render-action.' . $this->index . '.' . data_get($this->button, 'action') => $render,
            ];
        }

        return null;
    }

    public function renderComponent(): ?array
    {
        $renderComponent = data_get($this->button, 'dynamicProperties.component');

        if ($renderComponent) {
            $html = Blade::render(
                '<x-dynamic-component
                        :component="$component"
                        :attributes="$params"
                        />',
                [
                    'component' => data_get($renderComponent, 'component'),
                    'params'    => $this->attributes->merge(
                        (array) data_get($renderComponent, 'params')
                    ),
                ],
            );

            return [
                'render-action.' . $this->index . '.' . data_get($this->button, 'action') => $html,
            ];
        }

        return null;
    }
}
