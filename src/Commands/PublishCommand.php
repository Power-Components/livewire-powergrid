<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishCommand extends Command
{
    protected $signature = 'powergrid:publish {--force=}';

    protected $description = 'Make a new PowerGrid table component.';

    public function handle()
    {
        if (!is_dir($stubsPath = $this->laravel->basePath('stubs'))) {
            (new Filesystem)->makeDirectory($stubsPath);
        }

        $files = [
            __DIR__ . '/../../resources/stubs/table.model.stub' => $stubsPath . '/table.model.stub',
        ];

        foreach ($files as $from => $to) {
            if (!file_exists($to) || $this->option('force')) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->info('Stubs published successfully.');

    }
}
