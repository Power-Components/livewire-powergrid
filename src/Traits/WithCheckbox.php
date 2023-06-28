<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use PowerComponents\LivewirePowerGrid\Helpers\ActionRules;
use Throwable;

trait WithCheckbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute = '';

    /**
     * @throws Exception|Throwable
     */
    public function selectCheckboxAll(): void
    {
        if (!$this->checkboxAll) {
            $this->checkboxValues = [];

          //  $this->dispatchBrowserEvent('pgBulkActions::clear', $this->tableName);

            return;
        }

        /** @var AbstractPaginator $data */
        $data = $this->fillData();

        $actionRulesClass = resolve(ActionRules::class);

        /** @phpstan-ignore-next-line  */
        collect($data->items())->each(function (array|Model|\stdClass $model) use ($actionRulesClass) {
            $rules = $actionRulesClass->recoverFromAction('pg:checkbox', $model);

            if (isset($rules['hide'])) {
                return;
            }
            $value = $model->{$this->checkboxAttribute};

            if (!in_array($value, $this->checkboxValues)) {
                $this->checkboxValues[] = (string) $value;

                $this->dispatchBrowserEvent('pgBulkActions::addMore', [
                    'value'     => $value,
                    'tableName' => $this->tableName,
                ]);
            }
        });
    }
}
