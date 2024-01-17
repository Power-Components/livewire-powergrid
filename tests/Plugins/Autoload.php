<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Plugins;

use Faker\{Factory, Generator};
use Livewire\Features\SupportTesting\Testable;
use Pest\Plugin;

Plugin::uses(InteractsWithLivewire::class);

function livewire(string $name, array $params = [], array $queryParams = []): Testable
{
    return test()->livewire(...func_get_args());
}

function fake(string $locale = Factory::DEFAULT_LOCALE): Generator
{
    return Factory::create($locale);
}
