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
    $response = Response::make(responseContext())->envelope();

    expect($response->data)->toBeArray()->not->toBeEmpty()
        ->and($response->data[0])->toHaveKeys(['id', 'name'])
        ->and($response->meta->pagination->perPage)->toBe(5)
        ->and($response->meta->pagination->total)->toBe(Dish::query()->count())
        ->and($response->meta->pagination->currentPage)->toBe(1)
        ->and($response->meta->sort->field)->toBe('id')
        ->and($response->columns)->toHaveCount(2)
        ->and($response->columns[0]->all())->toMatchArray(['field' => 'id', 'sortable' => true, 'searchable' => false])
        ->and($response->columns[1]->all())->toMatchArray(['field' => 'name', 'searchable' => true])
        ->and($response->filters[0]->key)->toBe('input_text')
        ->and($response->filters[0]->field)->toBe('name');
});

it('keys resolved action descriptors by primary key', function () {
    $response = Response::make(responseContext())->envelope();

    $firstId = (int) $response->data[0]['id'];

    expect($response->actions)->toHaveKey((string) $firstId)
        ->and($response->actions[(string) $firstId][0]->all())->toMatchArray([
            'id' => 'edit',
            'label' => 'Edit',
        ]);
});

it('echoes search state and narrows data in the envelope', function () {
    $response = Response::make(responseContext(['search' => 'Pastel']))->envelope();

    expect($response->meta->search)->toBe('Pastel')
        ->and($response->meta->pagination->total)->toBe(
            Dish::query()->where('name', 'like', '%Pastel%')->count()
        );
});

it('produces a JSON response', function () {
    $response = Response::make(responseContext())->toResponse();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->headers->get('content-type'))->toContain('application/json');
});
