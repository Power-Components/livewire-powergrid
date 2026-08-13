<?php

namespace PowerComponents\LivewirePowerGrid\Contracts;

use Closure;

interface GridCache
{
    public function supportsTags(): bool;

    /** @param  Closure(): mixed  $callback */
    public function remember(string $key, int $ttl, Closure $callback): mixed;

    /** @param  Closure(): mixed  $callback */
    public function taggedRemember(string $tag, string $key, int $ttl, Closure $callback): mixed;
}
