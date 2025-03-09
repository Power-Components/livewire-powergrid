<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\{Column};
use stdClass;

class Export
{
    public string $fileName;

    public Collection $data;

    public string $striped = '';

    public array $columnWidth = [];

    /** @var array<Column> $columns */
    public array $columns;

    public function fileName(string $name): Export
    {
        $this->fileName = $name;

        return $this;
    }

    public function setData(array $columns, Collection $data): Export
    {
        $this->columns = $columns;
        $this->data    = collect($data);

        return $this;
    }

    public function prepare(Collection $data, array $columns, bool $stripTags): array
    {
        $header = collect();

        $data = $data->transform(function ($row) use ($columns, $header, $stripTags) {
            $item = collect();

            collect($columns)->each(function ($column) use ($row, $header, $item, $stripTags) {
                /** @var Model|stdClass $row */
                if (method_exists($row, 'withoutRelations')) {
                    $row = $row->withoutRelations()->toArray();
                }

                $isExportable = false;

                $hide = (bool) data_get(
                    collect((array) data_get($row, '__powergrid_rules'))
                        ->where('apply', true)
                        ->last(),
                    'disable',
                );

                $disable = (bool) data_get(
                    collect((array) data_get($row, '__powergrid_rules'))
                        ->where('apply', true)
                        ->last(),
                    'disable',
                );

                if ($hide || $disable) {
                    $isExportable = true;
                }

                /** @var Column $column */
                if ($column->visibleInExport || (!$column->hidden && is_null($column->visibleInExport)) && !$isExportable) {
                    /** @var array $row */
                    foreach ($row as $key => $value) {
                        if ($key === $column->field) {
                            if (true === $stripTags) {
                                $value = strip_tags($value);
                            }
                            $item->put($column->title, html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        }
                    }

                    if (!$header->contains($column->title)) {
                        $header->push($column->title);
                    }
                }
            });

            return $item->all();
        });

        return [
            'headers' => $header->all(),
            'rows'    => $data->all(),
        ];
    }
}
