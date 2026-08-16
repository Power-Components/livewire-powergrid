<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Exception;
use Illuminate\Console\Command;
use PowerComponents\LivewirePowerGrid\Commands\Actions\{AskColumnSource, AskComponentDatasource, AskComponentName, AskDatabaseTableName, AskModelName, BuildStubVars, CheckIfDatabaseHasTables, ConfirmAutoImportFields, ConfirmGeneratedColumns, ResolveGeneratedColumns};
use PowerComponents\LivewirePowerGrid\Commands\Concerns\RenderAscii;
use PowerComponents\LivewirePowerGrid\Commands\Enums\{ColumnSource, Datasource};
use PowerComponents\LivewirePowerGrid\Commands\Support\PowerGridComponentMaker;

use function Laravel\Prompts\{error, info, note, warning};

/** @codeCoverageIgnore */
class CreateCommand extends Command
{
    use RenderAscii;

    /** @var string */
    protected $signature = 'powergrid:create {--template= : name of the file that will be used as a template}';

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

        // Query Builder has only one possible source, so it is not asked.
        if ($this->component->canHaveModel()) {
            $this->component->setColumnSource(
                ColumnSource::from(
                    AskColumnSource::handle($this->component->model, $this->component->modelTable())
                )
            );
        }

        if ($this->previewColumns() && ConfirmGeneratedColumns::handle() === false) {
            $this->component->setAutoCreateColumns(false);

            // There is nothing else to read the fields from in this run:
            // re-run the command to generate them from the other source.
            warning('🚫 Fields discarded. Only the Action column will be generated.');
        }

        return $this;
    }

    /**
     * Shows which columns the chosen source will generate, before the component is written.
     *
     * @return bool Whether the source has any field to generate.
     */
    private function previewColumns(): bool
    {
        $columns = ResolveGeneratedColumns::handle($this->component);

        if ($columns->isEmpty()) {
            warning("🚫 No fields found in {$this->columnSourceLabel()}. Only the Action column will be generated.");

            return false;
        }

        note("👀 Preview of the fields from {$this->columnSourceLabel()}:");

        $this->table(
            ['Field', 'Type', 'Generated as'],
            $columns->map(
                fn (string $type, string $field): array => [$field, $type, BuildStubVars::describe($type)]
            )->values()->all()
        );

        return true;
    }

    private function columnSourceLabel(): string
    {
        if ($this->component->requiresDatabaseTableName()) {
            return "the [{$this->component->databaseTable}] table";
        }

        return $this->component->columnSource === ColumnSource::DATABASE_TABLE
            ? "the [{$this->component->modelTable()}] table"
            : "\$fillable in [{$this->component->model}]";
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
        return 'Auto-import Data Source fields from '.
        (
            $this->component->requiresDatabaseTableName() ?
                 "[{$this->component->databaseTable}] table?" :
                 "[{$this->component->model}] Model?"
        );
    }
}
