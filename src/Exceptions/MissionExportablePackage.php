<?php

namespace PowerComponents\LivewirePowerGrid\Exceptions;

use Exception;

class MissionExportablePackage extends Exception
{
    public function __construct(string $package)
    {
        parent::__construct(
            "You need to install the [{$this->getPackages($package)}] package to perform this action"
        );
    }

    private function getPackages(string $package): string
    {
        return match ($package) {
            'openspout_v3', 'openspout_v4' => 'openspout/openspout',
            default => ''
        };
    }
}
