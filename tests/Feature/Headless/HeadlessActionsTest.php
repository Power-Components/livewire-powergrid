<?php

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Support\Actions\ActionsResolver;
use PowerComponents\Turbine\Button;
use PowerComponents\Turbine\Response\ActionDescriptor;
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
        ->and($descriptors[0]->id)->toBe('edit')
        ->and($descriptors[0]->label)->toBe('Edit')
        ->and($descriptors[0]->visible)->toBeTrue()
        ->and($descriptors[0]->disabled)->toBeFalse()
        ->and($descriptors[0]->attributes['event'])->toBe(['type' => 'dispatch', 'event' => 'editDish', 'params' => ['id' => 7]])
        ->and($descriptors[1]->attributes['event'])->toBe(['type' => 'call', 'method' => 'deleteDish', 'params' => ['id' => 7]]);
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

    expect($row1['remove']->visible)->toBeFalse()
        ->and($row1['edit']->visible)->toBeTrue()
        ->and($row1['edit']->disabled)->toBeFalse()
        ->and($row2['edit']->disabled)->toBeTrue()
        ->and($row2['remove']->visible)->toBeTrue()
        ->and($row3['edit']->disabled)->toBeFalse()
        ->and($row3['remove']->visible)->toBeTrue();
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

    expect($row5['remove']->attributes['wire:confirm'])->toBe('Are you sure?')
        ->and($row5['remove']->attributes)->toMatchArray(['class' => 'text-red-500'])
        ->and($row9['secret']->visible)->toBeFalse()
        ->and($row5['secret']->visible)->toBeTrue();
});

it('returns no descriptors when the context declares no actions', function () {
    $context = actionsContext(actions: null);

    expect((new ActionsResolver($context))->forRow((object) ['id' => 1]))->toBe([]);
});

it('passes wire:* attributes and event dynamically through the attributes bag', function () {
    $context = actionsContext(fn ($row) => [
        Button::add('edit')->slot('Edit')->dispatch('editDish', ['id' => $row->id]),
        Button::add('remove')->slot('Remove')->confirm('Delete this item?'),
    ]);

    $descriptors = (new ActionsResolver($context))->forRow((object) ['id' => 3]);

    expect($descriptors[0])->toBeInstanceOf(ActionDescriptor::class)
        ->and($descriptors[0]->attributes['event'])->toBe(['type' => 'dispatch', 'event' => 'editDish', 'params' => ['id' => 3]])
        ->and(array_key_exists('wire:confirm', $descriptors[0]->attributes))->toBeFalse()
        ->and($descriptors[1]->attributes['wire:confirm'])->toBe('Delete this item?')
        ->and(array_key_exists('event', $descriptors[1]->attributes))->toBeFalse()
        ->and($descriptors[0]->all())->toHaveKeys(['id', 'label', 'icon', 'tag', 'visible', 'disabled', 'attributes']);
});
