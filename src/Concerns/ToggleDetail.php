<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Support\Collection as BaseCollection;
use Livewire\Attributes\On;

trait ToggleDetail
{
    /**
     * @throws Exception
     */
    public function mountToggleDetail(): void
    {
        $this->throwFeatureDetail();
    }

    private function resolveDetailRow(mixed $results): void
    {
        if (!isset($this->setUp['detail'])) {
            return;
        }

        $collection = $results;

        if (!$results instanceof BaseCollection) {
            /** @phpstan-ignore-next-line */
            $collection = !is_array($results) && method_exists($results, 'items') ? collect($results->items()) : collect($results);
        }

        $collection->pluck($this->primaryKey)
            ->map(fn ($id) => strval($id))
            ->each(function ($id) {
                $state = data_get($this->setUp, "detail.state.$id", false);
                data_set($this->setUp, "detail.state.$id", $state);
            });
    }

    #[On('pg:toggleDetail-{tableName}')]
    public function toggleDetail(string $id): void
    {
        $detailStates = (array) data_get($this->setUp, 'detail.state');

        if (boolval(data_get($this->setUp, 'detail.collapseOthers'))) {
            /** @var \Illuminate\Support\Enumerable<(int|string), (int|string)> $except */
            $except = $id;
            collect($detailStates)->except($except)
                ->filter(fn ($state) => boolval($state))->keys()
                ->each(
                    fn ($key) => data_set($this->setUp, "detail.state.$key", false)
                );
        }

        data_set($this->setUp, "detail.state.$id", !boolval(data_get($this->setUp, "detail.state.$id")));

        $state = strval(data_get($this->setUp, "detail.state.$id"));

        if (method_exists($this, 'afterToggleDetail')) {
            $this->afterToggleDetail($id, $state);
        }
    }

    private function throwFeatureDetail(): void
    {
        if (
            array_key_exists('detail', $this->setUp)
            && array_key_exists('responsive', $this->setUp)
        ) {
            throw new Exception('The Feature Responsive cannot be used with Detail');
        }
    }
}
