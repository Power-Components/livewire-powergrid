<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;
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
            $encoded = Js::from([
                'component' => $component,
                'arguments' => $params,
            ]);

            $this->dynamicProperties['openModal'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatch('openModal', $encoded)",
            ];

            return $this;
        });

        Button::macro('toggleDetail', function () {
            $this->dynamicProperties['toggleDetail'] = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => function ($component, $row) {
                    return 'toggleDetail(\'' . data_get($row, $component->primaryKey) . '\')';
                },
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
                "component" => "a",
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

            $this->dynamicProperties['render'] = Blade::render('<form target="' . $target . '" action="' . $route . '" method="post">
    @method("' . $method . '")
    @csrf
    <button type="submit" class="' . $this->class . '">' . $this->slot . '</button>
</form>');

            return $this;
        });

        Button::macro('bladeComponent', function (string $component, array $params = []) {
            $this->dynamicProperties['component'] = [
                'component' => $component,
                'params'    => $params,
            ];

            return $this;
        });

        Button::macro('id', function (string $id = null) {
            $this->dynamicProperties['id'] = [
                "attribute" => "id",
                "value"     => function ($component, $row) use ($id) {
                    return $id ? $id . '-' . data_get($row, $component->primaryKey) : data_get($row, $component->primaryKey);
                },
            ];

            return $this;
        });

        Button::macro('hideWhen', function (\Closure $closure) {
            $this->dynamicProperties['hide'] = $closure;

            return $this;
        });

        Button::macro('showWhen', function (\Closure $closure) {
            $this->dynamicProperties['show'] = $closure;

            return $this;
        });

        Button::macro('can', function (bool|\Closure $allowed = true) {
            if ($allowed instanceof \Closure) {
                $this->dynamicProperties['show'] = $allowed;
            } else {
                $this->dynamicProperties['show'] = fn () => $allowed;
            }

            $this->dynamicProperties['can'] = $allowed;

            return $this;
        });
    }
}
