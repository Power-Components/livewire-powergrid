<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateCommand extends Command
{
    protected $signature = 'powergrid:create
    {--template= : name of the file that will be used as a template}';

    protected $description = 'Make a new PowerGrid table component.';

    public function handle()
    {
        $tableName = $this->ask('Component Name');
        $tableName = str_replace(['.', '\\'], '/', $tableName);

        $modelName  = $this->ask('Model (ex: "App\Models\User")');
        $fillable   = false;
        $collection = false;

        if ($this->confirm('Use the based on fillable ?')) {
            $fillable   = true;
        }

        if ($this->confirm('Will you use a collection? (default: Model)')) {
            $collection   = true;
        }

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $tableName, $matches);

        if ($tableName === 'default') {
            $this->error('Error: Table name is required.<info> E.g. powergrid:create UserTable"</info>');

            return;
        }

        if (empty($modelName)) {
            $example = '\\App\\Models\\' . $tableName;
            $this->error('Error: Model name is required.<info> E.g. powergrid:create ' . $tableName . ' --model="' . $example . '"</info>');

            return;
        }

        $modelNameArr  = explode('\\', $modelName);
        $modelLastName = Arr::last($modelNameArr);

        if (count($modelNameArr) === 1) {
            if (strlen(preg_replace('![^A-Z]+!', '', $modelName))) {
                $this->warn('Error: Could not process the informed Model name. Did you use quotes?<info> E.g. --model="\App\Models\ResourceModel"</info>');

                return;
            }
            $this->error('Error: Model name is required.<info> E.g. --model="\App\Models\ResourceModel"</info>');

            return;
        }

        if (empty($modelName)) {
            $this->error('Could not create, Model path is missing');
            exit;
        }
        $stub = $this->getStubs($collection);

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

        if (!File::exists($path) || $this->confirm('It seems <comment>' . $tableName . '</comment> already exists. Overwrite it?')) {
            File::put($path, $stub);
            $this->info("\n⚡ <comment>" . $filename . '</comment> was successfully created at [<comment>App/' . $savedAt . '</comment>].');
            $this->info("\n⚡ Your PowerGrid can be now included with: <comment>" . $component_name . "</comment>\n");
        }
    }

    private function createFromFillable(string $modelName, string $modelLastName)
    {
        $model          = new $modelName();
        $stub           = File::get(__DIR__ . '/../../resources/stubs/table.fillable.stub');
        $getFillable    = array_merge([$model->getKeyName()], $model->getFillable());
        $getFillable    = array_merge($getFillable, ['created_at', 'updated_at']);

        $dataSource     = "";
        $columns        = "[\n";

        foreach ($getFillable as $field) {
            if (in_array($field, $model->getHidden())) {
                continue;
            }
            $type = Arr::first(Arr::where(DB::select('describe ' . $model->getTable()), function ($info) use ($field) {
                return ($info->Field === $field) ? $info->Type : '';
            }))->Type;

            $title = Str::of($field)->replace('_', ' ')->upper();

            if (in_array($type, ['timestamp', 'datetime'])) {
                $dataSource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', function(' . $modelLastName . ' $model) { ' . "\n" . '                return Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\');' . "\n" . '            })';

                $columns .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '_formatted\')' . "\n" . '                ->searchable()' . "\n" . '                ->sortable()' . "\n" . '                ->makeInputDatePicker(\'' . $field . '\'),' . "\n\n";
            }

            if ($type === 'tinyint(1)') {
                $dataSource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";

                continue;
            }
            if ($type === 'int(11)') {
                $dataSource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->makeInputRange(),' . "\n\n";

                continue;
            }
            if (!in_array($type, ['timestamp', 'datetime'])) {
                $dataSource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::add()' . "\n" . '                ->title(__(\'' . $title . '\'))' . "\n" . '                ->field(\'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
            }
        }

        $columns .= "        ]\n";

        $stub = str_replace('{{ dataSource }}', $dataSource, $stub);

        return str_replace('{{ columns }}', $columns, $stub);
    }

    protected function getStubs($collection = null)
    {
        if (!empty($this->option('template'))) {
            return File::get(base_path($this->option('template')));
        }
        if ($collection) {
            return File::get(__DIR__ . '/../../resources/stubs/table.stub');
        }

        return File::get(__DIR__ . '/../../resources/stubs/table.model.stub');
    }
}
