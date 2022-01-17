<?php

namespace PowerComponents\LivewirePowerGrid\Rules;

class RuleManager
{
    public const TYPE_ACTIONS = 'actions';

    public const TYPE_ROWS = 'pg:rows';

    public const TYPE_CHECKBOX = 'pg:checkbox';

    public const TYPE_COLUMN = 'pg:column';

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
}
