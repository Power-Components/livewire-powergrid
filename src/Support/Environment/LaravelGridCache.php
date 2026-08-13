<?php

namespace PowerComponents\LivewirePowerGrid\Support\Environment;

use Closure;
use Illuminate\Support\Facades\Cache;
use PowerComponents\LivewirePowerGrid\Contracts\GridCache;

final class LaravelGridCache implements GridCache
{
    public function supportsTags(): bool
    {
        return Cache::supportsTags();
    }

    /** @param  Closure(): mixed  $callback */
    public function remember(string $key, int $ttl, Closure $callback): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /** @param  Closure(): mixed  $callback */
    public function taggedRemember(string $tag, string $key, int $ttl, Closure $callback): mixed
    {
        return Cache::tags($tag)->remember($key, $ttl, $callback);
    }
}
