<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @codeCoverageIgnore
 */
trait HasExport
{
    /**
     * Runtime batch-export state. Keys: exporting, finished, id, progress, files, errors.
     * Server-owned: written only by the export flow, never accepted from the client.
     *
     * @var array<string, mixed>
     */
    #[Locked]
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

    public function downloadExport(string $file): ?BinaryFileResponse
    {
        /** @var BinaryFileResponse|null $result */
        $result = $this->delegateToPlugin('downloadExport', [$file]);

        return $result;
    }

    public function updateExportProgress(): void
    {
        $this->delegateToPlugin('updateExportProgress', []);
    }
}
