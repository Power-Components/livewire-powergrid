<?php

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

require __DIR__.'/../Concerns/Components/ComponentsForFilterTest.php';

dataset('filterComponent', [
    'tailwind -> id' => [$component::class, (object) ['theme' => Tailwind::class, 'field' => 'name']],
    'tailwind -> dishes.id' => [$componentJoin::class, (object) ['theme' => Tailwind::class, 'field' => 'dishes.name']],
]);
