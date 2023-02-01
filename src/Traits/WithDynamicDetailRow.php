<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Collection as BaseCollection;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Column;

trait WithDynamicDetailRow
{
    public array $hiddenColumns = [];

    public int $screenWidth = 0;

    public ?array $responsiveMode = [];

    public function getDetailVisibleRowProperty(): BaseCollection
    {
        return collect($this->columns)
            ->filter(fn ($column) => filled(json_decode(strval(data_get($column, 'responsive')), true)))
            ->filter(function ($column) {
                $responsive = (array) json_decode(strval(data_get($column, 'responsive')), true);

                $isVisible = false;

                foreach ($responsive as $key => $value) {
                    $isVisible = is_numeric($key) && $key > $this->screenWidth;
                }

                return $isVisible;
            });
    }

    public function dynamicDetailRow(): void
    {
        $this->responsiveMode = [
            'name'             => 'detail',
            'view'             => 'livewire-powergrid::components.detail-row',
            'options'          => ['hiddenColumns' => $this->hiddenColumns],
            'state'            => [],
            'showCollapseIcon' => true,
            'viewIcon'         => '',
            'collapseOthers'   => false,
        ];

        data_set($this->setUp, 'detail', $this->responsiveMode);

        $this->dispatchBrowserEvent('addFlexToActions');
    }

    public function getDynamicDetailRowAttributes(\stdClass|Column $column): ComponentAttributeBag
    {
        $attributes = [];

        if (filled($column->responsive)) {
            $responsiveColumn = (array) json_decode($column->responsive, true);

            if ($responsiveColumn['default']) {
                $responsiveColumnAttributes = collect($responsiveColumn)
                    ->filter(fn (string $value, string $key) => $key !== 'x-responsive')
                    ->mapWithKeys(fn (string $value, string $key) => ['responsive-' . $key => $value])
                    ->toArray();

                $attributes = array_merge([
                    'column-field' => $column->field,
                    'column-title' => $column->title,
                    'component'    => $this->id,
                    'x-responsive' => $responsiveColumn['default'],
                    'target'       => $this->tableName,
                ], $responsiveColumnAttributes);
            }
        }

        return new ComponentAttributeBag($attributes);
    }
}
