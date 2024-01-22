<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function executeCall(array $params)
    {
    }

    public function actions($row): array
    {
        return [
            Button::make('edit')
                ->slot('call: ' . $row->id)
                ->call('executeCall', [
                    'params' => ['id' => $row->id],
                ]),
        ];
    }
};

dataset('action:call', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "call" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml('wire:click="$call(&#039;executeCall&#039;, JSON.parse(&#039;{\u0022params\u0022:{\u0022id\u0022:1}}&#039;))">call: 1</button>')
        ->assertSeeHtml('wire:click="$call(&#039;executeCall&#039;, JSON.parse(&#039;{\u0022params\u0022:{\u0022id\u0022:2}}&#039;))">call: 2</button>')
        ->assertDontSeeHtml('wire:click="$call(&#039;executeCall&#039;, JSON.parse(&#039;{\u0022params\u0022:{\u0022id\u0022:7}}&#039;))">call: 7</button>')
        ->call('setPage', 2)
        ->assertSeeHtml('wire:click="$call(&#039;executeCall&#039;, JSON.parse(&#039;{\u0022params\u0022:{\u0022id\u0022:7}}&#039;))">call: 7</button>')
        ->assertDontSeeHtml('wire:click="$call(&#039;executeCall&#039;, JSON.parse(&#039;{\u0022params\u0022:{\u0022id\u0022:1}}&#039;))">call: 1</button>');
})
    ->with('action:call')
    ->group('action');
