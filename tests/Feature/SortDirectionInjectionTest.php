<?php

use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines\{ColumnRawQueries, Sorting};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{DishesCustomSortTable, DishesNaturalSortTable};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

/*
| Regression tests for GHSA-7fgc-3h6c-698r — SQL injection through the public
| sortDirection Livewire property. The direction reaches raw ORDER BY clauses
| through two paths (naturalSort and sortUsing callbacks); both must be
| restricted to an asc/desc allowlist.
*/

$payload = 'asc, (SELECT SLEEP(3))';

it('neutralizes SQL injection through sortDirection on a naturalSort column', function () use ($payload) {
    $component = new DishesNaturalSortTable();

    // The advisory bypass: an empty sortField skips the Sorting pipeline, but
    // ColumnRawQueries still applies the naturalSort orderByRaw unconditionally.
    $component->sortField = '';
    $component->sortDirection = $payload;

    $query = Dish::query();

    (new ColumnRawQueries($component))->handle($query, fn ($q) => $q);

    expect($query->toSql())
        ->not->toContain('SLEEP')
        ->toContain(' asc');
});

it('neutralizes SQL injection through sortDirection in a custom sort callback', function () use ($payload) {
    $component = new DishesCustomSortTable();

    // The "price" column uses sortUsing() with orderByRaw("... {$direction}").
    $component->sortField = 'price';
    $component->sortDirection = $payload;

    $query = Dish::query();

    (new Sorting($component))->handle($query, fn ($q) => $q);

    expect($query->toSql())->not->toContain('SLEEP');
});

it('normalizes a tampered sortDirection to asc through the Livewire lifecycle', function () use ($payload) {
    // Reproduces the exact advisory request: sortField="" + a malicious
    // sortDirection sent via /livewire/update. The updatedSortDirection hook
    // must normalize it, and the render (which runs the naturalSort query)
    // must not fail with a SQL error.
    livewire(DishesNaturalSortTable::class)
        ->set('sortField', '')
        ->set('sortDirection', $payload)
        ->assertSet('sortDirection', 'asc');
});
