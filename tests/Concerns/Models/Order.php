<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property float $tax
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $produced_at
 */
class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'orders';

    protected $casts = [
        'name'      => 'string',
        'link'      => 'string',
        'price'     => 'decimal:2',
        'tax'       => 'float',
        'is_active' => 'boolean',
    ];
}
