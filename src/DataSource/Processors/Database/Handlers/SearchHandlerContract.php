<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

interface SearchHandlerContract
{
    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder;
}
