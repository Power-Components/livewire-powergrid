<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{File};
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\Commands\Actions\{FillableTable, Models, Stubs, TailwindForm};
use PowerComponents\LivewirePowerGrid\Commands\Concerns\RenderAscii;
use PowerComponents\LivewirePowerGrid\Exceptions\CreateCommandException;

class CreateCommand extends Command
{
    use RenderAscii;

    /** @var string */
    protected $signature = 'powergrid:create {--template= : name of the file that will be used as a template}';

    /** @var string */
    protected $description = 'Make a new PowerGrid table component.';

    protected string $tableName;

    protected bool $useFilable = false;

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
        $this->tableName = strval($this->ask('What is the name of your Table Component? (E.g., <comment>UserTable</comment>)', 'PowerGridTable'));

        if (empty(trim(strval($this->tableName)))) {
            throw new CreateCommandException('You must provide a name for your ⚡ PowerGrid Table!');
        }

        $this->tableName = str_replace(['.', '\\'], '/', (string) $this->tableName);

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $this->tableName, $matches);

        if (!is_array($matches)) {
            throw new CreateCommandException('Could not parse model name');
        }
    }

    /**
     * @throws CreateCommandException
     */
    protected function askDatasource(): void
    {
        $this->datasourceOption =  strval($this->ask('Create Datasource with <comment>[M]</comment>odel or <comment>[C]</comment>ollection? (Default: Model)', 'M'));

        if (!in_array(strtolower(strval($this->datasourceOption)), ['m', 'c'])) {
            throw new CreateCommandException('Invalid option. Please enter [M] for model or [C] for Collection.');
        }
    }

    /**
     * @throws CreateCommandException
     */
    protected function askModel(): void
    {
        $this->stub = Stubs::load($this->datasourceOption, strval($this->option('template')));

        if (strtolower($this->datasourceOption) === 'm') {
            $this->model = strval($this->anticipate('Enter your Model name or file path (E.g., <comment>User</comment> or <comment>App\Models\User</comment>)', Models::list(), 'User'));

            if (empty($this->model)) {
                throw new CreateCommandException('Error: You must inform the Model name or file path.');
            }

            $this->modelPath  = explode('\\', $this->model);
            $this->modelName  = strval(Arr::last($this->modelPath));

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

            if ($this->confirm('Create columns based on Model\'s <comment>fillable</comment> property?')) {
                $this->useFilable = true;
            }

            if ($this->useFilable) {
                $this->stub = FillableTable::create($this->model, $this->modelName, strval($this->option('template')));
            }
        }

        $this->componentName = $this->tableName;
        $subFolder           = '';

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $this->tableName, $matches);

        if (!empty($matches)) {
            $this->componentName = end($matches);
            array_splice($matches, 2);
            $subFolder = '\\' . str_replace(['.', '/', '\\\\'], '\\', end($matches));
        }

        $this->stub = str_replace('{{ subFolder }}', $subFolder, $this->stub);
        $this->stub = str_replace('{{ componentName }}', $this->componentName, $this->stub);

        if (strtolower($this->datasourceOption) === 'm') {
            $this->stub = str_replace('{{ modelName }}', $this->model, $this->stub);
            $this->stub = str_replace('{{ modelLastName }}', $this->modelName, $this->stub);
            $this->stub = str_replace('{{ modelLowerCase }}', Str::lower($this->modelName), $this->stub);
            $this->stub = str_replace('{{ modelKebabCase }}', Str::kebab($this->modelName), $this->stub);
        }

        $livewirePath = 'Http/Livewire/';
        $path         = app_path($livewirePath . $this->tableName . '.php');

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
            $confirmation = (bool) $this->confirm('It seems that <comment>' . $this->tableName . '</comment> already exists. Would you like to overwrite it?');

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
        $this->output->title('Component is ready!');

        $this->info("\n⚡ <comment>" . $this->componentFilename . '</comment> was successfully created at [<comment>App/' . $this->savedAt . '</comment>].');

        $this->info("\n⚡ Your PowerGrid table can be now included with the tag: <comment>" . $this->componentName . '</comment>');

        $this->info("\n\n⭐ <comment>" . self::thanks() . "</comment> Please consider <comment>starring</comment> our repository at <comment>https://github.com/Power-Components/livewire-powergrid</comment> ⭐\n");
    }

    protected function thanks(): string
    {
        return strval(str_replace(',', '!', strval(__('Thanks,'))));
    }

    protected function checkTailwindForms(): void
    {
        $tailwind = TailwindForm::check();

        if (!empty($tailwind)) {
            $this->info($tailwind);
        }
    }
}
