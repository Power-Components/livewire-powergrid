<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export;

use Generator;
use PowerComponents\LivewirePowerGrid\Column;

class Export
{
    public string $fileName;

    /** @var iterable<int, mixed> */
    public iterable $data;

    public string $striped = '';

    /** @var array<string, mixed> */
    public array $columnWidth = [];

    /** @var array<Column> */
    public array $columns;

    public function fileName(string $name): Export
    {
        $this->fileName = $name;

        return $this;
    }

    /**
     * @param  array<int, Column>  $columns
     * @param  iterable<int, mixed>  $data
     */
    public function setData(array $columns, iterable $data): Export
    {
        $this->columns = $columns;

        // Keep the source iterable lazy (e.g. a cursor-backed LazyCollection) so
        // large exports are never fully materialized in memory.
        $this->data = $data;

        return $this;
    }

    /**
     * Titles of the columns that make it into the export file, in column order.
     *
     * @param  array<int, Column>  $columns
     * @return list<string>
     */
    public function exportHeaders(array $columns): array
    {
        $headers = [];

        foreach ($columns as $column) {
            if (! $this->columnIsExportable($column)) {
                continue;
            }

            $title = data_get($column, 'title');
            $headers[] = is_string($title) ? $title : '';
        }

        return $headers;
    }

    /**
     * Stream one export row (values aligned to exportHeaders()) at a time, so the
     * caller/writer never holds the whole dataset in memory.
     *
     * @param  iterable<int, mixed>  $data
     * @param  array<int, Column>  $columns
     * @return Generator<int, list<mixed>>
     */
    public function streamRows(iterable $data, array $columns, bool $stripTags): Generator
    {
        $exportableColumns = collect($columns)
            ->filter(fn ($column): bool => $this->columnIsExportable($column))
            ->values();

        foreach ($data as $row) {
            if (is_object($row) && method_exists($row, 'withoutRelations')) {
                $row = $row->withoutRelations()->toArray();
            }

            $row = (array) $row;

            $values = [];

            foreach ($exportableColumns as $column) {
                /** @var string $field */
                $field = data_get($column, 'field');

                $value = data_get($row, $field, '');
                $value = is_scalar($value) ? (string) $value : '';

                if ($stripTags) {
                    $value = strip_tags($value);
                }

                $values[] = $this->neutralizeFormula(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            yield $values;
        }
    }

    /**
     * Neutralize spreadsheet formula-injection payloads (CWE-1236): prefix a
     * leading '='/'+'/'-'/'@'/tab/CR with a single quote so spreadsheet apps
     * treat the cell as text instead of executing it as a formula.
     */
    private function neutralizeFormula(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        $first = $value[0];

        if (in_array($first, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }

    private function columnIsExportable(mixed $column): bool
    {
        return (bool) data_get($column, 'visibleInExport')
            || (! data_get($column, 'hidden') && is_null(data_get($column, 'visibleInExport')));
    }

    /**
     * Backward-compatible eager helper (materializes all rows). Prefer
     * exportHeaders() + streamRows() for large datasets.
     *
     * @param  iterable<int, mixed>  $data
     * @param  array<int, Column>  $columns
     * @return array{headers: list<string>, rows: list<list<mixed>>}
     */
    public function prepare(iterable $data, array $columns, bool $stripTags): array
    {
        return [
            'headers' => $this->exportHeaders($columns),
            'rows' => iterator_to_array($this->streamRows($data, $columns, $stripTags), false),
        ];
    }
}
