<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use PowerComponents\LivewirePowerGrid\Column;
use stdClass;

/** @codeCoverageIgnore */
class LazyChild extends Component
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public string $checkboxAttribute = '';

    public bool $radio = false;

    public string $radioAttribute = '';

    public Collection $data;

    public array $theme;

    public array $setUp;

    public Collection $columns;

    public string $tableName;

    public string $primaryKey = '';

    public string|int $realPrimaryKey = '';

    public string $parentName;

    public string|int $childIndex;

    public ?string $parentId = null;

    public function actionsFromView(mixed $row): ?View
    {
        $parentComponent = $this->getComponentAlias();

        if (method_exists($parentComponent, 'actionsFromView')) {
            return app($parentComponent)->actionsFromView($row);
        }

        return null;
    }

    public function shouldShowEditOnClick(array|Column|stdClass $column, mixed $row): bool
    {
        $parentComponent = $this->getComponentAlias();

        return app($parentComponent)->shouldShowEditOnClick($column, $row);
    }

    public function shouldShowToggleable(array|Column|stdClass $column, mixed $row): bool
    {
        $parentComponent = $this->getComponentAlias();

        return app($parentComponent)->shouldShowToggleable($column, $row);
    }

    private function getComponentAlias(): ?string
    {
        if (class_exists(\Livewire\Mechanisms\ComponentRegistry::class)) {

            return app(ComponentRegistry::class)->getClass($this->parentName);
        }

        return app('livewire.finder')->resolveClassComponentClassName($this->parentName);
    }

    public function render(): View
    {
        return view('livewire-powergrid::livewire.lazy-child');
    }
}
