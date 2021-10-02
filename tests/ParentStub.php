<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * @property int $id
 * @property int $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ParentStub extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name'
    ];

    public function dishes()
    {
        return $this->hasMany(ModelStub::class, "category_id");
    }
}
