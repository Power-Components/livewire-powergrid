<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\{Button, Column, Exportable, Footer, Header, PowerGrid, PowerGridComponent, PowerGridFields};

final class CypressTable extends PowerGridComponent
{
    use WithExport;

    public ?string $dynamicRules = null;

    public bool $applyRules = false;

    #[Url]
    public string $ruleType = 'rows'; // rows, checkbox, radio,

    #[Url]
    public string $testType = 'rules'; // rules, filters

    public function setUp(): array
    {
        if ($this->ruleType === 'radio') {
            $this->showRadioButton();
        }

        if ($this->ruleType === 'checkbox') {
            $this->showCheckBox();
        }

        $headerTest = match ($this->testType) {
            'filters' => [
                Header::make()
                    ->includeViewOnTop('components.header.filters-controllers')
                    ->showSearchInput(),
            ],
            default => [
                Header::make()
                    ->includeViewOnTop('components.header.rules-controllers')
                    ->showSearchInput(),
            ],
        };

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            ...$headerTest,
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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            /** Example of custom column using a closure **/
            ->add('name_lower', fn (User $model) => strtolower(e($model->name)))
            ->add('email')
            ->add('created_at_formatted', fn (User $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
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

    public function filters(): array
    {
        if ($this->testType == 'rules') {
            return [];
        }

        return [
            Filter::inputText('name'),
            Filter::inputText('email'),
            Filter::number('id'),
            Filter::datetimepicker('created_at_formatted', 'created_at')
                ->params([
                    'timezone' => 'America/Sao_Paulo',
                ]),
        ];
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
