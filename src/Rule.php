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

    /**
     * Rules for a specific action button.
     */
    public static function for(string $action): Rule
    {
        return new Rule($action, self::TYPE_BUTTON);
    }

    /**
     * Rules to be applied on rows matching the condition.
     */
    public static function rows(): Rule
    {
        return new Rule('pg:row', self::TYPE_ROW);
    }

    /**
     * Disables the button.
     */
    public function when(Closure $closure = null): Rule
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    /**
     * Sets the button's event to be emitted.
     */
    public function emit(string $event = '', array $params = []): Rule
    {
        $this->rule['redirect'] = [];
        $this->rule['emit']     = [
            'event'  => $event,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(string $attribute = null, string $value = null): Rule
    {
        $this->rule['setAttribute'] = [
            'attribute' => $attribute,
            'value'     => $value,
        ];

        return $this;
    }

    /**
     * Hides the button.
     */
    public function hide(): Rule
    {
        $this->rule['hide'] = true;

        return $this;
    }

    /**
     * Disables the button.
     */
    public function disable(): Rule
    {
        $this->rule['disable'] = true;

        return $this;
    }

    /**
     * Sets button's redirect URL.
     */
    public function redirect(Closure $closure = null, string $target = '_blank'): Rule
    {
        $this->rule['emit']     = [];
        $this->rule['redirect'] = [
            'closure' => $closure,
            'target'  => $target,
        ];

        return $this;
    }

    /**
     * Sets the button caption value.
     */
    public function caption(string $caption): Rule
    {
        $this->rule['caption'] = $caption;

        return $this;
    }
}
