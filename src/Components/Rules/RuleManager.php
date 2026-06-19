<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Illuminate\Support\Traits\Macroable;

class RuleManager
{
    use Macroable;

    public const TYPE_ACTIONS = 'actions';

    public const TYPE_ROWS = 'pg:rows';

    public const TYPE_CHECKBOX = 'pg:checkbox';

    public const TYPE_RADIO = 'pg:radio';

    public const TYPE_COLUMN = 'pg:column';

    /**
     * Rule modifiers registered by plugins.
     */
    /** @var list<string> */
    protected static array $pluginModifiers = [];

    /**
     * Register rule modifiers from a plugin.
     */
    /** @param  list<string>  $modifiers */
    public static function registerModifiers(array $modifiers): void
    {
        static::$pluginModifiers = array_merge(static::$pluginModifiers, $modifiers);
    }

    /** @return array<int, string> */
    public static function applicableModifiers(): array
    {
        return array_merge(
            ['bladeComponent', 'detailView', 'disable', 'dispatch', 'dispatchTo', 'emit', 'hide', 'loop', 'redirect', 'rowClasses', 'setAttribute', 'slot', 'toggleDetailVisibility'],
            static::$pluginModifiers
        );
    }

    public function button(string $button): RuleActions
    {
        return new RuleActions($button);
    }

    public function rows(): RuleRows
    {
        return new RuleRows();
    }

    public function checkbox(): RuleCheckbox
    {
        return new RuleCheckbox();
    }

    public function radio(): RuleRadio
    {
        return new RuleRadio();
    }
}
