<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('parent')
                ->slot('parent: ' . $row->id)
                ->parent('executeParent', ['id' => $row->id]),
        ];
    }
};

dataset('action:parent', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "parent" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml('wire:click="$parent.executeParent(JSON.parse(&#039;{\u0022id\u0022:1}&#039;))">parent: 1</button>')
        ->assertSeeHtml('wire:click="$parent.executeParent(JSON.parse(&#039;{\u0022id\u0022:2}&#039;))">parent: 2</button>')
        ->assertDontSeeHtml('wire:click="$parent.executeParent(JSON.parse(&#039;{\u0022id\u0022:7}&#039;))">parent: 7</button>')
        ->call('setPage', 2)
        ->assertSeeHtml('wire:click="$parent.executeParent(JSON.parse(&#039;{\u0022id\u0022:7}&#039;))">parent: 7</button>')
        ->assertDontSeeHtml('wire:click="$parent.executeParent(JSON.parse(&#039;{\u0022id\u0022:2}&#039;))">parent: 2</button>');
})
    ->with('action:parent')
    ->group('action');
