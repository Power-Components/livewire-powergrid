<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\{Button, Column, Exportable, Footer, Header, PowerGrid, PowerGridColumns, PowerGridComponent};

final class CypressTable extends PowerGridComponent
{
    use WithExport;

    public ?string $dynamicRules = null;

    public bool $applyRules = false;

    #[Url]
    public int $rule = 1;

    public function setUp(): array
    {
        if ($this->rule === 4) {
            $this->showRadioButton();
        }

        if ($this->rule === 3) {
            $this->showCheckBox();
        }

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()
                ->includeViewOnTop('components.header.rules-controllers')
                ->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name')
            /** Example of custom column using a closure **/
            ->addColumn('name_lower', fn (User $model) => strtolower(e($model->name)))
            ->addColumn('email')
            ->addColumn('created_at_formatted', fn (User $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::action('Action'),
        ];
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id]),
        ];
    }

    public function applyRules(): void
    {
        $this->applyRules = true;
    }

    public function actionRules($row): array
    {
        $apply = null;

        if ($this->dynamicRules && $this->applyRules) {
            eval('$apply = [' . $this->dynamicRules . '];');

            return $apply;
        }

        return [];
    }
}
