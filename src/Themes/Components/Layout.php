<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Layout
{
    public ?string $table = '';

    public string $header = '';

    public string $pagination = '';

    public string $message = '';

    public string $footer = '';

    public function table(string $path): Layout
    {
        $this->table    = $path;

        return $this;
    }

    public function header(string $path): Layout
    {
        $this->header    = $path;

        return $this;
    }

    public function pagination(string $path): Layout
    {
        $this->pagination    = $path;

        return $this;
    }

    public function message(string $path): Layout
    {
        $this->message    = $path;

        return $this;
    }

    public function footer(string $path): Layout
    {
        $this->footer    = $path;

        return $this;
    }
}
