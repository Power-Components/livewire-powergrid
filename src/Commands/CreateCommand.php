<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;

use PowerComponents\LivewirePowerGrid\Commands\Concerns\RenderAscii;

use PowerComponents\LivewirePowerGrid\Support\TempComponent;

use function Laravel\Prompts\{confirm, error, info};

class CreateCommand extends Command
{
    use RenderAscii;

    /** @var string */
    // protected $signature = 'powergrid:create {--template= : name of the file that will be used as a template}';
    protected $signature = 'ovo';

    /** @var string */
    protected $description = 'Make a new PowerGrid table component.';

    protected ?TempComponent $component;

    public function handle(): int
    {
        $this->renderPowergridAscii();

        $this->call('powergrid:update');

        $this->call('powergrid:check-dependencies'); //enable me

        try {
            $this->create();
        } catch (\Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function create(): void
    {
        $this->component = TempComponent::make($this->call('powergrid:ask-component-name'));

        $this->component->setDatasource($this->call('powergrid:choose-datasource'));

        $this->component->loadStub($this->option('template'));

        $this->component->setDatabaseTablename($this->call('powergrid:ask-database-table-name')); // not needed here

        list($model, $modelFqn) = $this->call('powergrid:ask-model-name');

        $this->component->setModelFqn($model, $modelFqn);

        $this->COMO_VAI_CHAMAR_ISSO();
    }

    public function fillable()
    {
        $this->useFillable = confirm('Create columns based on Model\'s fillable property?');

        if ($this->useFillable) {
            if (strtolower($this->datasourceOption) === strtolower(self::DATASOURCE_QUERY_BUILDER)) {
                $this->askDataBaseTableName();

                $this->stub = FillableTable::queryBuilder($this->dataBaseTableName, strval($this->option('template')));
            } else {
                $this->stub = FillableTable::eloquentBuilder($this->model, $this->modelName, strval($this->option('template')));
            }
        }
    }

    protected function COMO_VAI_CHAMAR_ISSO(): void
    {
        if (in_array(strtolower($this->datasourceOption), [strtolower(self::DATASOURCE_ELOQUENT_BUILDER), strtolower(self::DATASOURCE_QUERY_BUILDER)])) {
            $this->fillable();
        }

        $this->component->saveToDisk();

        $this->showCreated();
    }

    protected function showCreated(): void
    {
        $thanks = strval(str_replace(',', '!', strval(__('Thanks,'))));

        info("⚡ <comment>" . $this->component->filename . '</comment> was successfully created at [<comment>app' . $this->component->savedPath . '</comment>].');

        info("⚡ Your PowerGrid table can be now included with the tag: <comment>" . $this->component->componentName . '</comment>');

        info("⭐ <comment>{$thanks}</comment> Please consider <comment>starring</comment> our repository at <comment>https://github.com/Power-Components/livewire-powergrid</comment> ⭐\n");
    }
}
