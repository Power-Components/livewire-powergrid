<?php

namespace PowerComponents\LivewirePowerGrid\Support\State;

use Illuminate\Http\Request;

final class PowerGridState
{
    /**
     * @param  array<string, string>  $sortArray
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $filterBuilder
     * @param  array<string, mixed>  $setUp
     * @param  list<mixed>  $columns
     */
    public function __construct(
        public readonly string $search = '',
        public readonly string $sortField = 'id',
        public readonly string $sortDirection = 'asc',
        public readonly bool $multiSort = false,
        public readonly array $sortArray = [],
        public readonly array $filters = [],
        public readonly array $filterBuilder = [],
        public readonly string $softDeletes = '',
        public readonly array $setUp = [],
        public readonly array $columns = [],
        public readonly string $primaryKey = 'id',
        public readonly ?string $primaryKeyAlias = null,
        public readonly bool $ignoreTablePrefix = true,
        public readonly bool $pruneHiddenColumns = true,
        public readonly bool $paginateRaw = false,
        public readonly bool $isExporting = false,
        public readonly string $tableName = '',
        public readonly bool $supportModel = true,
    ) {}

    public function realPrimaryKey(): string
    {
        return $this->primaryKeyAlias ?? $this->primaryKey;
    }

    /** @param  array<string, mixed>  $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            search: self::asString($payload['search'] ?? ''),
            sortField: self::asString($payload['sortField'] ?? 'id', 'id'),
            sortDirection: self::asString($payload['sortDirection'] ?? 'asc', 'asc'),
            multiSort: (bool) ($payload['multiSort'] ?? false),
            sortArray: self::asStringMap($payload['sortArray'] ?? []),
            filters: self::asArray($payload['filters'] ?? []),
            filterBuilder: self::asArray($payload['filterBuilder'] ?? []),
            softDeletes: self::asString($payload['softDeletes'] ?? ''),
            setUp: self::asArray($payload['setUp'] ?? []),
            columns: array_values(self::asArray($payload['columns'] ?? [])),
            primaryKey: self::asString($payload['primaryKey'] ?? 'id', 'id'),
            primaryKeyAlias: isset($payload['primaryKeyAlias']) ? self::asString($payload['primaryKeyAlias']) : null,
            ignoreTablePrefix: (bool) ($payload['ignoreTablePrefix'] ?? true),
            pruneHiddenColumns: (bool) ($payload['pruneHiddenColumns'] ?? true),
            paginateRaw: (bool) ($payload['paginateRaw'] ?? false),
            isExporting: (bool) ($payload['isExporting'] ?? false),
            tableName: self::asString($payload['tableName'] ?? ''),
            supportModel: (bool) ($payload['supportModel'] ?? true),
        );
    }

    private static function asString(mixed $value, string $default = ''): string
    {
        return is_scalar($value) ? (string) $value : $default;
    }

    /** @return array<string, mixed> */
    private static function asArray(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];

        /** @var mixed $item */
        foreach ($value as $key => $item) {
            $out[(string) $key] = $item;
        }

        return $out;
    }

    /** @return array<string, string> */
    private static function asStringMap(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];

        /** @var mixed $item */
        foreach ($value as $key => $item) {
            $out[(string) $key] = is_scalar($item) ? (string) $item : '';
        }

        return $out;
    }

    public static function fromRequest(Request $request, string $key = 'powergrid'): self
    {
        /** @var array<string, mixed> $payload */
        $payload = (array) $request->input($key, []);

        return self::fromArray($payload);
    }
}
