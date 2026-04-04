<?php

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind};

require __DIR__.'/../Concerns/Components/ComponentsForFilterTest.php';

dataset('filterComponent', [
    'tailwind -> id' => [$component::class, (object) ['theme' => Tailwind::class, 'field' => 'name']],
    'bootstrap -> id' => [$component::class, (object) ['theme' => Bootstrap5::class, 'field' => 'name']],
    'tailwind -> dishes.id' => [$componentJoin::class, (object) ['theme' => Tailwind::class, 'field' => 'dishes.name']],
    'bootstrap -> dishes.id' => [$componentJoin::class, (object) ['theme' => Bootstrap5::class, 'field' => 'dishes.name']],
]);
