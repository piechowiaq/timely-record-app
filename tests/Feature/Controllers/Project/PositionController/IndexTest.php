<?php

use App\Http\Resources\PositionResource;
use App\Models\Position;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('positions.index'))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('positions.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('positions.index'))
        ->assertComponent('Projects/Positions/Index');

});

it('passes projects positions as well as positions with project_id null', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Position::factory(2)->create(['project_id' => $user->project_id]);
    Position::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Position::factory(2)->create();

    $positions = Position::where('project_id', $user->project_id)->with('department')->orWhereNull('project_id')->get();

    actingAs($user)->
    get(route('positions.index'))
        ->assertHasPaginatedResource('positions',
            PositionResource::collection($positions));

});
