<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use PowerComponents\LivewirePowerGrid\Column;

class Start
{
    public function summarize(): void
    {
        Column::macro('withSum', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.sum.label', $label);
            data_set($this->properties, 'summarize.sum.header', $header);
            data_set($this->properties, 'summarize.sum.footer', $footer);

            return $this;
        });

        Column::macro('withCount', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.count.label', $label);
            data_set($this->properties, 'summarize.count.header', $header);
            data_set($this->properties, 'summarize.count.footer', $footer);

            return $this;
        });

        Column::macro('withAvg', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.avg.label', $label);
            data_set($this->properties, 'summarize.avg.header', $header);
            data_set($this->properties, 'summarize.avg.footer', $footer);

            return $this;
        });

        Column::macro('withMin', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.min.label', $label);
            data_set($this->properties, 'summarize.min.header', $header);
            data_set($this->properties, 'summarize.min.footer', $footer);

            return $this;
        });

        Column::macro('withMax', function (string $label, bool $header, bool $footer) {
            data_set($this->properties, 'summarize.max.label', $label);
            data_set($this->properties, 'summarize.max.header', $header);
            data_set($this->properties, 'summarize.max.footer', $footer);

            return $this;
        });
    }
}
