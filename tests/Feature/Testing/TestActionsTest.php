<?php

use Illuminate\Support\{Carbon, Collection, Number};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind};
use PowerComponents\LivewirePowerGrid\{Button, Column, PowerGridComponent, PowerGridFields};

$component = new class () extends PowerGridComponent {
    public string $tableName = 'datasource-collection-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showToggleColumns()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Collection
    {
        return collect([
            [
                'id'         => 29,
                'name'       => 'Luan',
                'balance'    => 241.86,
                'is_online'  => true,
                'created_at' => '2023-01-01 00:00:00',
            ],
            [
                'id'         => 57,
                'name'       => 'Daniel',
                'balance'    => 166.51,
                'is_online'  => true,
                'created_at' => '2023-02-02 00:00:00',
            ],
            [
                'id'         => 93,
                'name'       => 'Claudio',
                'balance'    => 219.01,
                'is_online'  => false,
                'created_at' => '2023-03-03 00:00:00',
            ],
            [
                'id'         => 104,
                'name'       => 'Vitor',
                'balance'    => 44.28,
                'is_online'  => true,
                'created_at' => '2023-04-04 00:00:00',
            ],
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('balance', fn ($item) => Number::currency($item->balance, in: 'BRL', locale: 'pt-BR'))
            ->add('is_online', fn ($item) => $item->is_online ? '✅' : '❌')
            ->add('created_at', fn ($item) => Carbon::parse($item->created_at))
            ->add('created_at_formatted', fn ($item) => Carbon::parse($item->created_at)->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Index', 'id')->index(),

            Column::make('ID', 'id'),

            Column::add()
                ->title('Name')
                ->field('name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Balance')
                ->field('balance')
                ->sortable(),

            Column::add()
                ->title('Online')
                ->field('is_online'),

            Column::add()
                ->title('Created At')
                ->field('created_at_formatted'),

            Column::action('Action'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('view')
                ->icon('default-eye', [
                    'class' => '!text-green-500',
                ])
                ->slot('View')
                ->class('text-slate-500 flex gap-2 hover:text-slate-700 hover:bg-slate-100 font-bold p-1 px-2 rounded')
                ->dispatch('clickToEdit', ['dishId' => $row?->id, 'dishName' => $row?->name]),
        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
};

it('testings', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertHasAction('view')
        ->assertActionContainsAttribute('view', 'class', 'flex gap-2 hover:text-slate-700')
        ->assertActionContainsAttribute('view', 'wire:click', 'clickToEdit', ['dishId' => 29, 'dishName' => 'Luan'])
        ->assertActionHasIcon('view', 'default-eye', '!text-green-500')
        ->assertOk();
})->with([
    'tailwind'  => [$component::class, (object) ['theme' => Tailwind::class]],
    'bootstrap' => [$component::class, (object) ['theme' => Bootstrap5::class]],
]);
