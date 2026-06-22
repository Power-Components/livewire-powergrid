<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Minimal Livewire-facing surface for the Export feature. All export logic
 * lives in PowerComponents\LivewirePowerGrid\Plugins\Export\ExportPlugin;
 * these are the thin entry points Livewire can call (wire:click / wire:poll)
 * plus the single runtime state property the batch flow needs to persist
 * across requests.
 *
 * @codeCoverageIgnore
 */
trait HasExport
{
    /**
     * Runtime batch-export state. Keys: exporting, finished, id, progress, files, errors.
     *
     * @var array<string, mixed>
     */
    public array $exportState = [];

    public function exportToXLS(bool $selected = false): BinaryFileResponse|bool
    {
        /** @var BinaryFileResponse|bool $result */
        $result = $this->delegateToPlugin('exportToXLS', [$selected]);

        return $result;
    }

    public function exportToCsv(bool $selected = false): BinaryFileResponse|bool
    {
        /** @var BinaryFileResponse|bool $result */
        $result = $this->delegateToPlugin('exportToCsv', [$selected]);

        return $result;
    }

    public function downloadExport(string $file): BinaryFileResponse
    {
        /** @var BinaryFileResponse $result */
        $result = $this->delegateToPlugin('downloadExport', [$file]);

        return $result;
    }

    public function updateExportProgress(): void
    {
        $this->delegateToPlugin('updateExportProgress', []);
    }
}
