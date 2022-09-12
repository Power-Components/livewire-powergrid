<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ExportInterface
{
    public function download(array $exportOptions): BinaryFileResponse;

    public function build(array $exportOptions): void;
}
