<?php

use Livewire\Testing\TestableLivewire;

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;

use PowerComponents\LivewirePowerGrid\Tests\ResponsiveDetailRowTable;

it('render x-responsive correctly with detail row state', function () {
    /** @var PowerGridComponent|TestableLivewire $component */
    $component = livewire(ResponsiveDetailRowTable::class);

    expect($component)
        ->setUp->detail->view->toBe('livewire-powergrid::tests.detail')
        ->setUp->detail->show->toBeFalse();

    $component->call('dynamicDetailRow');

    $component->set('screenWidth', 300);

    expect($component)
        ->setUp->detail->show->toBeTrue()
        ->setUp->detail->hiddenColumns->toBeArray();

    $component->assertSeeHtmlInOrder([
        'column-field="id"',
        'column-title="Id"',
        'component="' . $component->id . '"',
        'x-responsive="table-column flex absolute -left-[99999px]" target="default"',
    ]);
});
