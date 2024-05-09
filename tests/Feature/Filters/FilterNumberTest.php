<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require(__DIR__ . '/../../Concerns/Components/ComponentsForFilterTest.php');

it('properly filters by filter Number', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme);

    $filters = array_merge($component->filters, filterNumber('price', min: '1\'500.20', max: '3\'000.00'));

    $component->set('filters', $filters)
        ->assertSeeHtml('placeholder="min_xyz_placeholder"')
        ->assertSeeHtml('placeholder="max_xyz_placeholder"')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)->toBe($filters);
})->group('filters')
->with('filterComponent');

it('properly filters by filter Number with wrong separators', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme);

    // Use wrong separators
    $filters = array_merge($component->filters, filterNumber('price', min: '1@500#20', max: '3@000#00'));

    $component->set('filters', $filters)
        ->assertSee('No records found');
})
->skipOnPostgreSQL('PG will throw "invalid input syntax for type double precision"')
->group('filters')
->with('filterComponent');
