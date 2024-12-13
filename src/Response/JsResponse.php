<?php

namespace PowerComponents\LivewirePowerGrid\Response;

use Illuminate\Http\Response;

final readonly class JsResponse
{
    public static function make(string $content): Response
    {
        return response($content)
                    ->withHeaders([
                        'Content-Type'            => 'application/javascript; charset=utf-8',
                        'Cache-Control'           => 'public, only-if-cached, max-age=31536000',
                        'Content-Security-Policy' => "default-src 'self'; script-src 'none';",
                    ]);
    }
}
