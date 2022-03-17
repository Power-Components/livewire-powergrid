<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PowerComponents\LivewirePowerGrid\Helpers\InteractsWithVersions;

class UpdateCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:update';

    protected $description = 'Check if there is a new version of PowerGrid.';

    public function handle(): int
    {
        if (config('livewire-powergrid.check_version') === false) {
            return self::SUCCESS;
        }

        try {
            $ensureLatestVersion = new InteractsWithVersions();

            $current = $ensureLatestVersion->ensureLatestVersion();

            if (isset($current['version'])) {
                if (version_compare($remote = $ensureLatestVersion->getLatestVersion(), $current['version']) > 0) {
                    $this->info(" You are using an outdated version <comment>{$current['version']}</comment> of PowerGrid ⚡. Please consider upgrading to <comment>{$remote}</comment>");
                    $this->info(" Released Date: <comment>{$current['release']}</comment>");
                }
            }
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
