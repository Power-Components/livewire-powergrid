<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Commands\Enums\{ColumnSource, Datasource};
use PowerComponents\LivewirePowerGrid\Commands\Support\{PowerGridComponentMaker, SchemaColumns};

/** Resolves which columns a component will be generated with, according to its configured source. */
final class ResolveGeneratedColumns
{
    /**
     * Column name => normalized type bucket, in the order they should be generated.
     *
     * @return Collection<string, string>
     */
    public static function handle(PowerGridComponentMaker $component): Collection
    {
        [$types, $fields] = $component->datasource === Datasource::QUERY_BUILDER
            ? self::fromDatabaseTable($component)
            : self::fromModel($component);

        return collect($fields)
            ->filter(fn (string $field): bool => $types->has($field))
            ->mapWithKeys(fn (string $field): array => [$field => strval($types->get($field))]);
    }

    /**
     * @return array{0: Collection<string, string>, 1: list<string>}
     */
    private static function fromDatabaseTable(PowerGridComponentMaker $component): array
    {
        $types = SchemaColumns::handle($component->databaseTable);

        return [$types, SchemaColumns::publicFields($types)];
    }

    /**
     * @return array{0: Collection<string, string>, 1: list<string>}
     */
    private static function fromModel(PowerGridComponentMaker $component): array
    {
        /** @var Model $model */
        $model = new $component->modelFqn();

        $types = SchemaColumns::handle($model->getTable(), $model->getConnectionName());

        $fields = $component->columnSource === ColumnSource::DATABASE_TABLE
            ? SchemaColumns::publicFields($types)
            : self::fromFillable($model);

        $hidden = $model->getHidden();

        $fields = array_values(array_filter(
            $fields,
            fn (string $field): bool => ! in_array($field, $hidden, true)
        ));

        return [$types, $fields];
    }

    /**
     * Primary key first, then $fillable, then created_at - the historical order.
     *
     * @return list<string>
     */
    private static function fromFillable(Model $model): array
    {
        $fields = $model->getFillable();

        if (filled($model->getKeyName())) {
            array_unshift($fields, $model->getKeyName());
        }

        $fields[] = 'created_at';

        return array_values(array_unique($fields));
    }
}
