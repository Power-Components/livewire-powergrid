<?php


namespace PowerComponents\LivewirePowerGrid\Traits;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;

trait Checkbox
{

    /**
     * @var bool
     */
    public bool $checkbox = false;
    /**
     * @var bool
     */
    public bool $checkbox_all = false;
    /**
     * @var array
     */
    public array $checkbox_values = [];
    /**
     * @var string
     */
    public string $checkbox_attribute;

    public function updatedCheckboxAll()
    {
        $this->checkbox_values = [];

        if ($this->checkbox_all) {
            $this->dataSource()->each(function($model) {
                $this->checkbox_values[] = (string) $model->{$this->checkbox_attribute};
            });
        }
    }

}
