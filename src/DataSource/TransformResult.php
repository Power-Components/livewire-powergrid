<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Support\Collection as BaseCollection;

final class TransformResult
{
    public function __construct(
        public readonly BaseCollection $collection,
        public readonly float $transformTimeInMs,
    ) {
    }
}
