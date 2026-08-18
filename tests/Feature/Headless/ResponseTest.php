<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\{Button, Column};
use PowerComponents\Turbine\Response;
use PowerComponents\Turbine\Support\State\{ArrayGridContext, State};

function responseContext(array $statePayload = []): ArrayGridContext
{
    return new ArrayGridContext(
        state: State::fromArray(array_merge([
            'primaryKey' => 'id',
            'tableName' => 'dishes',
            'sortField' => 'id',
            'sortDirection' => 'asc',
            'setUp' => ['footer' => ['perPage' => 5, 'pageName' => 'page']],
        ], $statePayload)),
        datasourceResolver: fn () => Dish::query(),
        fields: (new PowerGridFields())->add('id')->add('name')->add('price'),
        columns: [
            Column::add()->title('Id')->field('id')->sortable(),
            Column::add()->title('Name')->field('name')->searchable()->sortable(),
        ],
        filters: [Filter::inputText('name')],
        actionsResolver: fn ($row) => [
            Button::add('edit')->slot('Edit')->dispatch('editDish', ['id' => $row->id]),
        ],
    );
}

it('builds a JSON envelope with data, meta, columns, filters and actions', function () {
    $envelope = Response::make(responseContext())->toArray();

    expect($envelope['data'])->toBeArray()->not->toBeEmpty()
        ->and($envelope['data'][0])->toHaveKeys(['id', 'name'])
        // meta: pagination + state echo
        ->and($envelope['meta']['pagination']['per_page'])->toBe(5)
        ->and($envelope['meta']['pagination']['total'])->toBe(Dish::query()->count())
        ->and($envelope['meta']['pagination']['current_page'])->toBe(1)
        ->and($envelope['meta']['sort']['field'])->toBe('id')
        // columns schema
        ->and($envelope['columns'])->toHaveCount(2)
        ->and($envelope['columns'][0])->toMatchArray(['field' => 'id', 'sortable' => true, 'searchable' => false])
        ->and($envelope['columns'][1])->toMatchArray(['field' => 'name', 'searchable' => true])
        // filters schema
        ->and($envelope['filters'][0]['key'])->toBe('input_text')
        ->and($envelope['filters'][0]['field'])->toBe('name');
});

it('keys resolved action descriptors by primary key', function () {
    $envelope = Response::make(responseContext())->toArray();

    $firstId = (int) $envelope['data'][0]['id'];

    expect($envelope['actions'])->toHaveKey((string) $firstId)
        ->and($envelope['actions'][(string) $firstId][0])->toMatchArray([
            'id' => 'edit',
            'label' => 'Edit',
            'event' => ['type' => 'dispatch', 'event' => 'editDish', 'params' => ['id' => $firstId]],
        ]);
});

it('echoes search state and narrows data in the envelope', function () {
    $envelope = Response::make(responseContext(['search' => 'Pastel']))->toArray();

    expect($envelope['meta']['search'])->toBe('Pastel')
        ->and($envelope['meta']['pagination']['total'])->toBe(
            Dish::query()->where('name', 'like', '%Pastel%')->count()
        );
});

it('produces a JSON response', function () {
    $response = Response::make(responseContext())->toResponse();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->headers->get('content-type'))->toContain('application/json');
});
