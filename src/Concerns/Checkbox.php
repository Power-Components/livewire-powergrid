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

    /** @var list<string> */
    public array $checkboxValues = [];

    public string $checkboxAttribute = 'id';

    /**
     * @throws Exception|Throwable
     */
    public function selectCheckboxAll(): void
    {
        if (! $this->checkboxAll) {
            $this->checkboxValues = [];

            $this->dispatch('pgBulkActions::clear', $this->tableName);

            return;
        }

        /** @var AbstractPaginator<int, mixed> $records */
        $records = $this->records();

        if ($records->isEmpty()) {
            return;
        }

        /** @phpstan-ignore-next-line  */
        collect($records->items())->each(function (array|Model|stdClass $model) {
            $value = $model->{$this->checkboxAttribute};

            $checkboxRule = collect((array) data_get($model, '__powergrid_rules'))
                ->where('apply', true)
                ->where('forAction', 'pg:checkbox')
                ->last();

            if ((bool) data_get($checkboxRule, 'hide') || (bool) data_get($checkboxRule, 'disable')) {
                return;
            }

            if (! in_array($value, $this->checkboxValues)) {
                $this->checkboxValues[] = (string) $value;

                $this->dispatch('pgBulkActions::addMore', [
                    'value' => strval($value),
                    'tableName' => $this->tableName,
                ]);
            }
        });
    }

    public function showCheckBox(string $attribute = 'id'): self
    {
        $this->checkbox = true;
        $this->checkboxAttribute = $attribute;

        return $this;
    }

    /** @return list<string> */
    public function checkedValues(): array
    {
        return $this->checkboxValues;
    }
}
