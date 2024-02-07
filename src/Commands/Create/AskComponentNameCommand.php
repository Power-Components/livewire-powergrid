<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Create;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;

use PowerComponents\LivewirePowerGrid\Actions\SanitizeComponentName;

use function Laravel\Prompts\{confirm, text};

class AskComponentNameCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:ask-component-name';

    /** @var string */
    protected $description = 'Ask PowerGrid\'s component table name';

    /** @var bool */
    protected $hidden = true;

    protected string $componentName = '';

    public function handle(): string
    {
        while ($this->componentName === '') {
            $this->componentName = SanitizeComponentName::handle(
                text(
                    label: 'Enter a name for your new PowerGrid Component:',
                    placeholder: 'UserTable',
                    default: 'UserTable',
                    required: true
                )
            );

            $this->checkIfComponentAlreadyExists();
        }

        return $this->componentName;
    }

    private function checkIfComponentAlreadyExists(): void
    {
        if (File::exists(powergrid_components_path($this->componentName . '.php'))) {
            $confirmation = (bool) confirm("Component [{$this->componentName}] already exists. Overwrite it?");

            if ($confirmation === false) {
                $this->componentName = '';
            }
        }
    }
}
