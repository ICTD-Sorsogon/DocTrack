<?php

namespace Tests\Feature;

use App\Models\{Traits\TrackingNumberBuilder, Document};
use Laravel\Sanctum\Sanctum;
use Tests\Support\SeedDatabaseAfterRefresh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DocumentFeatureTest extends TestCase
{
    //Important always include when inserting/retrieving data from database
    use RefreshDatabase, SeedDatabaseAfterRefresh, TrackingNumberBuilder;
    /**
     * @test
     * @covers \App\Http\Controllers\API\DocumentController::addNewDocument
     */
    public function can_create_docs()
    {
        $this->withoutExceptionHandling();
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);

        $data = \App\Models\Document::factory()->make()->only(
            ['subject','is_external', 'document_type_id', 'sender_name',
             'page_count', 'remarks', 'attachment_page_count']
        );
        $data['destination_office_id'] = [1];
        $this->assertAuthenticated();
        $response = $this->postJson('/api/add_new_document', $data);
        $tracking_code = $response->getOriginalContent()->tracking_code;
        $response->assertSuccessful();
        $response->assertJsonFragment(collect($data)->except(['is_external', 'is_terminal', 'originating_office','status', 'destination_office_id'])->toArray());
        unset($data['destination_office_id']);
        $this->assertDatabaseHas('documents',array_merge(compact('tracking_code'), $data));
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\DocumentController::addNewDocument
     */
    public function cannot_edit_not_owned_doc() 
    {
        $user = \App\Models\User::find(1);
        Sanctum::actingAs($user);
        $data = Document::firstWhere('originating_office','!=', $user->office_id);

        $this->assertAuthenticated();
        $response = $this->postJson('/api/add_new_document/'.$data->id, $data->toArray());
        $response->assertStatus(403);
    }
}
