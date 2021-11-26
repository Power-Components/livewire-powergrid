<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\Facades\{DB, Schema};
use Exception;

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
        $driverName = DB::getDriverName();

        $likeSyntax = [
            'pgsql' => 'ILIKE',
        ];

        return $likeSyntax[$driverName] ?? 'LIKE';
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
                '*' => "CAST(NULLIF(REGEXP_REPLACE($sortField, '\D', '', 'g'), '') AS INTEGER)",
            ],
            'sqlsrv' => [
                '*' => "CAST(SUBSTRING($sortField, PATINDEX('%[a-z]%', $sortField), LEN($sortField)-PATINDEX('%[a-z]%', $sortField)) AS INT)",
            ],
        ];

        return $sqlByDriver[$driverName][$driverVersion] ??  $sortField;
    }

    /**
     * @param string|null $sortFieldType
     * @return bool
     */
    public static function isValidSortFieldType(?string $sortFieldType): bool
    {
        if (is_null($sortFieldType)) {
            return false;
        }

        return in_array($sortFieldType, self::$sortStringNumberTypes);
    }

    /**
     * @param string $sortField
     * @return string
     * @throws Exception
     */
    public static function getSortFieldType(string $sortField): ?string
    {
        $data = explode('.', $sortField);
        if (!isset($data[1])) {
            return null;
        }

        if (!Schema::hasColumn($data[0], $data[1])) {
            throw new Exception("There is no column with name '$data[1]' on table '$data[0]'. Please see: https://livewire-powergrid.docsforge.com/main/include-columns/#fieldstring-field-string-datafield");
        }

        return Schema::getConnection()
            ->getDoctrineColumn($data[0], $data[1])
            ->getType()
            ->getName();
    }

    /**
     * @return string
     */
    public static function getDriverVersion(): string
    {
        return DB::getPdo()
            ->getAttribute(constant('PDO::ATTR_SERVER_VERSION'));
    }
}
