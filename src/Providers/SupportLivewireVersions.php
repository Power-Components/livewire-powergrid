<?php

namespace PowerComponents\LivewirePowerGrid\Providers;

use Composer\InstalledVersions;

class SupportLivewireVersions
{
    private static ?string $version = null;

    public static function version(): ?string
    {
        if (self::$version !== null) {
            return self::$version;
        }

        if (! class_exists(InstalledVersions::class)) {
            return null;
        }

        return self::$version = InstalledVersions::getPrettyVersion('livewire/livewire');
    }

    public static function isV4(): bool
    {
        return str_starts_with(self::version() ?? '', 'v4.');
    }

    public static function isV3(): bool
    {
        return str_starts_with(self::version() ?? '', 'v3.');
    }
}
