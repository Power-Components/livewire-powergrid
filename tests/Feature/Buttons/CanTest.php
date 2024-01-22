<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('cannot')
                ->slot('cannot: ' . $row->id)
                ->can(false),

            Button::make('can')
                ->slot('can: ' . $row->id)
                ->can(true),

            Button::make('can closure')
                ->slot('can closure: ' . $row->id)
                ->can(fn () => $row->id === 9),
        ];
    }
};

dataset('action:can', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "can" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertDontSeeHtml("cannot: 1</button>")
        ->assertSeeHtml("can: 1</button>")
        ->call('setPage', 2)
        ->assertDontSeeHtml("cannot: 7</button>")
        ->assertSeeHtml("can: 7</button>")

        ->assertDontSeeHtml("cannot: 9</button>")
        ->assertSeeHtml("can: 9</button>")
        ->assertSeeHtml("can closure: 9</button>");
})
    ->with('action:can')
    ->group('action');
