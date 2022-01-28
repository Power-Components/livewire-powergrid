<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ExportInterface
{
    public function download(bool $deleteFileAfterSend): BinaryFileResponse;

    public function build(): void;
}
