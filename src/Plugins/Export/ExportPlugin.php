<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export;

use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent;
use Illuminate\Support;
use Illuminate\Support\{Collection, LazyCollection, Str};
use Illuminate\Support\Facades\Bus;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Themes\{DaisyUI, Flux, Theme};
use PowerComponents\Turbine\Components\SetUp\Exportable;
use PowerComponents\Turbine\DataSource\ProcessDataSource;
use PowerComponents\Turbine\Export\ExportEngine;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Feature plugin that renders the export UI into the header zone and handles
 * synchronous (XLS/CSV download) and batched/queued exports.
 *
 * Runtime batch state lives in a single component property: $component->exportState.
 *
 * @codeCoverageIgnore
 */
class ExportPlugin extends PluginBase
{
    public function name(): string
    {
        return 'export';
    }

    public function isEnabled(): bool
    {
        return ! empty(data_get($this->component->setUp, 'exportable'));
    }

    public function scripts(): array
    {
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;
        $theme = $theme instanceof Theme ? $theme : null;
        $js = dirname(__DIR__, 3).'/resources/js/components';
        $files = [];

        if (! $theme || $theme->usesAlpineDropdown()) {
            $files[] = $js.'/pg-dropdown.js';
        }

        if (! $theme || $theme->usesAlpineExport()) {
            $files[] = $js.'/pg-export.js';
        }

        return $files;
    }

    public function handlesZone(string $zone): bool
    {
        return in_array($zone, ['header', 'header.bottom'], true) && $this->isEnabled();
    }

    public function renderZone(string $zone): ?string
    {
        if (! $this->handlesZone($zone)) {
            return null;
        }

        return match ($zone) {
            'header' => $this->renderDropdown(),
            'header.bottom' => $this->renderProgress(),
            default => null,
        };
    }

    /**
     * The export dropdown injected into the header actions row.
     */
    private function renderDropdown(): string
    {
        $exportable = data_get($this->component->setUp, 'exportable');

        /** @var view-string $viewName */
        $viewName = $this->resolveThemeView();

        return view($viewName, [
            'element' => $this->component->headerElement('export'),
            'tableName' => $this->component->tableName,
            'types' => (array) data_get($exportable, 'type', []),
            'total' => $this->component->total(),
            'enabledFiltersCount' => count($this->component->enabledFilters),
            'checkbox' => $this->component->checkbox,
        ])->render();
    }

    /**
     * The batch-export progress/download panel rendered below the header.
     * Turbine ships no default panel: the user opts in by registering their
     * own view via Exportable::progressView(). The view receives $exportState
     * (keys: exporting, finished, id, progress, files, errors) and $tableName.
     */
    private function renderProgress(): ?string
    {
        $exportable = data_get($this->component->setUp, 'exportable');

        /** @var view-string|null $progressView */
        $progressView = data_get($exportable, 'progressView');

        if (! is_string($progressView) || blank($progressView)) {
            return null;
        }

        return view($progressView, [
            'exportState' => $this->component->exportState,
            'tableName' => $this->component->tableName,
        ])->render();
    }

    /**
     * Pick the theme-specific export view so each theme keeps its native look
     * (DaisyUI popover, Flux dropdown, Tailwind/Alpine dropdown). Custom themes
     * fall back to the Tailwind-styled default view.
     *
     * @return view-string
     */
    private function resolveThemeView(): string
    {
        $tokenView = theme_view('header.export');

        if ($tokenView !== '' && view()->exists($tokenView)) {
            /** @var view-string&non-empty-string $tokenView */
            return $tokenView;
        }

        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        $variant = match (true) {
            $theme instanceof DaisyUI => 'daisyui',
            $theme instanceof Flux => 'flux',
            default => 'index',
        };

        /** @var view-string $view */
        $view = "powergrid-plugins::Export.themes.{$variant}";

        return $view;
    }

    public function exportToXLS(bool $selected = false): BinaryFileResponse|bool
    {
        return $this->export(Exportable::TYPE_XLS, $selected);
    }

    public function exportToCsv(bool $selected = false): BinaryFileResponse|bool
    {
        return $this->export(Exportable::TYPE_CSV, $selected);
    }

    public function downloadExport(string $file): ?BinaryFileResponse
    {
        /** @var array<int, string> $exportedFiles */
        $exportedFiles = (array) data_get($this->component->exportState, 'files', []);

        if ($file === '' || basename($file) !== $file || ! in_array($file, $exportedFiles, true)) {
            return null;
        }

        /** @var string $disk */
        $disk = data_get($this->component->setUp, 'exportable.disk', 'local');

        /** @var string $directory */
        $directory = data_get($this->component->setUp, 'exportable.directory', '');

        $directory = trim($directory, '/');
        $path = $directory !== '' ? $directory.'/'.$file : $file;

        $storage = Support\Facades\Storage::disk($disk);

        $realPath = realpath($storage->path($path));
        $base = realpath($storage->path($directory));

        if ($realPath === false || $base === false
            || ! str_starts_with($realPath, rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)) {
            return null;
        }

        return response()->download($realPath);
    }

    public function exportBatch(): ?Batch
    {
        /** @var string|null $id */
        $id = data_get($this->component->exportState, 'id');
        $id = strval($id);

        if (empty($id)) {
            return null;
        }

        return Bus::findBatch($id);
    }

    public function updateExportProgress(): void
    {
        $batch = $this->exportBatch();

        if (is_null($batch)) {
            return;
        }

        $finished = $batch->finished();

        $this->component->exportState = array_merge($this->component->exportState, [
            'finished' => $finished,
            'progress' => $batch->progress(),
            'errors' => $batch->hasFailures(),
            'exporting' => ! $finished,
        ]);

        $this->component->onPluginUpdated('export', 'batchExecuting', ['batch' => $batch]);
    }

    /**
     * @throws Exception | Throwable
     */
    private function export(string $exportType, bool $selected): BinaryFileResponse|bool
    {
        if ($this->getQueuesCount() > 0 && ! $selected) {
            $exportableClass = $this->getExportableClassFromConfig($exportType);

            return $this->runOnQueue($exportableClass, $exportType);
        }

        if (count($this->component->checkboxValues) === 0 && $selected) {
            return false;
        }

        /** @var string $fileName */
        $fileName = data_get($this->component->setUp, 'exportable.fileName');

        /** @var array<string, mixed> $exportOptions */
        $exportOptions = (array) data_get($this->component->setUp, 'exportable', []);
        if ($selected) {
            $exportOptions['selectedKeys'] = $this->component->checkboxValues;
        }

        $exportEngine = new ExportEngine();
        $filePath = $exportEngine->build(
            context: $this->component,
            exportType: $exportType,
            fileName: $fileName,
            exportOptions: $exportOptions,
            selected: $selected
        );

        $deleteFileAfterSend = boolval(data_get($exportOptions, 'deleteFileAfterSend', true));

        return response()
            ->download($filePath)
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws Throwable
     */
    private function runOnQueue(string $exportFileType, string $exportType): bool
    {
        $this->component->exportState = array_merge($this->component->exportState, [
            'exporting' => true,
            'finished' => false,
        ]);

        $queues = $this->putQueuesToBus($exportFileType, $exportType);

        $batch = Bus::batch($queues->toArray())
            ->name($this->getBatchName())
            ->onQueue($this->getOnQueue())
            ->onConnection($this->getQueuesConnection())
            ->then(fn (Batch $batch) => $this->component->onPluginUpdated('export', 'batchThen', ['batch' => $batch]))
            ->catch(fn (Batch $batch, Throwable $e) => $this->component->onPluginUpdated('export', 'batchCatch', ['batch' => $batch, 'exception' => $e]))
            ->finally(fn (Batch $batch) => $this->component->onPluginUpdated('export', 'batchFinally', ['batch' => $batch]))
            ->dispatch();

        $this->component->exportState = array_merge($this->component->exportState, [
            'id' => $batch->id,
        ]);

        return true;
    }

    /** @return Collection<int, mixed> */
    private function putQueuesToBus(string $exportableClass, string $fileExtension): Collection
    {
        $component = $this->component;
        $processDataSource = tap(ProcessDataSource::make($component), fn ($datasource) => $datasource->get());

        $files = [];
        $filters = $component->filters;
        $filtered = $component->filtered;
        $queues = collect([]);

        $total = (int) $component->total();
        $queueCount = $total > $this->getQueuesCount() ? $this->getQueuesCount() : 1;

        $perPage = (int) ceil($total / $queueCount);

        $offset = 0;

        /** @var class-string $jobClass */
        $jobClass = $this->getJobClass();

        /** @var string $exportFileName */
        $exportFileName = data_get($component->setUp, 'exportable.fileName');

        for ($i = 1; $i <= $queueCount; $i++) {
            $fileName = Str::kebab($exportFileName).
                '-'.round(($offset + 1), 2).
                '-'.round(($offset + $perPage), 2).
                '-'.$component->getId().
                '.'.$fileExtension;

            $params = [
                'filtered' => $filtered,
                'exportableClass' => $exportableClass,
                'fileName' => $fileName,
                'offset' => $offset,
                'limit' => $perPage,
                'filters' => Support\Facades\Crypt::encrypt($filters),
                'exportable' => (array) $component->setUp['exportable'],
                'parameters' => Support\Facades\Crypt::encrypt($component->getPublicPropertiesDefinedInComponent()),
            ];

            $queues->push(new $jobClass(
                get_class($component),
                $component->columns(),
                $params,
            ));

            $files[] = $fileName;

            $offset += $perPage;
        }

        $this->component->exportState = array_merge($this->component->exportState, [
            'files' => $files,
        ]);

        return $queues;
    }

    /**
     * @return Eloquent\Collection<int, mixed>|Collection<int, mixed>|LazyCollection<int, mixed>
     *
     * @throws Exception
     */
    public function prepareToExport(bool $selected = false): Eloquent\Collection|Collection|LazyCollection
    {
        $exportEngine = new ExportEngine();
        /** @var array<string, mixed> $exportOptions */
        $exportOptions = (array) data_get($this->component->setUp, 'exportable', []);
        if ($selected) {
            $exportOptions['selectedKeys'] = $this->component->checkboxValues;
        }

        return $exportEngine->prepareDataset($this->component, $exportOptions, $selected);
    }

    private function getExportableClassFromConfig(string $exportType): string
    {
        /** @var string $defaultExportable */
        $defaultExportable = config('livewire-powergrid.exportable.default');

        /** @var string|null $exportableClass */
        $exportableClass = data_get(config('livewire-powergrid.exportable'), $defaultExportable.'.'.$exportType);

        if (empty($exportableClass) || ! class_exists($exportableClass)) {
            throw new Exception(
                "PowerGrid export driver not found for [{$defaultExportable}.{$exportType}]. ".
                "Check 'livewire-powergrid.exportable.default' and make sure the '{$defaultExportable}' driver maps '{$exportType}' to an existing class (and that openspout/openspout is installed)."
            );
        }

        return strval($exportableClass);
    }

    private function getQueuesCount(): int
    {
        /** @var int|string|null $queues */
        $queues = data_get($this->component->setUp, 'exportable.batchExport.queues', 0);

        return intval($queues);
    }

    private function getQueuesConnection(): string
    {
        /** @var string|null $connection */
        $connection = data_get($this->component->setUp, 'exportable.batchExport.onConnection');

        return strval($connection);
    }

    private function getOnQueue(): string
    {
        /** @var string|null $onQueue */
        $onQueue = data_get($this->component->setUp, 'exportable.batchExport.onQueue');

        return strval($onQueue);
    }

    private function getBatchName(): string
    {
        /** @var string $name */
        $name = data_get($this->component->setUp, 'exportable.batchName', 'PowerGrid batch export');

        return $name;
    }

    private function getJobClass(): string
    {
        /** @var string|null $jobClass */
        $jobClass = data_get($this->component->setUp, 'exportable.jobClass');

        return ! empty($jobClass) ? $jobClass : ExportJob::class;
    }
}
