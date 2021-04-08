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

    /**
     * @param string $attribute
     * @return PowerGridComponent
     */
    public function showCheckBox(string $attribute = 'id'): PowerGridComponent
    {
        $this->checkbox = true;
        $this->checkbox_attribute = $attribute;
        return $this;
    }

    public function updatedCheckboxAll()
    {
        $this->checkbox_values = [];

        if ($this->checkbox_all) {
            collect($this->dataSource())->each(fn( $model) => $this->checkbox_values[] = (string)$model->{$this->checkbox_attribute});
        }
    }

}
