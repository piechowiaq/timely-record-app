<?php

use App\Models\Project;

use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('users.index', Project::factory()->create()))
        ->assertRedirect(route('login'));

});
