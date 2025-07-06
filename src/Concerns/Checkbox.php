<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use stdClass;
use Throwable;

trait Checkbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute = 'id';

    /**
     * @throws Exception|Throwable
     */
    public function selectCheckboxAll(): void
    {
        if (!$this->checkboxAll) {
            $this->checkboxValues = [];

            $this->dispatch('pgBulkActions::clear', $this->tableName);

            return;
        }

        /** @var AbstractPaginator $data */
        $data = $this->getRecords();

        if ($data->isEmpty()) {
            return;
        }

        /** @phpstan-ignore-next-line  */
        collect($data->items())->each(function (array|Model|stdClass $model) {
            $value = $model->{$this->checkboxAttribute};

            $hide = (bool) data_get(
                collect((array) $model->__powergrid_rules) //@phpstan-ignore-line
                    ->where('apply', true)
                    ->where('forAction', 'pg:checkbox')
                    ->last(),
                'hide',
            );

            $disable = (bool) data_get(
                collect((array) $model->__powergrid_rules) //@phpstan-ignore-line
                    ->where('apply', true)
                    ->where('forAction', 'pg:checkbox')
                    ->last(),
                'disable',
            );

            if ($hide || $disable) {
                return;
            }

            if (!in_array($value, $this->checkboxValues)) {
                $this->checkboxValues[] = (string) $value;

                $this->dispatch('pgBulkActions::addMore', [
                    'value'     => strval($value),
                    'tableName' => $this->tableName,
                ]);
            }
        });
    }

    public function showCheckBox(string $attribute = 'id'): self
    {
        $this->checkbox          = true;
        $this->checkboxAttribute = $attribute;

        return $this;
    }

    public function checkedValues(): array
    {
        return $this->checkboxValues;
    }
}
