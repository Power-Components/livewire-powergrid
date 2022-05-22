<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class DemoCommand extends Command
{
    protected $signature = 'powergrid:demo';

    protected $description = 'Generate a PowerGrid demo Table.';

    protected string $stubPath = __DIR__ . '/../../resources/stubs/';

    protected string $tableFileName;

    protected string $viewFileName;

    protected string $livewirePath;

    protected string $fullLivewirePath;

    protected string $viewFolder;

    protected string $link = 'https://github.com/Power-Components/livewire-powergrid';

    /**
     * @throws \Exception
     */
    public function handle(): int
    {
        $this->tableFileName = 'PowerGridDemoTable';
        $this->viewFileName  = 'powergrid-demo.blade';

        $this->livewirePath      = 'Http/Livewire/';
        $this->fullLivewirePath  = app_path($this->livewirePath);

        $this->viewFolder  = 'resources/views';

        if (!is_dir($this->fullLivewirePath)) {
            (new Filesystem())->makeDirectory($this->fullLivewirePath);
        }

        $url = config('app.url');

        if (!is_string($url) || empty($url)) {
            throw new \Exception('Config URL invalid or not set.');
        }

        $stub = str_replace('{{ url }}', $url, $this->stubPath);

        file_put_contents($this->fullLivewirePath . $this->tableFileName . '.php', file_get_contents($stub . $this->tableFileName . '.stub'));
        file_put_contents($this->laravel->basePath($this->viewFolder) . '/' . $this->viewFileName . '.php', file_get_contents($stub . $this->viewFileName . '.stub'));

        $this->welcomeBox();

        $this->instructions();

        return self::SUCCESS;
    }

    protected function instructions(): void
    {
        $this->info(
            "<comment>➤</comment> <comment>{$this->tableFileName}.php</comment> was successfully created at [<comment>App/{$this->livewirePath }</comment>]\n"
        );

        $this->info("<comment>➤</comment> <comment>{$this->viewFileName}.php</comment> was successfully created at [<comment>{$this->viewFolder }/</comment>]\n");

        $this->output->newLine();

        $this->output->title('<info>Next steps</info>');

        $this->info("\n<comment>1.</comment> You must include <comment>Route::view('/powergrid', 'powergrid-demo');</comment> in your <comment>routes/web.php</comment> file.");
        $this->info("\n<comment>2.</comment> Serve your project. For example, run <comment>php artisan serve</comment>.");
        $this->info("\n<comment>3.</comment> Visit <comment>" . config('app.url') . '/powergrid</comment> to view the Demo Table.');
        $this->info("\n\n⭐ <comment>" . self::thanks() . "</comment> Please consider <comment>starring</comment> our repository at <href={$this->link}>{$this->link}</> ⭐\n");
    }

    protected function thanks(): string
    {
        return strval(str_replace(',', '!', strval(__('Thanks,'))));
    }

    protected function welcomeBox(): void
    {
        $this->output->newLine();

        $infoStyle = new OutputFormatterStyle('black', 'green');

        $this->output->getFormatter()->setStyle('msg', $infoStyle);

        $message =  $this->getHelper('formatter')->formatBlock(['', '⚡ Welcome to PowerGrid! ⚡', ''], 'msg', true);
        $this->output->writeln($message);

        $this->output->newLine(2);
    }
}
