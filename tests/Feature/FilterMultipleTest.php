<?php

use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;

it('properly filters by inputText, number, boolean filter and clearAll', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterInputText('ba', 'contains', $params->field));

    if (str_contains($params->field, '.')) {
        $data  = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $table => [
                        $field => 'ba',
                    ],
                ],
                'input_text_options' => [
                    $table => [
                        $field => 'contains',
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $params->field => 'ba',
                ],
                'input_text_options' => [
                    $params->field => 'contains',
                ],
            ]);
    }

    $component->assertSee('Barco-Sushi da Sueli');

    $filters = array_merge($component->filters, filterNumber('price', '80,00', '100'));

    $component->set('filters', $filters)
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)
        ->toMatchArray($filters);

    $filters = array_merge($component->filters, filterBoolean('in_stock', 'true'));

    $component->set('filters', $filters)
        ->assertDontSee('Barco-Sushi Simples');

    expect($component->filters)
        ->toMatchArray($filters);

    $component->call('clearFilter', $params->field);

    $filters = array_merge(
        [
            'input_text'         => [],
            'input_text_options' => [],
        ],
        filterNumber('price', '80,00', '100'),
        filterBoolean('in_stock', 'true')
    );
    expect($component->filters)
        ->toMatchArray($filters);

    $component->assertDontSee('Polpetone Filé Mignon');

    $component->call('clearAllFilters');

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');
    expect($component->filters)
        ->toMatchArray([]);
})->with('themes with name field');
