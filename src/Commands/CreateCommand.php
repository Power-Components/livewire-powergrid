<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Exception;
use Illuminate\Console\Command;
use PowerComponents\LivewirePowerGrid\Commands\Actions\{AskColumnSource,
    AskComponentName,
    AskDatabaseTableName,
    AskModelName,
    CheckIfDatabaseHasTables,
    ConfirmAutoImportFields};
use PowerComponents\LivewirePowerGrid\Commands\Actions\AskComponentDatasource;
use PowerComponents\LivewirePowerGrid\Commands\Concerns\RenderAscii;
use PowerComponents\LivewirePowerGrid\Commands\Enums\{ColumnSource, Datasource};
use PowerComponents\LivewirePowerGrid\Commands\Support\PowerGridComponentMaker;

use function Laravel\Prompts\{error, info, note};

/** @codeCoverageIgnore */
class CreateCommand extends Command
{
    use RenderAscii;

    /** @var string */
    protected $signature = 'powergrid:create
        {--template= : name of the file that will be used as a template}
        {--columns= : source for auto-generated columns (fillable|table)}';

    /** @var string */
    protected $description = 'Make a new PowerGrid table component.';

    private PowerGridComponentMaker $component;

    public function handle(): int
    {
        $this->renderPowergridAscii();

        try {
            $this
                ->step1()
                ->step2()
                ->step3()
                ->step4()
                ->step5()
                ->step6()
                ->save()
                ->feedback();

            return self::SUCCESS;
        } catch (Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function step1(): self
    {
        $this->component = PowerGridComponentMaker::make(AskComponentName::handle());

        return $this;
    }

    private function step2(): self
    {
        $this->component->setDatasource(Datasource::from(AskComponentDatasource::handle()));

        return $this;
    }

    private function step3(): self
    {
        if ($this->component->canHaveModel() === false) {
            return $this;
        }

        ['model' => $model, 'fqn' => $fqn] = AskModelName::handle();

        $this->component->setModelWithFqn($model, $fqn);

        return $this;
    }

    private function step4(): self
    {
        if ($this->component->requiresDatabaseTableName()) {
            $this->component->setDatabaseTable(AskDatabaseTableName::handle());
        }

        return $this;
    }

    private function step5(): self
    {
        if (CheckIfDatabaseHasTables::handle() === false) {
            return $this;
        }

        if ($this->component->canAutoImportFields() === false
            || ConfirmAutoImportFields::handle($this->AutoImportLabel()) === false) {
            return $this;
        }

        $this->component->setAutoCreateColumns(true);

        if ($this->component->datasource === Datasource::ELOQUENT_BUILDER) {
            $this->component->setColumnSource($this->resolveColumnSource());
        }

        return $this;
    }

    private function resolveColumnSource(): ColumnSource
    {
        $columns = $this->option('columns');

        return match (is_string($columns) ? $columns : '') {
            'fillable' => ColumnSource::FILLABLE,
            'table', 'database', 'db' => ColumnSource::DATABASE_TABLE,
            default => ColumnSource::from(AskColumnSource::handle()),
        };
    }

    private function step6(): self
    {
        if (empty($this->option('template'))) {
            $this->component->loadPowerGridStub();
        } else {
            /** @var string $template */
            $template = $this->option('template');

            $this->component->loadCustomStub(base_path($template));
        }

        return $this;
    }

    private function save(): self
    {
        $this->component->saveToDisk();

        return $this;
    }

    public function feedback(): void
    {
        note("⚡ <comment>{$this->component->name}</comment> was successfully created at [<comment>{$this->component->createdPath()}</comment>].");

        note("💡 include the <comment>{$this->component->name}</comment> component using the tag: <comment>{$this->component->htmlTag}</comment>");

        info('👍 Please consider <comment>⭐ starring ⭐</comment> <info>our repository. Visit: </info><comment>https://github.com/Power-Components/livewire-powergrid</comment>'.PHP_EOL);
    }

    private function AutoImportLabel(): string
    {
        return 'Auto-import DataSource fields from '.
        (
            $this->component->requiresDatabaseTableName() ?
                 "[{$this->component->databaseTable}] table?" :
                 "[{$this->component->model}] Model?"
        );
    }
}
