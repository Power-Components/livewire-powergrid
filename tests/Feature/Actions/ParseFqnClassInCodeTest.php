<?php

use PowerComponents\LivewirePowerGrid\Actions\ParseFqnClassInCode;

it('can find the namespace in a PHP file source code', function () {
    $code = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoModel extends Model
{
    use HasFactory;
}
EOD;

    expect(ParseFqnClassInCode::handle($code))->toBe('App\Models\DemoModel');
});

it('throws an exception when namespace cannot be found', function () {
    ParseFqnClassInCode::handle('foobar');
})->throws('could not find a FQN Class is source-code');
