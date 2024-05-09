<?php

require(__DIR__ . '/../Concerns/Components/ComponentsForFilterTest.php');

dataset('filterComponent', [
    'tailwind -> id'         => [$component::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    'bootstrap -> id'        => [$component::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
    'tailwind -> dishes.id'  => [$componentJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name']],
    'bootstrap -> dishes.id' => [$componentJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name']],
]);
