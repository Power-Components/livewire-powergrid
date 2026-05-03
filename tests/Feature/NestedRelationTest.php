<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\NestedRelationSearchTable;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches data using nested relations', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('search', 'Not McDonalds')
        ->assertSee('Not McDonalds');
})->with([
    'tailwind' => [NestedRelationSearchTable::class, (object) ['theme' => Tailwind::class]],
]);
