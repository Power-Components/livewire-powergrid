<?php

namespace PowerComponents\LivewirePowerGrid\Contracts;

interface SchemaInspector
{
    /** @return array<string, string> */
    public function columnTypes(string $table, ?string $connection = null): array;

    /** @return list<string> */
    public function columnListing(string $table, ?string $connection = null): array;
}
