<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\SeedDatabaseAfterRefresh;
use Tests\TestCase;

class AccountFeatureTest extends TestCase
{

    use RefreshDatabase, SeedDatabaseAfterRefresh;//Important always include when inserting/retrieving data from database

    /** @test */
    public function authenticated_test()
    {
        //Select user for login in
        $user = \App\Models\User::find(1);

        //Login the selected user
        Sanctum::actingAs($user);

        //check if user is login
        $this->assertAuthenticated();
    }

    /** @test */
    public function can_create_user()
    {
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);
        $this->assertAuthenticated();
        $data = \App\Models\User::factory()->make();
        $response = $this->postJson('/api/add_new_user', $data->toArray());
        $response->assertSuccessful();
    }

    /** @test */
    public function can_edit_user()
    {
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);
        $data = \App\Models\User::find(2);
        $data->first_name = 'asd';
        $this->assertAuthenticated();
        $response = $this->postJson('/api/update_existing_user', $data->toArray());
        $response->assertSuccessful();
    }

    /** @test */
    public function can_delete_user()
    {
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);
        $data = \App\Models\User::find(2);
        $this->assertAuthenticated();
        $response = $this->postJson('/api/delete_existing_user/'.$data->id);
        $this->assertSoftDeleted($data);
    }




}
