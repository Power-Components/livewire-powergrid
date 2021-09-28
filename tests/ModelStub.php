<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelStub extends Model
{
    protected array $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentStub::class);
    }
}
