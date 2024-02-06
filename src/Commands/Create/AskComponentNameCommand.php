<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Create;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;

use function Laravel\Prompts\{confirm, text};

use PowerComponents\LivewirePowerGrid\Exceptions\InvalidTableNameException;

class AskComponentNameCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:ask-component-name';

    /** @var string */
    protected $description = 'Ask PowerGrid\'s component table name';

    /** @var bool */
    protected $hidden = true;

    protected string $componentName = '';

    /**
     *
     * @throws InvalidTableNameException
     */
    public function handle(): string
    {
        while ($this->componentName === '') {
            $this->componentName = text(
                label: 'Enter a name for your new PowerGrid Component:',
                placeholder: 'UserTable',
                default: 'UserTable',
                required: true
            );

            $this->validate();
        }

        return $this->componentName;
    }

    private function validate(): void
    {
        $this->componentName = str_replace(['.', '\\'], '/', (string) $this->componentName);

        preg_match('/(.*)(\/|\.|\\\\)(.*)/', $this->componentName, $matches);

        if (!is_array($matches)) {
            InvalidTableNameException::throw("Could not parse the table name [$this->componentName]");
        }

        $this->checkIfComponentExists();
    }

    private function checkIfComponentExists(): void
    {
        if (File::exists(powergrid_components_path($this->componentName . '.php'))) {
            $confirmation = (bool) confirm("Component [{$this->componentName}] already exists. Overwrite it?");

            if ($confirmation === false) {
                $this->componentName = '';
            }
        }
    }
}
