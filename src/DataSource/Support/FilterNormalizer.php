<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Support;

use Illuminate\Support\Arr;

class FilterNormalizer
{
    /**
     * Flatten and re-group a filter type's columns so that every DataSource
     * (database, collection, …) sees the same field => value shape:
     *  - relation paths stay intact (e.g. 'user.roles')
     *  - multi_select arrays are rebuilt as [0 => v, 1 => v, …]
     *  - number/date ranges are rebuilt as ['start' => v, 'end' => v]
     *
     * @param  array<array-key, mixed>  $columns
     * @return array<string, mixed>
     */
    public static function normalize(array $columns): array
    {
        $normalized = [];

        foreach (Arr::dot($columns) as $key => $value) {
            $parts = explode('.', strval($key));
            $lastPart = end($parts);

            if (is_numeric($lastPart) && intval($lastPart) == $lastPart) {
                array_pop($parts);
                $normalized[implode('.', $parts)][intval($lastPart)] = $value;
            } elseif ($lastPart === 'start' || $lastPart === 'end') {
                $normalized[implode('.', array_slice($parts, 0, -1))][$lastPart] = $value;
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
