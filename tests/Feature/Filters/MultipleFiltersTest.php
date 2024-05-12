<?php

use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require(__DIR__ . '/../../Concerns/Components/ComponentsForFilterTest.php');

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

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertSeeHtml('dish_name_xyz_placeholder');

    $filters = array_merge($component->filters, filterNumber('price', min: '1\'500.20', max: '3\'000.00'));

    $component->set('filters', $filters)
        ->assertSeeHtml('placeholder="min_xyz_placeholder"')
        ->assertSeeHtml('placeholder="max_xyz_placeholder"')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)->toBe($filters);

    $filters = array_merge($component->filters, filterBoolean('in_stock', 'true'));

    $component->set('filters', $filters)
       ->assertDontSee('Barco-Sushi Simples');

    expect($component->filters)
        ->toMatchArray($filters);

    $component->call('clearFilter', $params->field);

    $component->assertDontSee('Polpetone Filé Mignon');

    $component->call('clearAllFilters');

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');
    expect($component->filters)
        ->toMatchArray([]);
})->group('filters')
->with('filterComponent');
