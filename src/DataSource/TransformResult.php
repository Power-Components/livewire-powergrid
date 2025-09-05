<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Support\Collection as BaseCollection;

final class TransformResult
{
    public function __construct(
        public readonly BaseCollection $collection,
        public readonly float $transformTimeInMs,
        public readonly array $actionsByRow = []
    ) {}

    public function getActionsByRow(): array
    {
        return $this->actionsByRow;
    }

    public function getCollection(): BaseCollection
    {
        return $this->collection;
    }

    public function getTransformTimeInMs(): float
    {
        return $this->transformTimeInMs;
    }
}
