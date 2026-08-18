<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('removes <script> tag from custom field', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-fields-script';

        public function datasource()
        {
            return collect([['id' => 1, 'link' => 'hello there! <script>alert(1)</script>']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('link');
        }

        public function columns(): array
        {
            return [Column::make('Link', 'link')];
        }
    };

    Livewire::test($component::class)
        ->assertDontSeeHtml('<script>')
        ->assertSee('hello there!');
});

it('runs e() helper in standard PG fields', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-fields-escape';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => '<img src=x onerror=alert(1)>']]);
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
        ->assertDontSeeHtml('<img')
        ->assertSeeHtml('&lt;img');
});

it('escapes closure-based PG fields by default', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-fields-no-escape';

        public function datasource()
        {
            return collect([['id' => 1, 'link' => 'https://google.com']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('link', function ($row) {
                    return '<a href="'.data_get($row, 'link').'">Link</a>';
                });
        }

        public function columns(): array
        {
            return [Column::make('Link', 'link')];
        }
    };

    // Escaping happens at the render sink: closure-returned HTML is escaped
    // unless the developer opts into raw rendering via a custom cell view.
    Livewire::test($component::class)
        ->assertDontSeeHtml('<a href="https://google.com">Link</a>')
        ->assertSeeHtml('&lt;a href=&quot;https://google.com&quot;&gt;Link&lt;/a&gt;');
});
