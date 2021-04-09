<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PowerGridCommand extends Command
{
    protected $signature = 'powergrid:create {--name= : name of class component}
    {--model= : model Class}
    {--publish : publish stubs file}
    {--template= : name of the file that will be used as a template}
    {--force : Overwrite any existing files}';

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
            $tableName = $this->option('name');

            if (empty($tableName)) {
                $this->error('Error: Table name is required.<info> E.g. --name="ResourceTable"</info>');
                exit;
            }

            $modelName = $this->option('model');

            if (empty($modelName)) {
                $this->error('Error: Model name is required.<info> E.g. --model="\App\Models\ResourceModel"</info>');
                exit;
            }

            $modelNameArr = explode('\\', $modelName);

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

                $stub = str_replace('{{ componentName }}', $this->option('name'), $stub);
                $stub = str_replace('{{ modelName }}', $modelName, $stub);
                $modelLastName = Arr::last($modelNameArr);
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
