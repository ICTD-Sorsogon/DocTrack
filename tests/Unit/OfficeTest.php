<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\SeedDatabaseAfterRefresh;
use Tests\TestCase;

class OfficeTest extends TestCase
{
    use RefreshDatabase, SeedDatabaseAfterRefresh;//Important always include when inserting/retrieving data from database
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\api\OfficeController::addNewOffice
     */
    public function can_create_office()
    {
        //Select user for login in
        $user = \App\Models\User::find(1);
        //Login the selected user
        Sanctum::actingAs($user);
        //Generate data for test submittions
        $data = \App\Models\Office::factory()->create()->only(
            [ 'name', 'address', 'office_code', 'contact_number', 'contact_email']
        );
        //check if user is login
        $this->assertAuthenticated();
        //Send data to route
        $response = $this->postJson('/api/add_new_office', $data);
        //check if POST is successful
        $response->assertSuccessful();
        //check if details exist on response
        $response->assertJsonFragment(collect($data)->toArray());
        //check if data exist on the database
        $this->assertDatabaseHas('offices', $data);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\api\OfficeController::updateExistingOffice
     */
    public function can_edit_office()
    {
        $old_office = \App\Models\Office::find(1);
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);
        $this->assertAuthenticated();
        $office = [
            "id" =>$old_office->id,
            "name" => "ICT",
            "address" => "Capitol Compound, Burabod, Sorsogon City, Sorsogon",
            "office_code" => "GO",
            "contact_number" => "09613551323",
            "contact_email" => "governor@sorsogon.gov.ph"
        ];
        $response = $this->post('/api/update_existing_office', $office);
        $response->assertSuccessful();
        $response->assertJsonFragment(collect($office)->toArray());
    }

    /**
     * @test
     * @covers \App\Http\Controllers\api\OfficeController::deleteOffice
     */
    public function can_delete_office()
    {
        $office = \App\Models\Office::factory()->create();
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);
        $this->assertAuthenticated();
        $response = $this->postJson('/api/delete_office/'.$office->id);
        $this->assertDeleted($office);
    }

}
