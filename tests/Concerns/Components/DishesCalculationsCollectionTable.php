<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Carbon, Collection, Number};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Column,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields
};

class DishesCalculationsCollectionTable extends PowerGridComponent
{
    public array $eventId = [];

    public bool $join = false;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
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

    public function query(): Builder
    {
        return Dish::with('category');
    }

    public function join(): Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select('dishes.*', 'categories.name as category_name');
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

            Column::make('ID', 'id')
                ->withCount('Count ID', true, false),

            Column::add()
                ->title('Name')
                ->field('name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Balance')
                ->field('balance')
                ->withSum('Sum Balance', true, false)
                ->withAvg('Avg Balance', true, false)
                ->withCount('Count Balance', true, false)
                ->withMin('Min Balance', true, false)
                ->withMax('Max Balance', true, false)
                ->sortable(),

            Column::add()
                ->title('Online')
                ->field('is_online'),

            Column::add()
                ->title('Created At')
                ->field('created_at_formatted'),
        ];
    }

    public function summarizeFormat(): array
    {
        $fmt = (new \NumberFormatter('pt-PT', \NumberFormatter::DEFAULT_STYLE));

        return [
            'id.{count}' => fn ($value) => $fmt->format($value) . ' item(s)',

            'balance.{sum,avg,min,max}' => function ($value) {
                return (new \NumberFormatter('en_US', \NumberFormatter::CURRENCY))
                    ->formatCurrency($value, 'USD');
            },
        ];
    }

    public function bootstrap(): void
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind(): void
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
