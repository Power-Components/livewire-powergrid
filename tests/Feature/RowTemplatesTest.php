<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('interpolates row templates on the server and escapes values', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-row-templates';

        public function datasource()
        {
            return collect([
                (object) ['id' => 1, 'name' => 'Pizza'],
                (object) ['id' => 2, 'name' => '<script>alert(1)</script>'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name', fn ($row) => [
                    'dish' => [
                        'id' => $row->id,
                        'name' => $row->name,
                    ],
                ]);
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->template(),
            ];
        }

        public function rowTemplates(): array
        {
            return [
                'dish' => '<a href="/dishes/{{ id }}" class="link">{{ name }}</a>',
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<a href="/dishes/1" class="link">Pizza</a>')
        ->assertSeeHtml('<a href="/dishes/2" class="link">&lt;script&gt;alert(1)&lt;/script&gt;</a>')
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertDontSeeHtml('pgRenderRowTemplate')
        ->assertDontSeeHtml('pgRowTemplates');
});

it('returns null when the named template is missing', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-row-templates-missing';
    };

    expect($component->renderRowTemplate(['unknown' => ['name' => 'x']]))->toBeNull()
        ->and($component->renderRowTemplate('plain'))->toBeNull();
});
