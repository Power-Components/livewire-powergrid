<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishCommand extends Command
{
    protected $signature = 'powergrid:publish {--type=job} {--force=}';

    protected $description = 'Publish table stub';

    public function handle()
    {
        if ($this->option('type') === 'job') {
            $file = file_get_contents(__DIR__ . '/../Jobs/ExportJob.php');
            $stub = str_replace('namespace PowerComponents\\LivewirePowerGrid\\Jobs', 'namespace App\Jobs', $file);

            if (!is_dir(app_path('Jobs'))) {
                (new Filesystem())->makeDirectory(app_path('Jobs'));
            }

            file_put_contents(app_path('Jobs') . '/ExportJob.php', $stub);

            $this->info('Job file published successfully.');

            return;
        }

        if (!is_dir($stubsPath = $this->laravel->basePath('stubs'))) {
            (new Filesystem())->makeDirectory($stubsPath);
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
