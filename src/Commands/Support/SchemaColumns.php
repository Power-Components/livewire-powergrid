<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Support;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Reads a table's schema in a single query and normalizes native database
 * type names into the generic buckets the component generator understands.
 */
final class SchemaColumns
{
    /** Columns never generated when the source is the database table. */
    public const SENSITIVE_COLUMNS = ['password', 'remember_token', 'email_verified_at', 'two_factor_secret', 'two_factor_recovery_codes', 'api_token'];

    private const DATETIME_TYPES = ['datetime', 'datetime2', 'timestamp', 'timestamptz', 'datetimeoffset'];

    private const BOOLEAN_TYPES = ['bool', 'boolean', 'bit'];

    private const INTEGER_TYPES = ['int', 'integer', 'int2', 'int4', 'int8', 'smallint', 'mediumint', 'bigint', 'tinyint', 'serial', 'bigserial', 'smallserial'];

    // `bpchar` is how PostgreSQL reports a blank-padded `char(n)`.
    private const STRING_TYPES = ['varchar', 'char', 'bpchar', 'nvarchar', 'nchar', 'text', 'tinytext', 'mediumtext', 'longtext', 'ntext', 'citext', 'uuid', 'enum', 'string'];

    /**
     * Column name => normalized type bucket, in table column order.
     *
     * @return Collection<string, string>
     */
    public static function handle(string $table, ?string $connection = null): Collection
    {
        try {
            $columns = Schema::connection($connection)->getColumns($table);
        } catch (Exception) {
            // Component may be created before the database exists or is migrated.
            return collect();
        }

        return collect($columns)->mapWithKeys(fn (array $column): array => [
            strval($column['name']) => self::normalize(
                strtolower(strval($column['type_name'])),
                strtolower(strval($column['type'])),
            ),
        ]);
    }

    /**
     * Column names with the sensitive ones removed, in table column order.
     *
     * @param  Collection<string, string>  $types
     * @return list<string>
     */
    public static function publicFields(Collection $types): array
    {
        return array_values($types->keys()->reject(
            fn (string $field): bool => in_array($field, self::SENSITIVE_COLUMNS, true)
        )->all());
    }

    private static function normalize(string $typeName, string $type): string
    {
        if ($typeName === 'date') {
            return 'date';
        }

        if (in_array($typeName, self::DATETIME_TYPES, true)) {
            return 'datetime';
        }

        // MySQL and SQLite both report a boolean as `tinyint`; only the
        // display width in the full type tells it apart from an integer.
        // Match `tinyint(1)` exactly (or with a trailing modifier such as
        // ` unsigned`) so `tinyint(10)`..`tinyint(19)` aren't mistaken for it.
        if (in_array($typeName, self::BOOLEAN_TYPES, true) || $type === 'tinyint(1)' || str_starts_with($type, 'tinyint(1) ')) {
            return 'boolean';
        }

        if (in_array($typeName, self::INTEGER_TYPES, true)) {
            return 'integer';
        }

        if (in_array($typeName, self::STRING_TYPES, true)) {
            return 'string';
        }

        return 'other';
    }
}
