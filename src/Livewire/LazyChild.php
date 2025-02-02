<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Concerns\ToggleDetail;
use stdClass;

class LazyChild extends Component
{
    use ToggleDetail;

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

    public function mount(): void
    {
        $this->resolveDetailRow($this->data);
    }

    public function afterToggleDetail(string $id, string $state): void
    {
        $parentComponent = app(ComponentRegistry::class)->getClass($this->parentName);

        $dispatchAfterToggleDetail = (array) data_get($this->setUp, 'lazy.dispatchAfterToggleDetail');

        $this->dispatch($dispatchAfterToggleDetail, id: $id, state: $state ? 'true' : 'false')->to($parentComponent);
    }

    public function actionsFromView(mixed $row): ?View
    {
        /** @var string $parentComponent */
        $parentComponent = app(ComponentRegistry::class)->getClass($this->parentName);

        if (method_exists($parentComponent, 'actionsFromView')) {
            return app($parentComponent)->actionsFromView($row);
        }

        return null;
    }

    public function shouldShowEditOnClick(array|Column|stdClass $column, mixed $row): bool
    {
        /** @var string $parentComponent */
        $parentComponent = app(ComponentRegistry::class)->getClass($this->parentName);

        return app($parentComponent)->shouldShowEditOnClick($column, $row);
    }

    public function shouldShowToggleable(array|Column|stdClass $column, mixed $row): bool
    {
        /** @var string $parentComponent */
        $parentComponent = app(ComponentRegistry::class)->getClass($this->parentName);

        return app($parentComponent)->shouldShowToggleable($column, $row);
    }

    public function render(): View
    {
        return view('livewire-powergrid::livewire.lazy-child');
    }
}
