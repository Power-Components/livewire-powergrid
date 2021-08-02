<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Traits;

trait InteractsWithVersions
{
    /**
     * The latest version resolver.
     *
     * @var callable|null
     */
    protected static $latestVersionResolver = null;

    /**
     * Warns the user about the latest version of Forge CLI.
     *
     * @return void
     */
    protected function ensureLatestVersion()
    {
        $current = 'v' . config('livewire-powergrid.version');

        if (version_compare($remote = $this->getLatestVersion(), $current) > 0) {
            $this->info("You are using an outdated version {$current} of PowerGrid. Please update to {$remote}");
        }
    }

    /**
     * Returns the latest version.
     *
     * @return string
     */
    protected function getLatestVersion(): string
    {
        $resolver = static::$latestVersionResolver ?? function () {
            $package = json_decode(file_get_contents(
                    'https://packagist.org/p2/power-components/livewire-powergrid.json'
                ), true);

            return collect($package['packages']['power-components/livewire-powergrid'])
                    ->first()['version'];
        };

        return call_user_func($resolver);
    }

    /**
     * Sets the latest version resolver.
     *
     * @param callable $resolver
     * @return void
     */
    public static function resolveLatestVersionUsing(callable $resolver)
    {
        static::$latestVersionResolver = $resolver;
    }
}
