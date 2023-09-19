<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{File, Schema};
use Illuminate\Support\{Arr, Str};

use function Laravel\Prompts\{confirm, info, select, suggest, text};

use PowerComponents\LivewirePowerGrid\Commands\Actions\{DatabaseTables, FillableTable, Models, Stubs, TailwindForm};
use PowerComponents\LivewirePowerGrid\Commands\Concerns\RenderAscii;
use PowerComponents\LivewirePowerGrid\Commands\Exceptions\CreateCommandException;

class CreateCommand extends Command
{
    use RenderAscii;

    /** @var string */
    protected $signature = 'powergrid:create {--template= : name of the file that will be used as a template}';

    /** @var string */
    protected $description = 'Make a new PowerGrid table component.';

    protected string $tableName;

    protected string $dataBaseTableName;

    protected bool $useFillable = false;

    /** @var array<int, string> $modelPath */
    protected array $modelPath = [];

    protected string $datasourceOption;

    protected string $stub;

    protected string $model;

    protected string $modelName;

    protected string $cleanModelName;

    protected string $componentFilename;

    protected string $componentName;

    protected string $savedAt;

    public function handle(): int
    {
        $this->renderPowergridAscii();

        $this->call('powergrid:update');

        try {
            $this->askTableName();
            $this->askDatasource();
            $this->askModel();
        } catch (CreateCommandException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->checkTailwindForms();

        $this->showCreated();

        return self::SUCCESS;
    }

    /**
     * @throws CreateCommandException
     */
    protected function askTableName(): void
    {
        $this->tableName = text(
            label: 'What is the name of your Table Component?',
            placeholder: 'UserTable',
            default: 'UserTable',
            required: true
        );

        $this->tableName = str_replace(['.', '\\'], '/', (string) $this->tableName);

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $this->tableName, $matches);

        if (!is_array($matches)) {
            throw new CreateCommandException('Could not parse model name');
        }
    }

    /**
     * @throws CreateCommandException
     */
    protected function askDataBaseTableName(): void
    {
        $this->dataBaseTableName = suggest(
            label: 'What is the name of your database table name?',
            options: DatabaseTables::list(),
            required: true
        );

        $exists = Schema::hasTable($this->dataBaseTableName);

        if (!$exists && !app()->runningUnitTests()) {
            $this->components->error('The table you provided does not exist!');
            $this->askDataBaseTableName();
        }
    }

    protected function askDatasource(): void
    {
        $datasourceOption = select(
            label: 'What type of data source will you use?',
            options: [
                'Eloquent Builder',
                'Query Builder',
                'Collection',
            ],
            default: 'Eloquent Builder'
        );

        $this->datasourceOption = match ($datasourceOption) {
            'Eloquent Builder' => 'm',
            'Query Builder'    => 'qb',
            default            => 'c'
        };
    }

    /**
     * @throws CreateCommandException
     * @throws Exception
     * @throws \Exception
     */
    protected function askModel(): void
    {
        $this->stub = Stubs::load($this->datasourceOption, strval($this->option('template')));

        if (strtolower($this->datasourceOption) === 'm') {
            $this->model = suggest(
                label: 'Enter your Model name or file path',
                required: true,
                default: 'User',
                options: Models::list(),
            );

            $this->modelPath = explode('\\', $this->model);
            $this->modelName = strval(Arr::last($this->modelPath));

            if (count($this->modelPath) === 1) {
                if (file_exists('app/Models')) {
                    $this->model = 'App\\Models\\' . $this->model;
                } else {
                    $this->cleanModelName = strval(preg_replace('![^A-Z]+!', '', $this->model));

                    if (strlen($this->cleanModelName)) {
                        throw new CreateCommandException('Error: Could not process the informed Model name. Did you use quotes?<info> E.g. <comment>"\App\Models\ResourceModel"</comment></info>');
                    }
                }
            }

            if (!class_exists($this->model)) {
                throw new CreateCommandException('Error: Could not find "' . $this->model . '" class.');
            }
        }

        if (in_array(strtolower($this->datasourceOption), ['m', 'qb'])) {
            $this->useFillable = confirm('Create columns based on Model\'s fillable property?');

            if ($this->useFillable) {
                if (strtolower($this->datasourceOption) === 'qb') {
                    $this->askDataBaseTableName();

                    $this->stub = FillableTable::queryBuilder($this->dataBaseTableName, strval($this->option('template')));
                } else {
                    $this->stub = FillableTable::eloquentBuilder($this->model, $this->modelName, strval($this->option('template')));
                }
            }
        }

        $this->componentName = $this->tableName;
        $subFolder           = '';

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $this->tableName, $matches);

        if (!empty($matches)) {
            $this->componentName = end($matches);
            array_splice($matches, 2);
            /** @var array $matches */
            $subFolder = '\\' . str_replace(['.', '/', '\\\\'], '\\', end($matches));
        }

        $this->stub = str_replace('{{ livewireClassNamespace }}', strval(config('livewire.class_namespace')), $this->stub);
        $this->stub = str_replace('{{ subFolder }}', $subFolder, $this->stub);
        $this->stub = str_replace('{{ componentName }}', $this->componentName, $this->stub);

        if (strtolower($this->datasourceOption) === 'm') {
            $this->stub = str_replace('{{ modelName }}', $this->model, $this->stub);
            $this->stub = str_replace('{{ modelLastName }}', $this->modelName, $this->stub);
            $this->stub = str_replace('{{ modelLowerCase }}', Str::lower($this->modelName), $this->stub);
            $this->stub = str_replace('{{ modelKebabCase }}', Str::kebab($this->modelName), $this->stub);
        }

        if (strtolower($this->datasourceOption) === 'qb') {
            $this->stub = str_replace('{{ databaseTableName }}', $this->dataBaseTableName, $this->stub);
        }

        $livewirePath = str(strval(config('livewire.class_namespace')))
            ->replace('\\', '/')
            ->replace('App', '')
            ->append('/');

        $path = app_path($livewirePath . $this->tableName . '.php');

        $this->componentFilename = Str::of($path)->basename();
        $basePath                = Str::of($path)->replace($this->componentFilename, '');

        $this->savedAt = $livewirePath . $basePath->after($livewirePath);

        $namespace = collect(explode('.', str_replace(['/', '\\'], '.', $this->tableName)))
            ->map([Str::class, 'kebab'])
            ->implode('.');

        $this->componentName = Str::of($namespace)
            ->lower()
            ->replace('/', '-')
            ->replace('\\', '-')
            ->prepend('<livewire:')
            ->append('/>');

        File::ensureDirectoryExists($basePath);

        $createTable = true;

        if (File::exists($path)) {
            $confirmation = (bool) confirm('It seems that ' . $this->tableName . ' already exists. Would you like to overwrite it?');

            if ($confirmation === false) {
                $createTable = false;
            }
        }

        if ($createTable) {
            File::put($path, $this->stub);
        }
    }

    protected function showCreated(): void
    {
        info("⚡ <comment>" . $this->componentFilename . '</comment> was successfully created at [<comment>app' . $this->savedAt . '</comment>].');

        info("⚡ Your PowerGrid table can be now included with the tag: <comment>" . $this->componentName . '</comment>');

        info("⭐ <comment>" . self::thanks() . "</comment> Please consider <comment>starring</comment> our repository at <comment>https://github.com/Power-Components/livewire-powergrid</comment> ⭐\n");
    }

    protected function thanks(): string
    {
        return strval(str_replace(',', '!', strval(__('Thanks,'))));
    }

    protected function checkTailwindForms(): void
    {
        $tailwind = TailwindForm::check();

        if (!empty($tailwind)) {
            $this->components->info($tailwind);
        }
    }
}
