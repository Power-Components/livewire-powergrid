<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use PowerComponents\LivewirePowerGrid\Traits\ToggleDetail;

class LazyChild extends Component
{
    use ToggleDetail;

    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public string $checkboxAttribute = 'id';

    public bool $radio = false;

    public string $radioAttribute = 'id';

    private Collection $data;

    public array $theme;

    public array $setUp;

    public Collection $columns;

    public string $tableName;

    public string|int $primaryKey;

    public string $parentName;

    public string|int $childIndex;

    public function mount(Collection $data): void
    {
        $this->data = $data;

        $this->resolveDetailRow($this->data);
    }

    public function afterToggleDetail(string $id, string $state): void
    {
        $parentComponent = app(ComponentRegistry::class)->getClass($this->parentName);

        $dispatchAfterToggleDetail = (array) data_get($this->setUp, 'lazy.dispatchAfterToggleDetail');

        $this->dispatch($dispatchAfterToggleDetail, id: $id, state: $state ? 'true' : 'false')->to($parentComponent);
    }

    public function render(): View
    {
        $data = $this->data;

        return view('livewire-powergrid::livewire.lazy-child', compact('data'));
    }
}
