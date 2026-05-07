<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\ThemeBuilder;

class Base extends Theme
{
    public function struct(): ThemeBuilder
    {
        return ThemeBuilder::make($this->name())
            ->header([
                'layout' => [
                    'container' => 'd-flex flex-column flex-md-row justify-content-between align-items-center mb-3',
                    'sub_container' => 'd-flex flex-row gap-2',
                ],
            ])
            ->table([
                'layout' => [
                    'container' => 'table-responsive',
                    'table' => 'table table-bordered table-striped',
                    'thead' => 'table-light',
                ],
            ])
            ->footer([
                'layout' => [
                    'container' => 'd-flex justify-content-between align-items-center mt-3',
                    'select' => 'form-select w-auto',
                ],
            ]);
    }
}
