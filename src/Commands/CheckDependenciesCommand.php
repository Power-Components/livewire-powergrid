<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\{note,select};

use PowerComponents\LivewirePowerGrid\Actions\{CheckDependencyFlatPick, CheckDependencyOpenspout};

class CheckDependenciesCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:check-dependencies';

    /** @var string */
    protected $description = 'Check PowerGrid dependencies.';

    public function handle(): int
    {
        $this->check([
            CheckDependencyFlatPick::handle(),
            CheckDependencyOpenspout::handle(),
        ]);

        return self::SUCCESS;
    }

    /**
     * @param array<int, string> $dependencies
     */
    private function check(array $dependencies): void
    {
        foreach ($dependencies as $dependency) {
            if (!empty($dependency)) {
                note($dependency);
                select(
                    label: '',
                    options: ['continue...'],
                    hint:  'press <comment>\<enter\></comment> to continue...'
                );
            }
        }
    }
}
