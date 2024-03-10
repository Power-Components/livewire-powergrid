<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Actions\{GetStubVarsFromDbTable, GetStubVarsFromFromModel, SanitizeComponentName};
use PowerComponents\LivewirePowerGrid\Enums\Datasource;

/**
  * @property-read PowerGridStub $stub;
  * @property-read Datasource $datasource;
  * @property-read string $name;
  * @property-read string $namespace
  * @property-read string $folder
  * @property-read string $filename
  * @property-read string $htmlTag
  * @property-read string $fqn
  * @property-read string $databaseTable
  * @property-read string $model
  * @property-read string $modelFqn
  * @property-read bool $autoCreateColumns
  * @property-read bool $usesCustomStub
  * @property-read bool $isProcessed
 */
final class PowerGridComponentMaker
{
    private ?PowerGridStub $stub;

    private ?Datasource $datasource;

    private string $name;

    private string $namespace = '';

    private string $folder = '';

    private string $filename = '';

    private string $htmlTag = '';

    private string $fqn = '';

    private string $databaseTable = '';

    private string $model = '';

    private string $modelFqn = '';

    private bool $autoCreateColumns = false;

    private bool $usesCustomStub = false;

    private bool $isProcessed = false;

    public function __construct(string $name)
    {
        $this->resolveNameFolderFilename(SanitizeComponentName::handle($name))
            ->resolveNamespace()
            ->resolveFqn()
            ->resolveHtmlTag();
    }

    public static function make(string $name): self
    {
        return (new self($name));
    }

    public function requiresDatabaseTableName(): bool
    {
        return $this->datasource->requiresDatabaseTableName();
    }

    public function canHaveModel(): bool
    {
        return $this->datasource->canHaveModel();
    }

    public function canAutoImportFields(): bool
    {
        return $this->datasource->canAutoImportFields();
    }

    public function autoCreateColumns(): bool
    {
        return $this->autoCreateColumns;
    }

    public function isProcessed(): bool
    {
        return $this->isProcessed;
    }

    public function savePath(string $filename = ''): string
    {
        $path = $this->folder;

        if ($filename !== '') {
            $path .= $this->folder = DIRECTORY_SEPARATOR . $filename;
        }

        return powergrid_components_path($path);
    }

    public function setModelWithFqn(string $model, string $modelFqn): self
    {
        $this->model = $model;

        $this->modelFqn = $modelFqn;

        return $this;
    }

    public function loadCustomStub(string $path): self
    {
        $this->usesCustomStub = true;

        $this->stub = PowerGridStub::make($path);

        return $this;
    }

    public function loadPowerGridStub(): self
    {
        $path = powergrid_stubs_path($this->datasource->stubTemplate());

        if ($this->autoCreateColumns()) {
            $path = str_replace('.stub', '.fillable.stub', $path);
        }

        $this->stub = PowerGridStub::make($path);

        $this->usesCustomStub = false;

        return $this;
    }

    public function setAutoCreateColumns(bool $autoCreateColumns = true): self
    {
        $this->autoCreateColumns = $autoCreateColumns;

        return $this;
    }

    public function setDatasource(Datasource $datasource): self
    {
        $this->datasource = $datasource;

        return $this;
    }

    public function setDatabaseTable(string $databaseTable): self
    {
        $this->databaseTable = $databaseTable;

        return $this;
    }

    public function saveToString(): string
    {
        $this->process();

        return $this->stub->render();
    }

    public function saveToDisk(): self
    {
        File::ensureDirectoryExists($this->savePath());

        File::put($this->savePath($this->filename), $this->saveToString());

        return $this;
    }

    private function process(): self
    {
        $this->stub->setVar('namespace', $this->namespace);
        $this->stub->setVar('componentName', $this->name);

        $this->stub->setVar('model', $this->model);
        $this->stub->setVar('modelFqn', $this->modelFqn);

        $this->stub->setVar('databaseTableName', $this->databaseTable);

        if ($this->autoCreateColumns() === true) {
            if ($this->datasource === Datasource::ELOQUENT_BUILDER) {
                ['PowerGridFields' => $PowerGridFields, 'filters' => $filters, 'columns' => $columns] = GetStubVarsFromFromModel::handle($this);
            }

            if ($this->datasource === Datasource::QUERY_BUILDER) {
                ['PowerGridFields' => $PowerGridFields, 'filters' => $filters, 'columns' => $columns] = GetStubVarsFromDbTable::handle($this);
            }
        }

        $this->stub->setVar('PowerGridFields', $PowerGridFields ?? '');

        $this->stub->setVar('filters', $filters ?? '');

        $this->stub->setVar('columns', $columns ?? '');

        $this->isProcessed = true;

        return $this;
    }

    private function resolveNameFolderFilename(string $name): self
    {
        $this->folder = $this->resolveFolder($name);

        $this->name = Str::of($name)->afterLast('\\')->toString();

        $this->filename = Str::of($this->name)->append('.php')->toString();

        return $this;
    }

    private function resolveFolder(string $name): string
    {
        if (!str_contains($name, '\\')) {
            return '';
        }

        return Str::of($name)->beforeLast('\\')
            ->toString();
    }

    private function resolveNamespace(): self
    {
        $this->namespace = rtrim($this->livewireNamespace() . '\\' . $this->folder, '\\');

        return $this;
    }

    private function resolveFqn(): self
    {
        $this->fqn = $this->namespace . '\\' . $this->name;

        return $this;
    }

    public function createdPath(): string
    {
        return str($this->namespace)
                    ->replace('App', 'app')
                    ->append('\\' . $this->filename)
                    ->replace('\\', '/');
    }

    private function resolveHtmlTag(): self
    {
        $this->htmlTag = str($this->name)
            ->kebab()
            ->prepend($this->folder . '\\')
            ->lower()
            ->replace('\\', '.')
            ->ltrim('.')
            ->prepend('<livewire:')
            ->append('/>');

        return $this;
    }

    private function livewireNamespace(): string
    {
        return strval(config('livewire.class_namespace'));
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \Exception("Attribute [{$name}] does not exist.");
    }
}
