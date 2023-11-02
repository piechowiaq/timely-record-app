<?php

use function Pest\Laravel\get;

it('gives back successful response for home page', function () {
    get('/')->assertStatus(200);
});
