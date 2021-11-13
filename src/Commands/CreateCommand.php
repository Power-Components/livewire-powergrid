<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{File, Schema};
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\Helpers\InteractsWithVersions;

class CreateCommand extends Command
{
    protected $signature = 'powergrid:create
    {--template= : name of the file that will be used as a template}';

    protected $description = 'Make a new PowerGrid table component.';

    public function handle(): void
    {
        if (config('livewire-powergrid.check_version') === true) {
            $ensureLatestVersion = new InteractsWithVersions();
            $current             = $ensureLatestVersion->ensureLatestVersion();

            if (isset($current['version'])) {
                if (version_compare($remote = $ensureLatestVersion->getLatestVersion(), $current['version']) > 0) {
                    $this->info(" You are using an outdated version <comment>{$current['version']}</comment> of PowerGrid ⚡. Please update to <comment>{$remote}</comment>");
                    $this->info(" Released Date: <comment>{$current['release']}</comment>");
                }
            }
        }

        $fillable        = false;
        $tableName       = $this->ask('What is the name of your new ⚡ PowerGrid Table (E.g., <comment>UserTable</comment>)?');
        $tableName       = str_replace(['.', '\\'], '/', $tableName);

        if (empty(trim($tableName))) {
            $this->error('You must provide a name for your ⚡ PowerGrid Table!');

            return;
        }

        $creationModel   = $this->ask('Create Datasource with <comment>[M]</comment>odel or <comment>[C]</comment>ollection? (Default: Model)');

        if (empty($creationModel)) {
            $creationModel = 'M';
        }

        if (!in_array(strtolower($creationModel), ['m', 'c'])) {
            $this->error('Please enter <comment>[M]</comment> for Model or <comment>[C]</comment> for Collection');

            return;
        }

        $modelName = $this->ask('Enter your Model path (E.g., <comment>App\Models\User</comment>)');

        if (empty(trim($modelName))) {
            $this->error('Error: Model name is required.');

            return;
        }

        if ($this->confirm('Create columns based on Model\'s <comment>fillable</comment> property?')) {
            $fillable   = true;
        }

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $tableName, $matches);

        $modelNameArr  = explode('\\', $modelName);
        $modelLastName = Arr::last($modelNameArr);

        if (count($modelNameArr) === 1) {
            if (strlen(preg_replace('![^A-Z]+!', '', $modelName))) {
                $this->warn('Error: Could not process the informed Model name. Did you use quotes?<info> E.g. <comment>"\App\Models\ResourceModel"</comment></info>');

                return;
            }

            $this->error('Error: "' . $modelName . '" Invalid model path.<info> Path must be like: <comment>"\App\Models\User"</comment></info>');

            return;
        }

        if (empty($modelName)) {
            $this->error('Could not create, Model path is missing');

            return;
        }

        $stub = $this->getStubs($creationModel);

        if ($fillable) {
            $stub     = $this->createFromFillable($modelName, $modelLastName);
        }

        $componentName   = $tableName;
        $subFolder       = null;

        if (!empty($matches)) {
            $componentName = end($matches);
            array_splice($matches, 2);
            $subFolder = '\\' . str_replace(['.', '/', '\\\\'], '\\', end($matches));
        }

        $stub = str_replace('{{ subFolder }}', $subFolder, $stub);
        $stub = str_replace('{{ componentName }}', $componentName, $stub);
        $stub = str_replace('{{ modelName }}', $modelName, $stub);
        $stub = str_replace('{{ modelLastName }}', $modelLastName, $stub);
        $stub = str_replace('{{ modelLowerCase }}', Str::lower($modelLastName), $stub);
        $stub = str_replace('{{ modelKebabCase }}', Str::kebab($modelLastName), $stub);

        $livewirePath  = 'Http/Livewire/';
        $path          = app_path($livewirePath . $tableName . '.php');

        $filename  = Str::of($path)->basename();
        $basePath  = Str::of($path)->replace($filename, '');

        $savedAt   = $livewirePath . $basePath->after($livewirePath);

        $component_name = Str::of($tableName)
            ->lower()
            ->kebab()
            ->replace('/', '-')
            ->replace('\\', '-')
            ->replace('table', '-table')
            ->prepend('<livewire:')
            ->append('/>');

        File::ensureDirectoryExists($basePath);

        $createTable = true;

        if (File::exists($path)) {
            $confirmation = $this->confirm('It seems that <comment>' . $tableName . '</comment> already exists. Would you like to overwrite it?');

            if (strtolower($confirmation) !== 'yes') {
                $createTable = false;
            }
        }

        if ($createTable) {
            File::put($path, $stub);

            $this->checkTailwindForms();

            $this->info("\n⚡ <comment>" . $filename . '</comment> was successfully created at [<comment>App/' . $savedAt . '</comment>].');
            $this->info("\n⚡ Your PowerGrid can be now included with the tag: <comment>" . $component_name . "</comment>\n");
        }
    }

    private function createFromFillable(string $modelName, string $modelLastName)
    {
        $model          = new $modelName();
        $stub           = File::get(__DIR__ . '/../../resources/stubs/table.fillable.stub');
        $getFillable    = array_merge(
            [$model->getKeyName()],
            $model->getFillable(),
            ['created_at', 'updated_at']
        );

        $datasource     = '';
        $columns        = "[\n";

        foreach ($getFillable as $field) {
            if (in_array($field, $model->getHidden())) {
                continue;
            }

            $column = Schema::getConnection()->getDoctrineColumn($model->getTable(), $field);

            $title = Str::of($field)->replace('_', ' ')->upper();

            if ($column->getType()->getName() === 'datetime') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', function(' . $modelLastName . ' $model) { ' . "\n" . '                return Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\');' . "\n" . '            })';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '_formatted\')' . "\n" . '                ->searchable()' . "\n" . '                ->sortable()' . "\n" . '                ->makeInputDatePicker(\'' . $field . '\'),' . "\n\n";

                continue;
            }

            if ($column->getType()->getName() === 'date') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', function(' . $modelLastName . ' $model) { ' . "\n" . '                return Carbon::parse($model->' . $field . ')->format(\'d/m/Y\');' . "\n" . '            })';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '_formatted\')' . "\n" . '                ->searchable()' . "\n" . '                ->sortable()' . "\n" . '                ->makeInputDatePicker(\'' . $field . '\'),' . "\n\n";

                continue;
            }

            if ($column->getType()->getName() === 'boolean') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";

                continue;
            }

            if (in_array($column->getType()->getName(), ['smallint', 'integer', 'bigint'])) {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->makeInputRange(),' . "\n\n";

                continue;
            }

            if ($column->getType()->getName() === 'string') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable()' . "\n" . '                ->makeInputText(),' . "\n\n";

                continue;
            }

            $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
            $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
        }

        $columns .= "        ]\n";

        $stub = str_replace('{{ datasource }}', $datasource, $stub);

        return str_replace('{{ columns }}', $columns, $stub);
    }

    protected function getStubs($creationModel): string
    {
        if (!empty($this->option('template'))) {
            return File::get(base_path($this->option('template')));
        }
        if (strtolower($creationModel) === 'm') {
            return File::get(__DIR__ . '/../../resources/stubs/table.model.stub');
        }

        return File::get(__DIR__ . '/../../resources/stubs/table.stub');
    }

    protected function checkTailwindForms(): void
    {
        $tailwindConfigFile = base_path() . '/' . 'tailwind.config.js';

        if (File::exists($tailwindConfigFile)) {
            $fileContent    = File::get($tailwindConfigFile);

            if (Str::contains($fileContent, "require('@tailwindcss/forms')") === true) {
                $this->info("\n💡 It seems you are using the plugin <comment>Tailwindcss/form</comment>.\n   Please check: <comment>https://livewire-powergrid.docsforge.com/main/configure/#43-tailwind-forms</comment> for more information.");
            }
        }
    }
}
