<?php

use Illuminate\Support\Js;

use PowerComponents\LivewirePowerGrid\Button;

;

use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

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
        ->assertSeeHtml("wire:click=\"\$call('executeCall', " . Js::from([
            'params' => ['id' => 1],
        ]) . ")\">call: 1</button>")
        ->assertSeeHtml("wire:click=\"\$call('executeCall', " . Js::from([
            'params' => ['id' => 2],
        ]) . ")\">call: 2</button>")
        ->assertDontSeeHtml("wire:click=\"\$call('executeCall', " . Js::from([
            'params' => ['id' => 7],
        ]) . ")\">call: 7</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("wire:click=\"\$call('executeCall', " . Js::from([
            'params' => ['id' => 7],
        ]) . ")\">call: 7</button>")
        ->assertDontSeeHtml("wire:click=\"\$call('executeCall', " . Js::from([
            'params' => ['id' => 1],
        ]) . ")\">call: 1</button>");
})
    ->with('action:call')
    ->group('action');
