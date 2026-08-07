<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Database\Eloquent\Model;
use PowerComponents\LivewirePowerGrid\Commands\Enums\ColumnSource;
use PowerComponents\LivewirePowerGrid\Commands\Support\{PowerGridComponentMaker, SchemaColumns};

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

        $types = SchemaColumns::handle($model->getTable(), $model->getConnectionName());

        $fields = $component->columnSource === ColumnSource::DATABASE_TABLE
            ? SchemaColumns::publicFields($types)
            : self::fromFillable($model);

        $hidden = $model->getHidden();

        $fields = array_values(array_filter(
            $fields,
            fn (string $field): bool => ! in_array($field, $hidden, true)
        ));

        return BuildStubVars::handle($fields, $types, $component->model);
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
