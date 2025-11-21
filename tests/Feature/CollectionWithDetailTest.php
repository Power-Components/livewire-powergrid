<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DatasourceCollectionTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('collection detail', function () {
    livewire(DatasourceCollectionTable::class)
        ->assertSee('Name 1')
        ->assertDontSeeHtml([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->call('toggleDetail', 2)
        ->assertDispatched('pg-toggle-detail-testing-datasource-collection-table-2');
});
