<?php

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\Turbine\Button;
use PowerComponents\Turbine\Support\Actions\ActionsResolver;
use PowerComponents\Turbine\Support\State\{ArrayGridContext, State};

function actionsContext(?callable $actions, ?callable $rules = null): ArrayGridContext
{
    return new ArrayGridContext(
        state: State::fromArray(['primaryKey' => 'id']),
        datasourceResolver: fn () => collect([]),
        fields: new PowerGridFields(),
        actionsResolver: $actions,
        actionRulesResolver: $rules,
    );
}

it('resolves action descriptors as data with a structured event', function () {
    $context = actionsContext(fn ($row) => [
        Button::add('edit')->slot('Edit')->icon('pencil')->dispatch('editDish', ['id' => $row->id]),
        Button::add('remove')->slot('Remove')->call('deleteDish', ['id' => $row->id]),
    ]);

    $descriptors = (new ActionsResolver($context))->forRow((object) ['id' => 7]);

    expect($descriptors)->toHaveCount(2)
        ->and($descriptors[0]['id'])->toBe('edit')
        ->and($descriptors[0]['label'])->toBe('Edit')
        ->and($descriptors[0]['visible'])->toBeTrue()
        ->and($descriptors[0]['disabled'])->toBeFalse()
        ->and($descriptors[0]['event'])->toBe(['type' => 'dispatch', 'event' => 'editDish', 'params' => ['id' => 7]])
        ->and($descriptors[1]['event'])->toBe(['type' => 'call', 'method' => 'deleteDish', 'params' => ['id' => 7]]);
});

it('applies hide/disable rules server-side per row', function () {
    $context = actionsContext(
        fn ($row) => [
            Button::add('edit')->slot('Edit')->call('edit', ['id' => $row->id]),
            Button::add('remove')->slot('Remove')->call('remove', ['id' => $row->id]),
        ],
        fn ($row) => [
            Rule::button('remove')->when(fn ($r) => $r->id === 1)->hide(),
            Rule::button('edit')->when(fn ($r) => $r->id === 2)->disable(),
        ],
    );

    $resolver = new ActionsResolver($context);

    $row1 = collect($resolver->forRow((object) ['id' => 1]))->keyBy('id');
    $row2 = collect($resolver->forRow((object) ['id' => 2]))->keyBy('id');
    $row3 = collect($resolver->forRow((object) ['id' => 3]))->keyBy('id');

    // id 1: remove hidden, edit untouched
    expect($row1['remove']['visible'])->toBeFalse()
        ->and($row1['edit']['visible'])->toBeTrue()
        ->and($row1['edit']['disabled'])->toBeFalse()
        // id 2: edit disabled, remove untouched
        ->and($row2['edit']['disabled'])->toBeTrue()
        ->and($row2['remove']['visible'])->toBeTrue()
        // id 3: no rule matched, both fully enabled/visible
        ->and($row3['edit']['disabled'])->toBeFalse()
        ->and($row3['remove']['visible'])->toBeTrue();
});

it('applies setAttribute rule and confirm/can into the descriptor', function () {
    $context = actionsContext(
        fn ($row) => [
            Button::add('remove')->slot('Remove')
                ->call('remove', ['id' => $row->id])
                ->confirm('Are you sure?'),
            Button::add('secret')->slot('Secret')
                ->call('secret', ['id' => $row->id])
                ->can(fn ($r) => $r->id !== 9),
        ],
        fn ($row) => [
            Rule::button('remove')->when(fn ($r) => $r->id === 5)->setAttribute('class', 'text-red-500'),
        ],
    );

    $resolver = new ActionsResolver($context);

    $row5 = collect($resolver->forRow((object) ['id' => 5]))->keyBy('id');
    $row9 = collect($resolver->forRow((object) ['id' => 9]))->keyBy('id');

    expect($row5['remove']['confirm'])->toBe('Are you sure?')
        ->and($row5['remove']['attributes'])->toMatchArray(['class' => 'text-red-500'])
        // can() closure false → not visible for id 9
        ->and($row9['secret']['visible'])->toBeFalse()
        ->and($row5['secret']['visible'])->toBeTrue();
});

it('returns no descriptors when the context declares no actions', function () {
    $context = actionsContext(actions: null);

    expect((new ActionsResolver($context))->forRow((object) ['id' => 1]))->toBe([]);
});
