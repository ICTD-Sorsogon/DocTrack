<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\SeedDatabaseAfterRefresh;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase, SeedDatabaseAfterRefresh;//Important always include when inserting/retrieving data from database
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

      /**
     * @test
     * @covers \App\Http\Controllers\API\DocumentController::addNewDocument
     */
    public function can_create_docs()
    {
        //Select user for login in
        $user = \App\Models\User::find(1);
        //Login the selected user
        Sanctum::actingAs($user);
        //Generate data for test submittions
        $data = \App\Models\Document::factory()->create()->only(
            [ 'subject', 'is_external', 'document_type_id', 'destination_office_id', 'sender_name', 'page_count',
            'remarks' , 'attachment_page_count']
        );

        //check if user is login
        $this->assertAuthenticated();
        //Send data to route
        $response = $this->postJson('/api/add_new_document', $data);
        //check if POST is successful
        $response->assertSuccessful();
        //check if details exist on response
        $response->assertJsonFragment(collect($data)->except('is_external')->toArray());
        //check if data exist on the database
        $this->assertDatabaseHas('documents', $data);
    }
}
