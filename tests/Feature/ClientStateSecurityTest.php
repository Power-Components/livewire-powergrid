<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Livewire\Exceptions\MethodNotFoundException;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Column;

it('rejects a client update of the locked primary key', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'pk-locked';

        public function datasource(): Builder
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    expect(fn () => Livewire::test($component::class)->set('primaryKey', 'stored_at'))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

it('rejects a client primary key when the component redeclares the property', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'pk-redeclared';

        public string $primaryKey = 'id';

        public function datasource(): Builder
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    expect(fn () => Livewire::test($component::class)->set('primaryKey', 'stored_at'))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

it('keeps a quote in the row id inside the alpine expression', function () {
    $payload = "x');alert(document.domain);void('";

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'row-id-js';

        public function datasource()
        {
            return collect([
                ['id' => "x');alert(document.domain);void('", 'name' => 'Dish 1'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }

        public function setUp(): array
        {
            return [PowerGrid::responsive()];
        }
    };

    $html = Livewire::test($component::class)->html();
    $literal = js_literal($payload);

    expect($html)->toContain('isExpanded('.$literal.')')
        ->and($html)->toContain('toggleExpanded('.$literal.')')
        ->and($html)->not->toContain("isExpanded('x&#039;);alert(document.domain);void(&#039;')");

    $fallback = view('livewire-powergrid::components.themes.tailwind.table.responsive-container', [
        'rowId' => $payload,
    ])->render();

    expect($fallback)->toContain('isExpanded('.$literal.')')
        ->and($fallback)->not->toContain("isExpanded('{{");
});

it('rejects a client tableName on a redeclared property and keeps the mounted one', function () {
    $payload = "x');alert(document.domain);//";

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'client-tablename';

        public function datasource(): Builder
        {
            return Dish::query();
        }

        public function setUp(): array
        {
            return [PowerGrid::header()->showSoftDeletes()];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    $livewire = Livewire::test($component::class, ['tableName' => 'mounted-name']);

    expect(fn () => $livewire->set('tableName', $payload))
        ->toThrow(CannotUpdateLockedPropertyException::class);

    $decoded = html_entity_decode($livewire->assertSet('tableName', 'mounted-name')->html(), ENT_QUOTES | ENT_HTML5);

    expect($decoded)->not->toContain($payload)
        ->and($decoded)->toContain("dispatchClose('pg:softDeletes-mounted-name'");
});

it('rejects a client loadingComponent view', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'client-loading';

        public function datasource(): Builder
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    expect(fn () => Livewire::test($component::class)
        ->set('readyToLoad', false)
        ->set('loadingComponent', 'livewire-powergrid::components.themes.tailwind.header.loading'))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

function filteredDishGrid(array $setUp = []): string
{
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'filtered-dishes';

        public static array $extraSetUp = [];

        public function datasource(): Builder
        {
            return Dish::query();
        }

        public function setUp(): array
        {
            return self::$extraSetUp;
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Id', 'id'), Column::make('Name', 'name')];
        }
    };

    $component::$extraSetUp = $setUp;

    return $component::class;
}

it('reads data endpoint filters with the declared filter type', function () {
    $request = Request::create('/', 'GET', ['powergrid' => [
        'filters' => ['name' => ['type' => 'select', 'value' => ['calories' => '394']]],
    ]]);

    $response = Livewire::test(filteredDishGrid())->instance()->toDataArray($request);

    expect($response->meta->pagination->total)->toBe(Dish::query()->count());
});

it('counts tab badges with the declared filter type', function () {
    Livewire::test(filteredDishGrid([
        PowerGrid::tabs()->add('all', label: 'All')->add('cat1', label: 'Category 1', scope: ['category_id', 1]),
    ]))
        ->set('filters', ['name' => ['type' => 'select', 'value' => ['calories' => '394']]])
        ->assertOk();
});

it('does not expose component extension points as actions', function (string $method, array $params) {
    Livewire::test(filteredDishGrid())->call($method, ...$params);
})->with([
    'plugin hook' => ['onPluginUpdated', ['editable', 'updated', ['id' => 1, 'field' => 'price', 'value' => '0']]],
    'editable hook' => ['onUpdatedEditable', [1, 'price', '0']],
    'data array' => ['toDataArray', []],
])->throws(MethodNotFoundException::class);

it('rejects a client update of the persist prefix', function () {
    Livewire::test(filteredDishGrid())->set('persistPrefix', 'other');
})->throws(CannotUpdateLockedPropertyException::class);
