<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Category $category_id
 * @property string $name
 * @property float $price
 * @property int $calories
 * @property bool $in_stock
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $produced_at
 *
 * @property-read Category $category
 */
class Dish extends Model
{
    protected $guarded = [];

    protected $table = 'dishes';

    public static function servedAt()
    {
        return  Self::select('serving_at')->distinct('serving_at')->get();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
