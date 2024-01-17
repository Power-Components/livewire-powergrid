<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Restaurant extends Model
{
    protected $table = 'restaurants';

    protected $fillable = [
        'name',
    ];

    public function chefs(): HasMany
    {
        return $this->hasMany(Chef::class, 'restaurant_id');
    }
}
