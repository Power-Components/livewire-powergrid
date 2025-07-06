<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Support\{Collection as BaseCollection, Enumerable};
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
        if (!isset($this->setUp['detail']) && !isset($this->setUp['lazy'])) {
            return;
        }

        $collection = $results;

        if (!$results instanceof BaseCollection) {
            /** @phpstan-ignore-next-line */
            $collection = !is_array($results) && method_exists($results, 'items') ? collect($results->items()) : collect($results);
        }

        $primaryKey = $this->primaryKeyAlias ?? $this->primaryKey;

        $collection->pluck($primaryKey)
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

        if (filled(data_get($this->setUp, 'detail.collapseOthers'))) {
            /** @var Enumerable<(int|string), (int|string)> $except */
            $except = $id;
            collect($detailStates)->except($except)
                ->filter(fn ($state) => boolval($state))->keys()
                ->each(
                    fn ($key) => data_set($this->setUp, "detail.state.$key", false)
                );
        }

        data_set($this->setUp, "detail.state.$id", !boolval(data_get($this->setUp, "detail.state.$id")));

        $state = strval(data_get($this->setUp, "detail.state.$id"));

        /** @phpstan-ignore-next-line */
        if (method_exists($this, 'afterToggleDetail')) {
            $this->afterToggleDetail($id, $state);
        }
    }

    /**
     * @throws Exception
     */
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
