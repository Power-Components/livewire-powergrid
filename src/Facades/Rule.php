<?php

namespace PowerComponents\LivewirePowerGrid\Facades;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Components\Rules\{RuleActions, RuleCheckbox, RuleEditOnClick, RuleRadio, RuleRows, RuleToggleable};

/**
 * @method static RuleActions button(string $button)
 * @method static RuleToggleable toggleable(string $column)
 * @method static RuleEditOnClick editOnClick(string $column)
 * @method static RuleRows rows()
 * @method static RuleCheckbox checkbox()
 * @method static RuleRadio radio()
 *
 * @see RuleManager
 */
class Rule extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'rule';
    }
}
