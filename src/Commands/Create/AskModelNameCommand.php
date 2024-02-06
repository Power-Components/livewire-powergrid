<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Create;

use Illuminate\Console\Command;

use function Laravel\Prompts\{error, suggest};

use PowerComponents\LivewirePowerGrid\Actions\ListModels;

class AskModelNameCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:ask-model-name';

    /** @var string */
    protected $description = 'Ask PowerGrid\'s component model name';

    /** @var bool */
    protected $hidden = true;

    protected string $model = '';

    protected string $fqn = '';

    /**
     * @return array{model: string, fqn: string}
     */
    public function handle(): array
    {
        while ($this->model === '') {
            $this->setModel(suggest(
                label: 'Enter your Model name',
                options: ListModels::handle(),
                default: 'User',
                required: true,
            ))
            ->parseFqn()
            ->checkIfModelExists();
        }

        return ['model' => $this->model, 'fqn' => $this->fqn];
    }

    private function setModel(string $model): self
    {
        $this->model = str($model)->replaceMatches('#[^A-Za-z0-9\\\\]#', '')->toString();

        return $this;
    }

    private function parseFqn(): self
    {
        $this->fqn = 'App\\Models\\' . $this->model;

        if (str_contains($this->model, '\\')) {
            $this->fqn   = $this->model;
            $this->model = str($this->fqn)->rtrim('\\')->afterLast('\\');
        }

        return $this;
    }

    private function checkIfModelExists(): self
    {
        if (!class_exists($this->fqn)) {
            error("Could not find [{$this->fqn}] class. Try again or press Ctrl+C to abort.");

            $this->model = '';

            $this->fqn = '';
        }

        return $this;
    }
}
