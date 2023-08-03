<?php

use Illuminate\Support\Js;

use Livewire\Attributes\On;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

$component = new class () extends DishTableBase {
    #[On('executeDispatch')]
    public function executeCall(array $params)
    {
    }

    public function actions($row): array
    {
        return [
            Button::make('dispatch')
                ->slot('dispatch: ' . $row->id)
                ->dispatch('executeDispatch', ['id' => $row->id]),
        ];
    }
};

dataset('action:dispatch', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "dispatch" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("wire:click=\"\$dispatch('executeDispatch', " . Js::from(['id' => 1]) . ")\">dispatch: 1</button>")
        ->assertSeeHtml("wire:click=\"\$dispatch('executeDispatch', " . Js::from(['id' => 2]) . ")\">dispatch: 2</button>")
        ->assertDontSeeHtml("wire:click=\"\$dispatch('executeDispatch', " . Js::from(['id' => 7]) . ")\">dispatch: 7</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("wire:click=\"\$dispatch('executeDispatch', " . Js::from(['id' => 7]) . ")\">dispatch: 7</button>")
        ->assertDontSeeHtml("wire:click=\"\$dispatch('executeDispatch', " . Js::from(['id' => 1]) . ")\">dispatch: 1</button>");
})
    ->with('action:dispatch')
    ->group('action');
