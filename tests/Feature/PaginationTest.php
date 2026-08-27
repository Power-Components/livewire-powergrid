<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

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
        ->assertSeeInOrder(['Showing', '1', 'to', '10', 'of', '15', 'Results'])
        ->set('setUp.footer.perPage', 25)
        ->assertSee('Dish-Item-15')
        ->set('setUp.footer.perPage', 0)
        ->assertSeeInOrder(['1', '15'])
        ->assertSee('Dish-Item-15')
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

it('renders the configured record count mode', function (string $mode, array $expected) {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-record-count';

        public string $recordCountMode = 'full';

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
                    ->showRecordCount($this->recordCountMode),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    Livewire::test($component::class, ['recordCountMode' => $mode])
        ->assertSeeInOrder($expected);
})->with([
    'full' => ['full', ['Showing', '1', 'to', '10', 'of', '15', 'Results']],
    'short' => ['short', ['1', '-', '10', '|', '15']],
    'min' => ['min', ['1', '10']],
]);

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
