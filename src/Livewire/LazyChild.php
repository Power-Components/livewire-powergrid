<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\{Collection, Collection as BaseCollection};
use Livewire\Component;

class LazyChild extends Component
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public string $checkboxAttribute = 'id';

    public bool $radio = false;

    public string $radioAttribute = 'id';

    public mixed $data;

    public array $theme;

    public array $setUp;

    public Collection $columns;

    public string $tableName;

    public string|int $primaryKey;

    public function mount(): void
    {
        $this->resolveDetailRow($this->data);
    }

    private function resolveDetailRow(mixed $results): void
    {
        if (!isset($this->setUp['detail'])) {
            return;
        }

        $collection = $results;

        if (!$results instanceof BaseCollection) {
            /** @phpstan-ignore-next-line */
            $collection = collect($results->items());
        }

        /** @phpstan-ignore-next-line */
        $collection->each(function ($model) {
            $id = strval($model->{$this->primaryKey});

            data_set($this->setUp, 'detail', (array) $this->setUp['detail']);

            $state = data_get($this->setUp, 'detail.state.' . $id, false);

            data_set($this->setUp, 'detail.state.' . $id, $state);
        });
    }

    public function toggleDetail(string $id): void
    {
        $detailStates = (array) data_get($this->setUp, 'detail.state');

        if (boolval(data_get($this->setUp, 'detail.collapseOthers'))) {
            /** @var \Illuminate\Support\Enumerable<(int|string), (int|string)> $except */
            $except = $id;
            collect($detailStates)->except($except)
                ->filter(fn ($state) => $state)->keys()
                ->each(
                    fn ($key) => data_set($this->setUp, "detail.state.$key", false)
                );
        }

        data_set($this->setUp, "detail.state.$id", !boolval(data_get($this->setUp, "detail.state.$id")));
    }

    public function render(): View
    {
        return view('livewire-powergrid::livewire.lazy-child');
    }
}
