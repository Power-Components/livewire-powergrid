<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\NestedRelationSearchTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches data using nested relations', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'Not McDonalds')
        ->assertSee('Not McDonalds');
})->with('nested_search_themes');

dataset('nested_search_themes', [
    'tailwind'  => [NestedRelationSearchTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [NestedRelationSearchTable::class, (object) ['theme' => 'bootstrap']],
]);
