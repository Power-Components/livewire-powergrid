<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Commands\Enums\ColumnSource;
use PowerComponents\LivewirePowerGrid\Commands\Support\{PowerGridComponentMaker, StubColumnBuilder};

/** @codeCoverageIgnore */
class GetStubVarsFromFromModel
{
    /**
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(PowerGridComponentMaker $component): array
    {
        /** @var Model $model */
        $model = new $component->modelFqn();

        $connection = Schema::connection($model->getConnection()->getName());

        $table = $model->getTable();

        $fields = $component->columnSource === ColumnSource::DATABASE_TABLE
            ? self::fieldsFromTable($connection, $table)
            : self::fieldsFromFillable($model);

        $fieldTypes = [];

        foreach ($fields as $field) {
            if (in_array($field, $model->getHidden())) {
                continue;
            }

            if (in_array($field, StubColumnBuilder::SENSITIVE_FIELDS)) {
                continue;
            }

            if (self::hasColumn($connection, $table, $field)) {
                $fieldTypes[$field] = $connection->getColumnType($table, $field);
            }
        }

        return (new StubColumnBuilder())->build($fieldTypes, $component->model);
    }

    /**
     * @return array<int, string>
     */
    private static function fieldsFromFillable(Model $model): array
    {
        $fields = $model->getFillable();

        if (filled($model->getKeyName())) {
            $fields = array_merge([$model->getKeyName()], $fields);
        }

        return array_merge($fields, ['created_at']);
    }

    /**
     * @return array<int, string>
     */
    private static function fieldsFromTable(Builder $connection, string $table): array
    {
        return $connection->getColumnListing($table);
    }

    private static function hasColumn(Builder $connection, string $table, string $field): bool
    {
        try {
            return $connection->hasColumn($table, $field);
        } catch (\Exception) {
            return Schema::hasColumn($table, $field);
        }
    }
}
