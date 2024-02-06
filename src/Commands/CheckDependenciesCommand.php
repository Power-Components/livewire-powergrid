<?php

namespace PowerComponents\LivewirePowerGrid\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\{info,select};

use PowerComponents\LivewirePowerGrid\Actions\DependenciesCheck;

class CheckDependenciesCommand extends Command
{
    /** @var string */
    protected $signature = 'powergrid:check-dependencies';

    /** @var string */
    protected $description = 'Check PowerGrid dependencies.';

    /** @var bool */
    protected $hidden = true;

    public function handle(): int
    {
        $this->check([
            DependenciesCheck::flatpickr(),
            DependenciesCheck::openspout(),
        ]);

        return self::SUCCESS;
    }

    /**
     * @param array<int, DependenciesCheck> $dependencies
     */
    private function check(array $dependencies): void
    {
        foreach ($dependencies as $dependency) {
            if (!empty($dependency)) {
                info((string) $dependency);
                select('', ['press \<enter\> to continue...'], );
            }
        }
    }
}
