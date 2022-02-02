<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{File, Log, Schema};
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\Helpers\InteractsWithVersions;

class CreateCommand extends Command
{
    protected $signature = 'powergrid:create
    {--template= : name of the file that will be used as a template}';

    protected $description = 'Make a new PowerGrid table component.';

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->checkUpdates();

        $fillable  = false;
        $tableName = $this->ask('What is the name of your new ⚡ PowerGrid Table (E.g., <comment>UserTable</comment>)?');

        if (!is_string($tableName)) {
            throw new \Exception('Could not parse table name');
        }

        $tableName = str_replace(['.', '\\'], '/', (string) $tableName);

        if (empty(trim($tableName))) {
            $this->error('You must provide a name for your ⚡ PowerGrid Table!');

            return;
        }

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $tableName, $matches);

        if (!is_array($matches)) {
            throw new Exception('Could not parse model name');
        }

        $creationModel = $this->ask('Create Datasource with <comment>[M]</comment>odel or <comment>[C]</comment>ollection? (Default: Model)');

        if (!is_string($creationModel)) {
            throw new \Exception('Could not parse table name');
        }

        if (empty($creationModel)) {
            $creationModel = 'M';
        }

        if (!in_array(strtolower($creationModel), ['m', 'c'])) {
            $this->error('Please enter <comment>[M]</comment> for Model or <comment>[C]</comment> for Collection');

            return;
        }

        $stub = $this->getStubs($creationModel);

        $modelName     = '';
        $modelLastName = '';

        if (strtolower($creationModel) === 'm') {
            $modelName = $this->anticipate('Enter your Model name or file path (E.g., <comment>User</comment> or <comment>App\Models\User</comment>)', $this->listModels());

            if (empty($modelName)) {
                $this->error('Could not create, Model path is missing');
            }

            if (!is_string($modelName)) {
                throw new \Exception('Could not parse table name');
            }

            if (empty(trim($modelName))) {
                $this->error('Error: Model name is required.');

                return;
            }

            if ($this->confirm('Create columns based on Model\'s <comment>fillable</comment> property?')) {
                $fillable = true;
            }

            $modelNameArr  = explode('\\', $modelName);
            $modelLastName = Arr::last($modelNameArr);

            if (count($modelNameArr) === 1) {
                if (file_exists('app/Models')) {
                    $modelName = 'App\\Models\\' . $modelName;
                } else {
                    $cleanModelName = preg_replace('![^A-Z]+!', '', $modelName);

                    if (!is_string($cleanModelName)) {
                        throw new Exception('Could not parse model name');
                    }

                    if (strlen($cleanModelName)) {
                        $this->warn('Error: Could not process the informed Model name. Did you use quotes?<info> E.g. <comment>"\App\Models\ResourceModel"</comment></info>');

                        return;
                    }

                    $this->error('Error: "' . $modelName . '" Invalid model path.<info> Path must be like: <comment>"\App\Models\User"</comment></info>');

                    return;
                }
            }

            if ($fillable && is_string($modelLastName)) {
                $stub = $this->createFromFillable($modelName, $modelLastName);
            }
        }

        $componentName = $tableName;
        $subFolder     = '';

        if (!empty($matches)) {
            $componentName = end($matches);
            array_splice($matches, 2);
            $subFolder = '\\' . str_replace(['.', '/', '\\\\'], '\\', end($matches));
        }

        if (!is_string($componentName)) {
            throw new \Exception('Could not parse component name');
        }

        $stub = str_replace('{{ subFolder }}', $subFolder, $stub);
        $stub = str_replace('{{ componentName }}', $componentName, $stub);

        if (strtolower($creationModel) === 'm' && is_string($modelLastName)) {
            $stub = str_replace('{{ modelName }}', $modelName, $stub);
            $stub = str_replace('{{ modelLastName }}', $modelLastName, $stub);
            $stub = str_replace('{{ modelLowerCase }}', Str::lower($modelLastName), $stub);
            $stub = str_replace('{{ modelKebabCase }}', Str::kebab($modelLastName), $stub);
        }

        $livewirePath = 'Http/Livewire/';
        $path         = app_path($livewirePath . $tableName . '.php');

        $filename = Str::of($path)->basename();
        $basePath = Str::of($path)->replace($filename, '');

        $savedAt = $livewirePath . $basePath->after($livewirePath);

        $namespace = collect(explode('.', str_replace(['/', '\\'], '.', $tableName)))
            ->map([Str::class, 'kebab'])
            ->implode('.');

        $componentName = Str::of($namespace)
            ->lower()
            ->replace('/', '-')
            ->replace('\\', '-')
            ->prepend('<livewire:')
            ->append('/>');

        File::ensureDirectoryExists($basePath);

        $createTable = true;

        if (File::exists($path)) {
            $confirmation = (bool) $this->confirm('It seems that <comment>' . $tableName . '</comment> already exists. Would you like to overwrite it?');

            if ($confirmation === false) {
                $createTable = false;
            }
        }

        if ($createTable && is_string($stub)) {
            File::put($path, $stub);

            $this->checkTailwindForms();

            $this->info("\n⚡ <comment>" . $filename . '</comment> was successfully created at [<comment>App/' . $savedAt . '</comment>].');
            $this->info("\n⚡ Your PowerGrid can be now included with the tag: <comment>" . $componentName . "</comment>\n");
        }
    }

    protected function getStubs(string $creationModel): string
    {
        if (is_string($this->option('template')) && empty($this->option('template')) === false) {
            return File::get(base_path($this->option('template')));
        }
        if (strtolower($creationModel) === 'm') {
            return File::get(__DIR__ . '/../../resources/stubs/table.model.stub');
        }

        return File::get(__DIR__ . '/../../resources/stubs/table.collection.stub');
    }

    /**
     * @throws Exception
     */
    private function createFromFillable(string $modelName, string $modelLastName): string
    {
        $model = new $modelName();

        if ($model instanceof Model === false) {
            throw new \Exception('Invalid model given.');
        }

        $stub        = File::get(__DIR__ . '/../../resources/stubs/table.fillable.stub');

        $getFillable = $model->getFillable();

        if (filled($model->getKeyName())) {
            $getFillable = array_merge([$model->getKeyName()], $getFillable);
        }

        $getFillable = array_merge(
            $getFillable,
            ['created_at', 'updated_at']
        );

        $datasource = '';
        $columns    = "[\n";

        foreach ($getFillable as $field) {
            if (in_array($field, $model->getHidden())) {
                continue;
            }

            $conn = Schema::getConnection();

            $conn->getDoctrineSchemaManager()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');

            if (Schema::hasColumn($model->getTable(), $field)) {
                $column = $conn->getDoctrineColumn($model->getTable(), $field);

                $title = Str::of($field)->replace('_', ' ')->upper();

                if (in_array($column->getType()->getName(), ['datetime', 'date'])) {
                    $columns .= '            Column::add()' . "\n" . '                ->title(\'' . $title . '\')' . "\n" . '                ->field(\'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->searchable()' . "\n" . '                ->sortable()' . "\n" . '                ->makeInputDatePicker(\'' . $field . '\'),' . "\n\n";
                }

                if ($column->getType()->getName() === 'datetime') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', function(' . $modelLastName . ' $model) { ' . "\n" . '                return Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\');' . "\n" . '            })';

                    continue;
                }

                if ($column->getType()->getName() === 'date') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', function(' . $modelLastName . ' $model) { ' . "\n" . '                return Carbon::parse($model->' . $field . ')->format(\'d/m/Y\');' . "\n" . '            })';

                    continue;
                }

                if ($column->getType()->getName() === 'boolean') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns    .= '            Column::add()' . "\n" . '                ->title(\'' . $title . '\')' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";

                    continue;
                }

                if (in_array($column->getType()->getName(), ['smallint', 'integer', 'bigint'])) {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns    .= '            Column::add()' . "\n" . '                ->title(\'' . $title . '\')' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->makeInputRange(),' . "\n\n";

                    continue;
                }

                if ($column->getType()->getName() === 'string') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns    .= '            Column::add()' . "\n" . '                ->title(\'' . $title . '\')' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable()' . "\n" . '                ->makeInputText(),' . "\n\n";

                    continue;
                }

                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(\'' . $title . '\')' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
            }
        }

        $columns .= "        ]\n";

        $stub = str_replace('{{ datasource }}', $datasource, $stub);

        return str_replace('{{ columns }}', $columns, $stub);
    }

    /**
     * List files in Models folder
     *
     * @return array
     */
    private function listModels(): array
    {
        $modelsFolder = app_path('Models');

        $files = collect(File::allFiles($modelsFolder))
            ->map(fn ($file) => $file->getFilenameWithoutExtension());

        $files->map(function ($file) use (&$files) {
            $files->push('App\\Models\\' . $file);
        });

        return  $files->toArray();
    }

    private function checkTailwindForms(): void
    {
        $tailwindConfigFile = base_path() . '/' . 'tailwind.config.js';

        if (File::exists($tailwindConfigFile)) {
            $fileContent = File::get($tailwindConfigFile);

            if (Str::contains($fileContent, "require('@tailwindcss/forms')") === true) {
                $this->info("\n💡 It seems you are using the plugin <comment>Tailwindcss/form</comment>.\n   Please check: <comment>https://livewire-powergrid.com/#/get-started/configure?id=_43-tailwind-forms</comment> for more information.");
            }
        }
    }

    private function checkUpdates(): void
    {
        if (config('livewire-powergrid.check_version') === true) {
            $ensureLatestVersion = new InteractsWithVersions();

            try {
                $current = $ensureLatestVersion->ensureLatestVersion();

                if (isset($current['version'])) {
                    if (version_compare($remote = $ensureLatestVersion->getLatestVersion(), $current['version']) > 0) {
                        $this->info(" You are using an outdated version <comment>{$current['version']}</comment> of PowerGrid ⚡. Please update to <comment>{$remote}</comment>");
                        $this->info(" Released Date: <comment>{$current['release']}</comment>");
                    }
                }
            } catch (Exception $e) {
                Log::debug($e->getMessage());
            }
        }
    }
}
