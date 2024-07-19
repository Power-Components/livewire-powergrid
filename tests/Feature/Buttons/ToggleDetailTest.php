<?php

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

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

$dotNotationPrimaryKeyComponent = new class () extends DishTableBase {
    public string $primaryKey = 'dishes.id';

    public ?string $primaryKeyAlias = 'id';

    public function dataSource(): Builder
    {
        return Dish::query()
            ->join('categories as newCategories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'newCategories.id');
            })
            ->select('dishes.*', 'newCategories.name as category_name');
    }

    public function actions($row): array
    {
        return [
            Button::make('toggleDetail')
                ->slot('toggleDetail: ' . $row->id)
                ->toggleDetail(),
        ];
    }
};

it('properly displays "toggleDetail" on edit button', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("wire:click=\"toggleDetail(&#039;1&#039;)\">toggleDetail: 1</button>")
        ->assertSeeHtml("wire:click=\"toggleDetail(&#039;2&#039;)\">toggleDetail: 2</button>")
        ->assertDontSeeHtml("wire:click=\"toggleDetail(&#039;7&#039;)\">toggleDetail: 7</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("wire:click=\"toggleDetail(&#039;7&#039;)\">toggleDetail: 7</button>")
        ->assertDontSeeHtml("wire:click=\"toggleDetail(&#039;1&#039;)\">toggleDetail: 1</button>");
})
    ->with([
        'tailwind'  => [$component::class, (object) ['theme' => 'tailwind']],
        'bootstrap' => [$component::class, (object) ['theme' => 'bootstrap']],
    ])
    ->group('action');

it('properly displays "toggleDetail" on edit button using dot notation in primaryKey', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("\"toggleDetail(&#039;1&#039;)\">toggleDetail: 1</button>")
        ->assertSeeHtml("\"toggleDetail(&#039;2&#039;)\">toggleDetail: 2</button>")
        ->assertDontSeeHtml("wire:click=\"toggleDetail(&#039;7&#039;)\">toggleDetail: 7</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("wire:click=\"toggleDetail(&#039;7&#039;)\">toggleDetail: 7</button>")
        ->assertDontSeeHtml("wire:click=\"toggleDetail(&#039;1&#039;)\">toggleDetail: 1</button>");
})
    ->with([
        'tailwind join primary key alias'  => [$dotNotationPrimaryKeyComponent::class, (object) ['theme' => 'tailwind']],
        'bootstrap join primary key alias' => [$dotNotationPrimaryKeyComponent::class, (object) ['theme' => 'bootstrap']],
    ])
    ->group('action');
