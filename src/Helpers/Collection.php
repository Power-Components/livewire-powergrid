<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;

class Collection
{

    public static function paginate(BaseCollection $results, $pageSize): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage('page');

        $total = $results->count();

        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param Collection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected static function paginator($items, $total, $perPage, $currentPage, $options): LengthAwarePaginator
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    public static function search(BaseCollection $model, string $search, $columns): BaseCollection
    {
        $data_map = collect([]);

        if (!empty($search)) {
            foreach ($model as $item) {
                foreach ($columns as $key => $value) {
                    $field = $value->field;
                    if (isset($value->searchable)) {
                        if (Str::contains(strtolower($item->$field), strtolower($search))) {
                            if (!in_array(strtolower($item->$field), $data_map->toArray())) {
                                $data_map->push($item);
                            }
                        }
                    }
                }
            }
            $data_map =  array_unique($data_map->toArray(), SORT_REGULAR);

        } else {
            $data_map = $model;
        }

        return collect($data_map);
    }

}
