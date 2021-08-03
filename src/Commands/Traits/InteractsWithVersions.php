<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Traits;

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\InstalledRepositoryInterface;
use Illuminate\Support\Carbon;

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
        $composer  = Factory::create(new NullIo(), null, false);
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();

        $current   = $this->searchPackage($localRepo);

        if (isset($current['version'])) {
            if (version_compare($remote = $this->getLatestVersion(), $current['version']) > 0) {
                $this->info(" You are using an outdated version <comment>{$current['version']}</comment> of PowerGrid ⚡. Please update to <comment>{$remote}</comment>");
                $this->info(" Released Date: <comment>{$current['release']}</comment>");
            }
        }
    }

    /**
     * Search package version.
     *
     * @param InstalledRepositoryInterface $localRepo
     * @return array
     */
    public function searchPackage(InstalledRepositoryInterface $localRepo): array
    {
        foreach ($localRepo->getPackages() as $package) {
            if ($package->getName() === 'power-components/livewire-powergrid') {
                return [
                    'version' => $package->getPrettyVersion(),
                    'release' => Carbon::parse($package->getReleaseDate())->format('M d, Y h:i A'),
                ];
            }
        }

        return [];
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
}
