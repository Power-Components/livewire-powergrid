<?php

namespace PowerComponents\LivewirePowerGrid\Components\Actions;

use Illuminate\Support\Js;
use PowerComponents\LivewirePowerGrid\Button;

class Macros
{
    public static function boot(): void
    {
        Button::macro('call', function (string $method, array $params) {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$call('{$method}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('dispatch', function (string $event, array $params) {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatch('{$event}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('dispatchTo', function (string $component, string $event, array $params) {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatchTo('{$component}', '{$event}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('dispatchSelf', function (string $event, array $params) {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatchSelf('{$event}', " . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('parent', function (string $method, array $params) {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$parent.{$method}(" . Js::from($params) . ")",
            ];

            return $this;
        });

        Button::macro('openModal', function (string $component, array $params) {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "\$dispatch('openModal', { component: {$component}, parameters: " . Js::from($params) . "})",
            ];

            return $this;
        });

        Button::macro('toggleDetail', function () {
            $this->dynamicProperties = [
                "component" => "button",
                "attribute" => "wire:click",
                "value"     => "toggleDetail({primaryKey})",
            ];

            return $this;
        });
    }
}
