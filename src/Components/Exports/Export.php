<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\Rules\{RuleManager, RulesController};

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

    /**
     * @param array<Column> $columns
     * @param Collection $data
     * @return Export
     */
    public function setData(array $columns, Collection $data): Export
    {
        $this->columns = $columns;
        $this->data    = collect($data);

        return $this;
    }

    /**
     * @param Collection $data
     * @param array<Column> $columns
     * @return array{headers: array, rows: array}.
     */
    public function prepare(Collection $data, array $columns): array
    {
        $header = collect([]);

        $actionRulesClass = resolve(RulesController::class);

        $data = $data->transform(function ($row) use ($columns, $header, $actionRulesClass) {
            $item = collect([]);

            collect($columns)->each(function ($column) use ($row, $header, $item, $actionRulesClass) {
                /** @var Model|\stdClass $row */
                if (method_exists($row, 'withoutRelations')) {
                    $row = $row->withoutRelations()->toArray();
                }

                $rules        = $actionRulesClass->recoverFromAction($row, RuleManager::TYPE_ROWS);
                $isExportable = false;

                if (filled($rules['hide']) || filled($rules['disable'])) {
                    $isExportable = true;
                }

                /** @var Column $column */
                if ($column->visibleInExport || (!$column->hidden && is_null($column->visibleInExport)) && !$isExportable) {
                    /** @var array $row */
                    foreach ($row as $key => $value) {
                        if ($key === $column->field) {
                            $item->put($column->title, html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        }
                    }

                    if (!$header->contains($column->title)) {
                        $header->push($column->title);
                    }
                }
            });

            return $item->toArray();
        });

        return [
            'headers' => $header->toArray(),
            'rows'    => $data->toArray(),
        ];
    }
}
