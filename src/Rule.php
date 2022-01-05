<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;

final class Rule
{
    public array $rule = [];

    public string $forAction = '';

    public function __construct(string $action)
    {
        $this->forAction = $action;
    }

    public static function for(string $action): Rule
    {
        return new Rule($action);
    }

    public function when(Closure $closure = null): Rule
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    public function unless(Closure $closure = null): Rule
    {
        $this->rule['unless'] = $closure;

        return $this;
    }

    public function wire(string $action = 'click', string $event = '', array $params = []): Rule
    {
        $this->rule['wire'] = [
            'action' => $action,
            'event'  => $event,
            'params' => $params,
        ];

        return $this;
    }

    public function setAttribute(string $attribute = null, string $value = null): Rule
    {
        $this->rule['setAttribute'] = [
            'attribute' => $attribute,
            'value'     => $value,
        ];

        return $this;
    }

    public function hide(): Rule
    {
        $this->rule['hide'] = true;

        return $this;
    }

    public function disable(): Rule
    {
        $this->rule['disable'] = true;

        return $this;
    }
}
