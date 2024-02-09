<?php

namespace PowerComponents\LivewirePowerGrid\Enums;

use Illuminate\Support\Collection;

enum DataSource
{
    case ELOQUENT_BUILDER;

    case QUERY_BUILDER;

    case COLLECTION;

    public static function from(string $datasource): mixed
    {
        return constant("self::{$datasource}");
    }

    public function label(): string
    {
        return match ($this) {
            DataSource::ELOQUENT_BUILDER => "Eloquent Builder",
            DataSource::QUERY_BUILDER    => "Query Builder",
            DataSource::COLLECTION       => "Collection"
        };
    }

    /**
     * Can use model in stubs
     */
    public function canHaveModel(): bool
    {
        return match ($this) {
            DataSource::ELOQUENT_BUILDER => true,
            default                      => false
        };
    }

    /**
     * Requires database table name in stubs
     */
    public function requiresDatabaseTableName(): bool
    {
        return match ($this) {
            DataSource::QUERY_BUILDER => true,
            default                   => false
        };
    }

    /**
     * Can read and source columns from fillable
     */
    public function canAutoImportFields(): bool
    {
        return match ($this) {
            DataSource::ELOQUENT_BUILDER => true,
            DataSource::QUERY_BUILDER    => true,
            default                      => false
        };
    }

    public function stubTemplate(): string
    {
        return match ($this) {
            DataSource::ELOQUENT_BUILDER => 'table.model.stub',
            DataSource::QUERY_BUILDER    => 'table.query-builder.stub',
            DataSource::COLLECTION       => 'table.collection.stub',
        };
    }

    /**
    * Datasource with labels for dropdown select
     *
     * @return Collection<string,string>
     */
    public static function asOptions(): Collection
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case): array => [strval($case->name) => $case->label()]);
    }
}
