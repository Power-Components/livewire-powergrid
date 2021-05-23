<?php

use PowerComponents\LivewirePowerGrid\PowerGridComponent;

beforeEach(function () {
    $this->component = new PowerGridComponent;
});

it('properly paginates data', function () {
    $this->assertFalse($this->component->perPageInput);

    $this->component->showPerPage(25);

    $this->assertTrue($this->component->perPageInput);
    $this->assertEquals(25, $this->component->perPage);
});

it('does not set a pagination value that is not supported', function () {
    $this->assertFalse($this->component->perPageInput);

    $this->component->showPerPage(22);

    $this->assertFalse($this->component->perPageInput);
    $this->assertEquals(10, $this->component->perPage);
});
