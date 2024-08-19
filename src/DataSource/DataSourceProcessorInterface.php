<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

interface DataSourceProcessorInterface
{
    public static function match(mixed $key): bool;

    public function process(): mixed;
}
