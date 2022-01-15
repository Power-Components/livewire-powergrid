<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;

final class Rule
{
    public const TYPE_BUTTON = 'button';

    public const TYPE_ROW = 'row';

    public const TYPE_COLUMN = 'column';

    public array $rule = [];

    public string $forAction = '';

    public string $type = '';

    public ?string $column = '';

    public function __construct(string $action, string $type, string $column = null)
    {
        $this->forAction = $action;
        $this->type      = $type;
        $this->column    = $column;
    }

    public static function for(string $action): Rule
    {
        return new Rule($action, self::TYPE_BUTTON);
    }

    public static function rows(): Rule
    {
        return new Rule('pg:row', self::TYPE_ROW);
    }

    public static function column(string $column): Rule
    {
        return new Rule('pg:column', self::TYPE_COLUMN, $column);
    }

    public function when(Closure $closure = null): Rule
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    public function emit(string $event = '', array $params = []): Rule
    {
        $this->rule['redirect'] = [];
        $this->rule['emit']     = [
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

    public function redirect(string $route, string $target = '_blank'): Rule
    {
        $this->rule['emit']     = [];
        $this->rule['redirect'] = [
            'url'    => $route,
            'target' => $target,
        ];

        return $this;
    }

    public function caption(string $caption): Rule
    {
        $this->rule['caption'] = $caption;

        return $this;
    }
}
