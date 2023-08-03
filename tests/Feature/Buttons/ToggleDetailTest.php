<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('toggleDetail')
                ->slot('toggleDetail: ' . $row->id)
                ->toggleDetail(),
        ];
    }
};

dataset('action:toggleDetail', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "toggleDetail" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("wire:click=\"toggleDetail(1)\">toggleDetail: 1</button>")
        ->assertSeeHtml("wire:click=\"toggleDetail(2)\">toggleDetail: 2</button>")
        ->assertDontSeeHtml("wire:click=\"toggleDetail(7)\">toggleDetail: 7</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("wire:click=\"toggleDetail(7)\">toggleDetail: 7</button>")
        ->assertDontSeeHtml("wire:click=\"toggleDetail(1)\">toggleDetail: 1</button>");
})
    ->with('action:toggleDetail')
    ->group('action');
