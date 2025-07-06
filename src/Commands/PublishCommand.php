<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

/** @codeCoverageIgnore */
class PublishCommand extends Command
{
    protected $signature = 'powergrid:publish {--type=job} {--force=}';

    protected $description = 'Publish table stub';

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        if ($this->option('type') === 'job') {
            $exportJobFile = __DIR__ . '/../Jobs/ExportJob.php';

            if (File::exists($exportJobFile) === false && File::isReadable($exportJobFile) === true) {
                throw new Exception('ExportJob.php not found.');
            }

            $file = (string) file_get_contents($exportJobFile);
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
