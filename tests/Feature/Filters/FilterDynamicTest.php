<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('properly renders dynamic filter', function () {
    view()->addNamespace('tests', __DIR__.'/../../../resources/views/tests');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-dynamic-filter';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function filters(): array
        {
            return [
                Filter::dynamic('name', 'name')
                    ->component('tests::dynamic-select')
                    ->attributes([
                        'class' => 'min-w-[170px]',
                        'options' => [
                            ['name' => 'Active', 'value' => true],
                        ],
                        'option-label' => 'name',
                        'option-value' => 'value',
                        'placeholder' => 'Choose',
                    ]),
            ];
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

    Livewire::test($component::class)
        ->assertSeeHtml('class: min-w-[170px]')
        ->assertSeeHtml('options: [{&quot;name&quot;:&quot;Active&quot;,&quot;value&quot;:true}]')
        ->assertSeeHtml('option-label: name')
        ->assertSeeHtml('option-value: value');
});
