<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use Illuminate\Support\Str;
use PowerComponents\Turbine\Contracts\Definition;

/**
 * SetUp builder for the status-tabs strip rendered above the toolbar.
 *
 * Usage inside a component's setUp():
 *
 *   PowerGrid::tabs()
 *       ->add('all',  label: 'All')                              // no scope = unfiltered
 *       ->add('new',  label: 'New', scope: ['status', 'new'])    // status = new
 *       ->add('done', label: 'Done', scope: ['status', 'in', ['shipped', 'delivered']])
 *       ->default('new');
 */
final class Tabs implements Definition
{
    public string $name = 'tabs';

    /** @var array<string, Tab> */
    public array $tabs = [];

    public ?string $default = null;

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param  array<int, mixed>|null  $scope  [column, value] or [column, operator, value]
     */
    public function add(
        string $key,
        string $label = '',
        ?array $scope = null,
        bool|int $badge = true,
        string $icon = '',
    ): self {
        $tab = new Tab($key);
        $tab->label = $label !== '' ? $label : Str::headline($key);
        $tab->scope = $scope;
        $tab->badge = $badge;
        $tab->icon = $icon;

        $this->tabs[$key] = $tab;

        return $this;
    }

    /** Tab selected on first load. */
    public function default(string $key): self
    {
        $this->default = $key;

        return $this;
    }
}
