<?php

namespace PowerComponents\LivewirePowerGrid\Support;

final class CompatAliases
{
    private const OLD_NAMESPACE = 'PowerComponents\\LivewirePowerGrid\\';

    private const NEW_NAMESPACE = 'PowerComponents\\Turbine\\';

    /**
     * Classes moved out of livewire-powergrid into the turbine engine. Paths are
     * shared by 6.x and 7.x, so a single alias per class keeps legacy
     * `use PowerComponents\LivewirePowerGrid\...` imports, type-hints and
     * instanceof checks working against the new PowerComponents\Turbine\* classes.
     *
     * @var list<string>
     */
    private const MOVED = [
        'Components\\Filters\\FilterBase',
        'Components\\Filters\\FilterInputText',
        'Components\\Filters\\FilterSelect',
        'Components\\Filters\\FilterEnumSelect',
        'Components\\Filters\\FilterMultiSelect',
        'Components\\Filters\\FilterMultiSelectAsync',
        'Components\\Filters\\FilterBoolean',
        'Components\\Filters\\FilterNumber',
        'Components\\Filters\\FilterDynamic',
        'Components\\Filters\\FilterDatePicker',
        'Components\\Filters\\FilterDateTimePicker',
        'Components\\Filters\\FilterManager',
        'Components\\Rules\\BaseRule',
        'Components\\Rules\\RuleManager',
        'Components\\Rules\\RuleActions',
        'Components\\Rules\\RuleRows',
        'Components\\Rules\\RuleCheckbox',
        'Components\\Rules\\RuleRadio',
        'Components\\Rules\\RuleToggleable',
        'Components\\Rules\\RuleEditOnClick',
        'Components\\SetUp\\Cache',
        'Components\\SetUp\\Responsive',
        'Components\\SetUp\\FilterBuilder',
    ];

    /**
     * Register a lazy autoloader that aliases a legacy FQCN to its turbine
     * counterpart the first time it is referenced. Zero cost for apps already on
     * the new namespace: the callback only fires for an unresolved legacy class.
     */
    public static function register(): void
    {
        spl_autoload_register(static function (string $class): void {
            if (! str_starts_with($class, self::OLD_NAMESPACE)) {
                return;
            }

            $relative = substr($class, strlen(self::OLD_NAMESPACE));

            if (! in_array($relative, self::MOVED, true)) {
                return;
            }

            class_alias(self::NEW_NAMESPACE.$relative, $class);
        });
    }
}
