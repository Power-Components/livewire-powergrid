<?php

namespace PowerComponents\LivewirePowerGrid;

final class Cache
{
    public string $name = 'cache';

    public bool $enabled = true;

    public bool $forever = false;

    public string $tag = '';

    public int $ttl = 300;

    public string $prefix = '';

    public static function make(): self
    {
        return new Cache();
    }

    public function forever(): Cache
    {
        $this->forever = true;

        return $this;
    }

    public function disabled(): Cache
    {
        $this->enabled = false;

        return $this;
    }

    public function customTag(string $tag): Cache
    {
        $this->tag = $tag;

        return $this;
    }

    public function prefix(string $prefix): Cache
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function ttl(int $time): Cache
    {
        $this->ttl = $time;

        return $this;
    }
}
