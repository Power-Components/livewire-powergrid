<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Throwable;

trait SoftDeletes
{
    public string $softDeletes = '';

    public function softDeletes(string $softDeletes): void
    {
        $this->softDeletes = $softDeletes;
    }

    /**
     * @throws Throwable
     */
    public function applySoftDeletes(Builder|MorphToMany $results): Builder|MorphToMany
    {
        throw_if(
            $this->softDeletes && !in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(get_class($results->getModel())), true),
            new Exception(get_class($results->getModel()) . ' is not using the \Illuminate\Database\Eloquent\SoftDeletes trait')
        );

        return match ($this->softDeletes) {
            /** @phpstan-ignore-next-line  */
            'withTrashed' => $results->withTrashed(),
            /** @phpstan-ignore-next-line  */
            'onlyTrashed' => $results->onlyTrashed(),
            default       => $results
        };
    }
}
