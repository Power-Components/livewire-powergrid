<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

interface ExportInterface
{
    public function download();

    public function build();
}
