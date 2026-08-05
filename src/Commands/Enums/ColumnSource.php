<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Enums;

use Illuminate\Support\Collection;

enum ColumnSource
{
    case FILLABLE;

    case DATABASE_TABLE;

    public static function from(string $columnSource): self
    {
        /** @var self $result */
        $result = constant("self::{$columnSource}");

        return $result;
    }

    public function label(): string
    {
        return match ($this) {
            ColumnSource::FILLABLE => 'Model $fillable property',
            ColumnSource::DATABASE_TABLE => 'Database table schema',
        };
    }

    /**
     * @return Collection<string,string>
     */
    public static function asOptions(): Collection
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case): array => [strval($case->name) => $case->label()]);
    }
}
