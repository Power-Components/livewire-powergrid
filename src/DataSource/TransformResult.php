<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Support\Collection as BaseCollection;

final class TransformResult
{
    /**
     * @param  BaseCollection<int, mixed>  $collection
     * @param  array<int|string, list<array<string, mixed>>>  $actionsByRow
     */
    public function __construct(
        public readonly BaseCollection $collection,
        public readonly float $transformTimeInMs,
        public readonly array $actionsByRow = []
    ) {}

    /** @return array<int|string, list<array<string, mixed>>> */
    public function getActionsByRow(): array
    {
        return $this->actionsByRow;
    }

    /** @return BaseCollection<int, mixed> */
    public function getCollection(): BaseCollection
    {
        return $this->collection;
    }

    public function getTransformTimeInMs(): float
    {
        return $this->transformTimeInMs;
    }
}
