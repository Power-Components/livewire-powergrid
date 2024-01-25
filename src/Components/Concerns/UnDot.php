<?php

namespace PowerComponents\LivewirePowerGrid\Components\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Arr, Collection};

trait UnDot
{
    public function unDotActionsFromRow(Model|\stdClass|array $row, string $key): Collection
    {
        /** @phpstan-ignore-next-line */
        $unDottedRow = Arr::undot(collect($row)->toArray());

        $actions = (array) data_get($unDottedRow, $key, []);

        return collect($actions);
    }
}
