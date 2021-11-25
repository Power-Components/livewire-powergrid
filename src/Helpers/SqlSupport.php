<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\Facades\{DB, Schema};

class SqlSupport
{
    /**
     * @var array|string[]
     */
    private static array $sortStringNumberTypes = ['string', 'varchar', 'char'];

    /**
     * @return string
     */
    public static function like(): string
    {
        if (DB::getDriverName() == 'pgsql') {
            return 'ILIKE';
        }

        return 'LIKE';
    }

    /**
     * @param string $sortField
     * @return string
     */
    public static function sortStringAsNumber(string $sortField): string
    {
        $driverName = DB::getDriverName();

        return self::getSortSqlByDriver($sortField, $driverName);
    }

    /**
     * @param string $sortField
     * @param string $driverName
     * @param string $driverVersion
     * @return string
     */
    private static function getSortSqlByDriver(string $sortField, string $driverName, string $driverVersion = '*'): string
    {
        $sqlByDriver = [
            'sqlite' => [
                '*' => "CAST($sortField AS INTEGER)",
            ],
            'mysql' => [
                '*' => "CAST(NULLIF(REGEXP_REPLACE($sortField, '[[:alpha:]]+', ''), '') AS SIGNED INTEGER)",
            ],
            'pgsql' => [
                '*' => "CAST(NULLIF(REGEXP_REPLACE($sortField, '\D', '', 'g'), '') AS BANANA)",
            ],
            'sqlsrv' => [
                '*' => "CAST(SUBSTRING($sortField, PATINDEX('%[a-z]%', $sortField), LEN($sortField)-PATINDEX('%[a-z]%', $sortField)) AS INT)",
            ],
        ];

        return $sqlByDriver[$driverName][$driverVersion] ??  $sortField;
    }

    /**
     * @param string $sortFieldType
     * @return bool
     */
    public static function isValidSortFieldType(string $sortFieldType): bool
    {
        return in_array($sortFieldType, self::$sortStringNumberTypes);
    }

    /**
     * @param string $sortField
     * @return string
     */
    public static function getSortFieldType(string $sortField): string
    {
        $data = explode('.', $sortField);

        return Schema::getConnection()
            ->getDoctrineColumn($data[0], $data[1])
            ->getType()
            ->getName();
    }
}
