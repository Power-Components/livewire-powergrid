<?php

use Illuminate\Support\HtmlString;
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
        ->assertDontSeeHtml('<script>alert(1)</script>')
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

function closureLinkTable(): string
{
    return (new class() extends PowerGridComponent
    {
        public string $tableName = 'test-fields-closure';

        public function datasource()
        {
            return collect([['id' => 1, 'link' => 'https://google.com', 'badge' => 'new']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('link', fn ($row) => '<a href="'.data_get($row, 'link').'">Link</a>')
                ->add('badge', fn ($row) => new HtmlString('<b>'.data_get($row, 'badge').'</b>'));
        }

        public function columns(): array
        {
            return [Column::make('Link', 'link'), Column::make('Badge', 'badge')];
        }
    })::class;
}

it('escapes closure-based PG fields by default', function () {
    Livewire::test(closureLinkTable())
        ->assertSeeHtml('&lt;a href=&quot;https://google.com&quot;&gt;Link&lt;/a&gt;')
        ->assertSeeHtml('<b>new</b>');
});

it('renders closure-based PG fields as raw HTML when escape_closure_fields is disabled', function () {
    config(['livewire-powergrid.escape_closure_fields' => false]);

    Livewire::test(closureLinkTable())
        ->assertSeeHtml('<a href="https://google.com">Link</a>')
        ->assertSeeHtml('<b>new</b>');
});
