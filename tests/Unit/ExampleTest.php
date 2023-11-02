<?php

test('that true is true', function () {
    expect(true)->toBeTrue(); //PEST Syntax
    $this->assertTrue(true); //PHPUnit Syntax
});
