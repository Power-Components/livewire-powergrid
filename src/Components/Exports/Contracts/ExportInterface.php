<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\Contracts;

use PowerComponents\LivewirePowerGrid\{Exportable, PowerGridComponent};
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ExportInterface
{
    public function download(array $exportOptions, PowerGridComponent $powerGridComponent): BinaryFileResponse;

    public function build(Exportable|array $exportOptions, PowerGridComponent $powerGridComponent): void;
}
