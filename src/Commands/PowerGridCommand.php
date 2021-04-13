<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PowerGridCommand extends Command
{
    protected $signature = 'powergrid:create
    {name=default : name of class component}
    {--model= : model Class}
    {--publish : publish stubs file}
    {--template= : name of the file that will be used as a template}
    {--force : Overwrite any existing files}
    {--fillable : Generate data from fillable}';

    protected $description = 'Make a new Laravel Livewire table component.';

    public function handle()
    {
        if ($this->option('publish')) {
            if (!is_dir($stubsPath = $this->laravel->basePath('stubs'))) {
                (new Filesystem)->makeDirectory($stubsPath);
            }

            $files = [
                __DIR__ . '/../../resources/stubs/table.stub' => $stubsPath . '/table.stub',
            ];

            foreach ($files as $from => $to) {
                if (!file_exists($to) || $this->option('force')) {
                    file_put_contents($to, file_get_contents($from));
                }
            }

            $this->info('Stubs published successfully.');
        } else {

            $tableName = $this->argument('name');
            $modelName = $this->option('model');
            $fillable  = $this->option('fillable');

            if ($tableName === 'default') {
                $this->error('Error: Table name is required.<info> E.g. powergrid:create UserTable"</info>');
                exit;
            }

            if (empty($modelName)) {
                $example = '\\App\\Models\\'.$tableName;
                $this->error('Error: Model name is required.<info> E.g. powergrid:create '.$tableName.' --model="'.$example.'"</info>');
                exit;
            }

            $modelNameArr = explode('\\', $modelName);
            $modelLastName = Arr::last($modelNameArr);

            if (count($modelNameArr) == 1) {

                if (strlen(preg_replace('![^A-Z]+!', '', $modelName))) {
                    $this->warn('Error: Could not process the informed Model name. Did you use quotes?<info> E.g. --model="\App\Models\ResourceModel"</info>');
                    exit;
                }
                $this->error('Error: Model name is required.<info> E.g. --model="\App\Models\ResourceModel"</info>');
                exit;
            }

            if (!empty($modelName)) {

                if (!empty($this->option('template'))) {
                    $stub = File::get(base_path($this->option('template')));
                } else {
                    $stub = File::get(__DIR__ . '/../../resources/stubs/table.stub');
                }

                if ($fillable) {

                    $stub = File::get(__DIR__ . '/../../resources/stubs/table.fillable.stub');

                    $model = new $modelName();
                    $fillable = array_merge([$model->getKeyName()], $model->getFillable());
                    $fillable = array_merge($fillable, ['created_at', 'updated_at']);

                    $dataSource = "";
                    $columns = "[\n";
                    foreach ($fillable as $field) {
                        if (!in_array($field, $model->getHidden())) {

                            $type = Arr::first(Arr::where(DB::select('describe '.$model->getTable()), function ($info) use ($field) {
                                return ($info->Field === $field) ? $info->Type: '';
                            }))->Type;

                            if (in_array($type, ['timestamp', 'datetime'])) {
                                $dataSource .= "\n".'            ->addColumn(\''.$field.'\')';
                                $dataSource .= "\n".'            ->addColumn(\''.$field.'_format\', function('.$modelLastName.' $model) { '."\n".'                return Carbon::parse($model->'.$field.')->format(\'d/m/Y H:i:s\');'."\n".'            })';

                                $columns    .= '            Column::add()'."\n".'                ->title(__(\''.Str::camel($field.'_format').'\'))'."\n".'                ->field(\''.$field.'\')'."\n".'                ->hidden(),'."\n";
                                $columns    .= '            Column::add()'."\n".'                ->title(__(\''.Str::camel($field.'_format').'\'))'."\n".'                ->field(\''.$field.'_format\')'."\n".'                ->searchable()'."\n".'                ->sortable()'."\n".'                ->makeInputDatePicker(\''.$field.'\'),'."\n";
                            } else {

                                $dataSource .= "\n".'            ->addColumn(\''.$field.'\')';
                                $columns    .= '            Column::add()'."\n".'                ->title(__(\''.Str::camel($field.'').'\'))'."\n".'                ->field(\''.$field.'\')'."\n".'                ->sortable()'."\n".'                ->searchable(),'."\n";
                            }
                        }
                    }

                    $columns .= "        ]\n";

                    $stub = str_replace('{{ dataSource }}', $dataSource, $stub);
                    $stub = str_replace('{{ columns }}', $columns, $stub);

                }

                $stub = str_replace('{{ componentName }}', $tableName, $stub);
                $stub = str_replace('{{ modelName }}', $modelName, $stub);
                $stub = str_replace('{{ modelLastName }}', $modelLastName, $stub);
                $stub = str_replace('{{ modelLowerCase }}', Str::lower($modelLastName), $stub);
                $stub = str_replace('{{ modelKebabCase }}', Str::kebab($modelLastName), $stub);

            } else {

                $this->error('Could not create, Model path is missing');
                exit;
            }

            $create_at = 'Http/Livewire/' . $tableName . '.php';

            $path = app_path($create_at);

            File::ensureDirectoryExists(app_path('Http/Livewire'));

            if (!File::exists($path) || $this->confirm('It seems <comment>' . $tableName . '</comment> already exists. Overwrite it?')) {
                File::put($path, $stub);
                $this->info('<comment>' . $tableName . '</comment> was successfully created at [<comment>App/' . $create_at . '</comment>]');
            }
        }
    }
}
