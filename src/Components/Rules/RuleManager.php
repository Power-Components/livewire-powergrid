<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

class RuleManager
{
    public const TYPE_ACTIONS = 'actions';

    public const TYPE_ROWS = 'pg:rows';

    public const TYPE_CHECKBOX = 'pg:checkbox';

    public const TYPE_RADIO = 'pg:radio';

    public const TYPE_COLUMN = 'pg:column';

    public static function applicableModifiers(): array
    {
        return ['bladeComponent', 'detailView', 'disable', 'dispatch', 'dispatchTo', 'emit', 'hide', 'loop', 'redirect', 'rowClasses', 'setAttribute', 'slot', 'ToggleableVisibility', 'DetailButtonVisibility'];
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
