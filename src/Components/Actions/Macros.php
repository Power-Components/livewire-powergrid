<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Button;

class Macros
{
    public static function boot(): void
    {
        Button::macro('call', function (string $method, array $params) {
            $this->dynamicProperties['call'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$call('{$method}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('dispatch', function (string $event, array $params) {
            $this->dynamicProperties['dispatch'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatch('{$event}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('dispatchTo', function (string $component, string $event, array $params) {
            $this->dynamicProperties['dispatchTo'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatchTo('{$component}', '{$event}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('dispatchSelf', function (string $event, array $params) {
            $this->dynamicProperties['dispatchSelf'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatchSelf('{$event}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('parent', function (string $method, array $params) {
            $this->dynamicProperties['parent'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$parent.{$method}(" . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('openModal', function (string $component, array $params) {
            $this->dynamicProperties['openModal'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatch('openModal', { component: {$component}, parameters: " . Js::from($params) . "})",
            ];

            return $this;
        });

        Button::macro('toggleDetail', function () {
            $this->dynamicProperties['toggleDetail'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "toggleDetail({primaryKey})",
            ];

            return $this;
        });

        /** @todo */
        Button::macro('disable', function () {
            $this->dynamicProperties['disable'] = [
                "component" => "button",
                "attribute" => "disabled",
                "value"     => "disabled",
            ];

            return $this;
        });

        Button::macro('tooltip', function (string $value) {
            $this->dynamicProperties['tooltip'] = [
                "component" => "button",
                "attribute" => "title",
                "value"     => $value,
            ];

            return $this;
        });

        Button::macro('route', function (string $route, array $params) {
            $this->dynamicProperties['route'] = [
                "component" => "a",
                "attribute" => "href",
                "value"     => route($route, $params),
            ];

            return $this;
        });

        Button::macro('target', function (string $target) {
            $this->dynamicProperties['target'] = [
                "attribute" => "target",
                "value"     => $target,
            ];

            return $this;
        });

        Button::macro('render', function (\Closure $closure) {
            $this->dynamicProperties['render'] = $closure;

            return $this;
        });

        Button::macro('method', function (string $method) {
            $target = strval(data_get($this, 'dynamicProperties.target', '_self'));
            $route  = strval(data_get($this, 'dynamicProperties.route.value'));

            if (blank($route)) {
                return $this;
            }

            $this->dynamicProperties['render'] = <<<HTML
<form target="$target" action="$route" method="post">
    @method('$method')
    @csrf
    <button type="submit" class="$this->class" id="$this->id">$this->slot</button>
</form>
HTML;

            return $this;
        });

        Button::macro('bladeComponent', function (string $component, array $params = []) {
            $this->dynamicProperties['render'] = Blade::render('<x-dynamic-component
                :component="$component"
                :attributes="$params"
                />', [
                'component' => $component,
                'params'    => new ComponentAttributeBag((array) $params),
            ]);

            return $this;
        });
    }
}
