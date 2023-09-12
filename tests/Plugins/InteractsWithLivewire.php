<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Plugins;

use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

trait InteractsWithLivewire
{
    /** @param  array<string, mixed>  $params */
    public function livewire(string $name, array $params = [], array $queryParams = []): Testable
    {
        return Livewire::withQueryParams($queryParams)->test($name, $params);
    }
}
