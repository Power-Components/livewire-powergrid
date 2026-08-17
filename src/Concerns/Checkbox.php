<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Livewire\Attributes\Locked;
use PowerComponents\Turbine\Components\Rules\RuleManager;
use PowerComponents\Turbine\Support\Actions\ActionsResolver;
use stdClass;
use Throwable;

trait Checkbox
{
    public bool $checkbox = false;

    public bool $checkboxAll = false;

    /** @var list<string> */
    public array $checkboxValues = [];

    #[Locked]
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
            $value = data_get($model, $this->checkboxAttribute);

            $actionsResolver = new ActionsResolver($this);
            if (! $actionsResolver->isRowSelectable($model, RuleManager::TYPE_CHECKBOX)) {
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
