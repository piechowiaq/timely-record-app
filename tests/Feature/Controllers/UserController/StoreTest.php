<?php

use function Pest\Laravel\post;

it('requires authentication', function () {

    post(route('users.store'))
        ->assertRedirect(route('login'));

});

//it('can store a user', function () {
//
//});
//
//it('redirects to user index page', function () {
//
//});
//
//it('can store a user with valid workspaces', function () {
//
//});
//
//it('can store a user with valid role', function () {
//
//});
