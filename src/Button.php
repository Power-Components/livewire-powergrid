<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use Illuminate\Support\Traits\Macroable;
use Livewire\Wireable;

/**
 * @method static dispatch(string $event, array $params)
 * @method static dispatchTo(string $component, string $event, array $params)
 * @method static dispatchSelf(string $event, array $params)
 * @method static openModal(string $component, array $params)
 * @method static parent(string $method, array $params)
 * @method static call(string $method, array $params)
 * @method static toggleDetail(int|string $rowId)
 * @method static tooltip(string $title)
 * @method static route(string $route, array $params, string $target)
 * @method static method(string $method)
 * @method static target(string $target) _blank, _self, _top, _parent, null
 * @method static can(bool|Closure $allowed = true)
 * @method static id(string $id = null)
 * @method static confirm(string $message = 'Are you sure you want to perform this action?')
 * @method static confirmPrompt(string $message = 'Are you sure you want to perform this action?', string $confirmValue = 'Confirm')
 * @method static class(string $classes)
 * @method static disable(bool $disable = true)
 */
final class Button implements Wireable
{
    use Macroable;

    public string $view = '';

    public array $attributes = [];

    public ?string $slot = '';

    public ?string $tag = 'button';

    public ?string $icon = '';

    public array $iconAttributes = [];

    public bool|Closure $can = true;

    public function __construct(public string $action)
    {
    }

    public static function add(string $action = ''): Button
    {
        return new Button($action);
    }

    public static function make(string $action, ?string $slot = null): self
    {
        return (new self($action))
            ->slot($slot);
    }

    public function tag(?string $tag = null): Button
    {
        $this->tag = $tag;

        return $this;
    }

    public function slot(?string $slot = null): Button
    {
        $this->slot = $slot;

        return $this;
    }

    public function view(string $view): Button
    {
        $this->view = $view;

        return $this;
    }

    public function attributes(array $attributes): Button
    {
        $this->attributes = array_merge($attributes, $this->attributes);

        return $this;
    }

    public function icon(string $icon, array $iconAttributes = []): Button
    {
        $this->icon           = $icon;
        $this->iconAttributes = $iconAttributes;

        return $this;
    }

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
