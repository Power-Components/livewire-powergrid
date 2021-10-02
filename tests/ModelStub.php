<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ParentStub $category_id
 * @property string $name
 * @property float $price
 * @property int $calories
 * @property bool $in_stock
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $produced_at
 *
 */
class ModelStub extends Model
{
    protected $guarded = [];

    protected $table = 'dishes';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentStub::class);
    }
}
