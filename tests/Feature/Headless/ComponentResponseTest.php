<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\{Button, Column};

function dishGridComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'dishGrid';

        public function setUp(): array
        {
            return [PowerGrid::footer()->showPerPage(5)];
        }

        public function datasource(): Builder
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->sortable(),
                Column::make('Name', 'name')->searchable()->sortable(),
            ];
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
        }

        public function actions($row): array
        {
            return [Button::add('edit')->slot('Edit')->dispatch('edit', ['id' => $row->id])];
        }
    };
}

it('builds the envelope from a PowerGridComponent and echoes request state', function () {
    $request = Request::create('/', 'GET', ['powergrid' => ['sortField' => 'id', 'sortDirection' => 'asc']]);

    $envelope = dishGridComponent()->toDataArray($request);

    expect($envelope['data'])->not->toBeEmpty()
        ->and($envelope['data'][0])->toHaveKeys(['id', 'name'])
        // rows are clean — no Livewire display metadata leaks into the JSON
        ->and($envelope['data'][0])->not->toHaveKey('__turbine_actions')
        ->and($envelope['data'][0])->not->toHaveKey('__turbine_rules')
        ->and($envelope['meta']['pagination']['per_page'])->toBe(5)
        ->and($envelope['meta']['pagination']['total'])->toBe(Dish::query()->count())
        ->and($envelope['columns'])->toHaveCount(2)
        ->and($envelope['filters'][0]['key'])->toBe('input_text');
});

it('applies request search + sort through the component bridge', function () {
    $request = Request::create('/', 'GET', ['powergrid' => [
        'search' => 'Pastel',
        'sortField' => 'price',
        'sortDirection' => 'desc',
    ]]);

    $envelope = dishGridComponent()->toDataArray($request);

    expect($envelope['meta']['search'])->toBe('Pastel')
        ->and($envelope['meta']['sort'])->toMatchArray(['field' => 'price', 'direction' => 'desc'])
        ->and($envelope['meta']['pagination']['total'])->toBe(
            Dish::query()->where('name', 'like', '%Pastel%')->count()
        );
});

it('resolves component actions into the envelope keyed by primary key', function () {
    $envelope = dishGridComponent()->toDataResponse(Request::create('/'))->getData(true);

    $firstId = (int) $envelope['data'][0]['id'];

    expect($envelope['actions'])->toHaveKey((string) $firstId)
        ->and($envelope['actions'][(string) $firstId][0])->toMatchArray([
            'id' => 'edit',
            'event' => ['type' => 'dispatch', 'event' => 'edit', 'params' => ['id' => $firstId]],
        ]);
});
