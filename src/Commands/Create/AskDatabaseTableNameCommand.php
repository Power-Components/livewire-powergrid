<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Create;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\{error, info, suggest};

use PowerComponents\LivewirePowerGrid\Actions\DatabaseTables;

use PowerComponents\LivewirePowerGrid\Exceptions\InvalidtableNameException;

class AskDatabaseTableNameCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:ask-database-table-name';

    /** @var string */
    protected $description = 'Ask PowerGrid\'s database table name';

    /** @var bool */
    protected $hidden = true;

    protected string $tableName;

    /**
     *
     * @throws InvalidtableNameException
     */
    public function handle(): string
    {
        $exists = false;

        while (!$exists) {
            $this->tableName = suggest(
                label: "Select or enter your Database Table's name:",
                options: DatabaseTables::list(),
                required: true
            );

            $exists = Schema::hasTable($this->tableName);

            if (!$exists && !app()->runningUnitTests()) {
                error("The table [$this->tableName] does not exist! Try again or press Ctrl+C to abort.");
            }
        }

        return $this->tableName;
    }
}
