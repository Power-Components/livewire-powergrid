<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/** Fixture whose $fillable already repeats the primary key and created_at. */
class WaiterWithKeyInFillable extends Model
{
    protected $table = 'waiters';

    protected $fillable = [
        'id',
        'name',
        'created_at',
    ];
}
