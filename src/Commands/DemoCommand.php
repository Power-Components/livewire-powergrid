<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class DemoCommand extends Command
{
    protected $signature = 'powergrid:demo';

    protected $description = 'Generate a PowerGrid demo Table.';
    
    protected $stubPath = __DIR__ . '/../../resources/stubs/';

    public function handle()
    {
        $tableFileName = 'PowerGridDemoTable';
        $viewFileName  = 'powergrid-demo.blade';

        $livewirePath = 'Http/Livewire/';
        $fullLivewirePath = app_path($livewirePath);

        $viewFolder = 'resources/views';

        if (!is_dir($fullLivewirePath)) {
            (new Filesystem)->makeDirectory($fullLivewirePath);
        }

        file_put_contents($fullLivewirePath . $tableFileName. '.php', file_get_contents($this->stubPath . $tableFileName . '.stub'));
        file_put_contents($this->laravel->basePath($viewFolder) . '/' . $viewFileName. '.php', file_get_contents($this->stubPath . $viewFileName . '.stub'));
        
        $this->info('⚡ *** PowerGrid Demo Table is ready! ***');
        $this->info("\n⚡ <comment>{$tableFileName}.php</comment> was successfully created at [<comment>App/{$livewirePath}</comment>]");
        $this->info("\n⚡ <comment>{$viewFileName}.php</comment> was successfully created at [<comment>{$viewFolder}/</comment>]");
        $this->info("\n⚡ *** Usage ***");
        $this->info("\n<comment>➤</comment> You must include <comment>Route::view('/powergrid', 'powergrid-demo');</comment> in <comment>routes/web.php</comment> file.");
        $this->info("\n<comment>➤</comment> Visit <comment>http://your-app/powergrid</comment>. Enjoy it!\n");
    }
}
