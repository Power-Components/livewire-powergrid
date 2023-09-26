<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\View\ComponentAttributeBag;

class FilterBoolean extends FilterBase
{
    public string $trueLabel = 'Yes';

    public string $falseLabel = 'No';

    public function label(string $trueLabel, string $falseLabel): FilterBoolean
    {
        $this->trueLabel  = $trueLabel;
        $this->falseLabel = $falseLabel;

        return $this;
    }

    public static function getWireAttributes(string $field, string $title): array
    {
        return collect()
            ->put('selectAttributes', new ComponentAttributeBag([
                'wire:input.blur' => 'filterBoolean(\'' . $field . '\', $event.target.value, \'' . $title . '\')',
                'wire:model.blur' => 'filters.boolean.' . $field,
            ]))->toArray();
    }
}
