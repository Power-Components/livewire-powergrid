<?php

namespace PowerComponents\LivewirePowerGrid\Controllers;

use Illuminate\Http\Response;
use PowerComponents\LivewirePowerGrid\Response\JsResponse;

final class InjectJsController
{
    public function __invoke(): Response
    {
        $content = strval(file_get_contents(
            base_path('vendor/power-components/livewire-powergrid/dist/powergrid.js')
        ));

        return JsResponse::make($content);
    }
}
