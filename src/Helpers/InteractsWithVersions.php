<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\InstalledRepositoryInterface;
use Exception;
use Illuminate\Support\Carbon;

/** @codeCoverageIgnore */
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
     * @throws Exception
     */
    public function getLatestVersion(): string
    {
        $resolver = static::$latestVersionResolver ?? function () {
            $json = file_get_contents(
                'https://packagist.org/p2/power-components/livewire-powergrid.json'
            );
            if (is_string($json) === false) {
                throw new Exception('Error: could not access PowerGrid versions URL');
            }

            /** @var array $package */
            $package = json_decode($json, true);

            /** @phpstan-ignore-next-line */
            $version = collect($package['packages']['power-components/livewire-powergrid'])
                    ->first()['version'];

            if (!is_string($version)) {
                throw new Exception('Error: could find PowerGrid version.');
            }

            return $version;
        };

        if (is_callable($resolver)) {
            $version = call_user_func($resolver);
            if (!is_string($version)) {
                throw new Exception('Error: could find PowerGrid version.');
            }

            return $version;
        }
    }
}
