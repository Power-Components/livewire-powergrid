<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Create;

use Illuminate\Console\Command;

use function Laravel\Prompts\select;

use PowerComponents\LivewirePowerGrid\Enums\DataSources;

class ChooseDatasourceCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:choose-datasource';

    /** @var string */
    protected $description = 'Choose PowerGrid\'s component datasource';

    /** @var bool */
    protected $hidden = true;

    public function handle(): string
    {
        return strval(select(
            label: 'Select your preferred Data source:',
            options: DataSources::collect(),
            default: DataSources::ELOQUENT_BUILDER->value
        ));
    }
}
