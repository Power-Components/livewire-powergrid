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
                    $this->info("✨ You are using an outdated ⚡ PowerGrid ⚡ version (<comment>{$current['version']}</comment>).");

                    $this->info("   Please consider upgrading to <comment>{$remote}</comment>, released at: <comment>{$current['release']}</comment>\n\n");
                }
            }
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
