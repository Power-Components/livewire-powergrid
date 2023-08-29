<?php

use Illuminate\Support\Js;

use PowerComponents\LivewirePowerGrid\Button;

;

use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('dispatchTo')
                ->slot('dispatchTo: ' . $row->id)
                ->dispatchTo('another-component', 'executeDispatchTo', ['id' => $row->id]),
        ];
    }
};

dataset('action:dispatchTo', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "dispatchTo" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("wire:click=\"\$dispatchTo('another-component', 'executeDispatchTo', " . Js::from(['id' => 1]) . ")\">dispatchTo: 1</button>")
        ->assertSeeHtml("wire:click=\"\$dispatchTo('another-component', 'executeDispatchTo', " . Js::from(['id' => 2]) . ")\">dispatchTo: 2</button>")
        ->assertDontSeeHtml("wire:click=\"\$dispatchTo('another-component', 'executeDispatchTo', " . Js::from(['id' => 7]) . ")\">dispatchTo: 7</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("wire:click=\"\$dispatchTo('another-component', 'executeDispatchTo', " . Js::from(['id' => 7]) . ")\">dispatchTo: 7</button>")
        ->assertDontSeeHtml("wire:click=\"\$dispatchTo('another-component', 'executeDispatchTo', " . Js::from(['id' => 1]) . ")\">dispatchTo: 1</button>");
})
    ->with('action:dispatchTo')
    ->group('action');
