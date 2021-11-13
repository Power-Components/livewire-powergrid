<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Composer\Factory;
use Composer\IO\NullIo;
use Composer\Repository\InstalledRepositoryInterface;
use Illuminate\Support\Carbon;

class InteractsWithVersions
{
    /**
     * The latest version resolver.
     *
     * @var callable|null
     */
    protected static $latestVersionResolver = null;

    /**
     * Warns the user about the latest version of PowerGrid.
     *
     * @return array
     */
    public function ensureLatestVersion(): array
    {
        /** @phpstan-ignore-next-line */
        $composer  = Factory::create(new NullIo(), null, false);
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();

        return $this->searchPackage($localRepo);
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
    public function getLatestVersion(): string
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
