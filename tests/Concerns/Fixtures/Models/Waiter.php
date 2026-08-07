<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Fixture for the column generator tests.
 *
 * Lives outside Concerns\Models on purpose: ListModelsTest asserts the exact
 * contents of that directory.
 */
class Waiter extends Model
{
    protected $table = 'waiters';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'internal_note',
    ];
}
