<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('properly displays record count and navigates', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-pagination';

        public function datasource()
        {
            $data = [];
            for ($i = 1; $i <= 15; $i++) {
                $data[] = ['id' => $i, 'name' => 'Dish-Item-'.str_pad($i, 2, '0', STR_PAD_LEFT)];
            }

            return collect($data);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::footer()
                    ->showPerPage(10)
                    ->showRecordCount(),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    Livewire::test($component::class)
        // Default "full" showRecordCount
        ->assertSeeInOrder(['Showing', '1', 'to', '10', 'of', '15', 'Results'])

        // "short" showRecordCount
        ->set('setUp.footer.recordCount', 'short')
        ->assertSeeInOrder(['1', '-', '10', '|', '15'])

        // "min" showRecordCount
        ->set('setUp.footer.recordCount', 'min')
        ->assertSeeInOrder(['1', '10'])

        // Per page changes
        ->set('setUp.footer.perPage', 11)
        ->assertSeeInOrder(['1', '11'])
        ->set('setUp.footer.perPage', 0) // All
        ->assertSeeInOrder(['1', '15'])
        ->assertSee('Dish-Item-15')

        // Navigation
        ->set('setUp.footer.perPage', 10)
        ->assertSee('Dish-Item-01')
        ->assertDontSee('Dish-Item-11')
        ->call('nextPage')
        ->assertSee('Dish-Item-11')
        ->assertDontSee('Dish-Item-01')
        ->call('previousPage')
        ->assertSee('Dish-Item-01')
        ->assertDontSee('Dish-Item-11')
        ->call('gotoPage', 2)
        ->assertSee('Dish-Item-11')
        ->assertDontSee('Dish-Item-01');
});

it('sanitizes pageName', function (string $pageNameCandidate, string $result) {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-pagename';

        public string $pageNameCandidate = '';

        public function datasource()
        {
            $data = [];
            for ($i = 1; $i <= 10; $i++) {
                $data[] = ['id' => $i, 'name' => 'Dish '.$i];
            }

            return collect($data);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::footer()
                    ->showPerPage(5)
                    ->pageName($this->pageNameCandidate),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    Livewire::test($component::class, ['pageNameCandidate' => $pageNameCandidate])
        ->assertSeeHtml("gotoPage(2, '{$result}')");
})->with([
    'some invalid characters' => ['customPage12Ντόναλντ34', 'customPage1234'],
    'only invalid characters' => ['Ντόναλντ', 'page'],
    'empty page name' => ['', 'page'],
]);
