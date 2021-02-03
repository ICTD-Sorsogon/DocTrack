<?php

namespace Database\Seeders;

use DB;
use App\Models\Document;
use App\Models\Log;
use App\Models\TrackingRecord;
use App\Models\User;

use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $users = User::with('office')->where('role_id', 2)->get();
        foreach ($users as $user) {
            if ($user->id%2 == 0) {
                for ($i = 0; $i < 10; $i++) {
                    $document = Document::factory()->make();

                    $persistDocument = Document::factory()->create(
                        array_merge($document->toArray(), [
                        'originating_office' =>  $document->is_external ? $faker->company : $user->office->id,
                        'tracking_code' => $this->buildTrackingNumber(
                            $document->is_external,
                            $user->office->office_code,
                            $document->attachment_page_count,
                            $document->date_filed
                        )])
                    );
                    TrackingRecord::factory()->create([
                            'document_id' => $persistDocument->id,
                            'touched_by' => $user->id
                        ]);

                    Log::factory()->create([
                            'user_id' => $user->id,
                        ]);
                }
            }
            for ($i = 0; $i < 10; $i++) {
                $document = Document::factory()->make();
                $persistDocument = Document::factory()->create(
                    array_merge($document->toArray(), [
                        'originating_office' =>  $document->is_external ? $faker->company : $user->office->id,
                        'tracking_code' => $this->buildTrackingNumber(
                            $document->is_external,
                            $user->office->office_code,
                            $document->attachment_page_count,
                            $document->date_filed
                        )
                    ])
                );

                TrackingRecord::factory()->create(
                    [
                        'document_id' => $persistDocument->id,
                        'touched_by' => $user->id
                    ]
                );

                Log::factory()->create([
                        'user_id' => $user->id,
                    ]);
            }
        }
    }

    private function buildTrackingNumber($source, $office_code, $attachment, $date)
    {
        $origin = $source ? 'E' : 'I';
        $tracking = $origin.
            '-'.
            $office_code.
            '-'.
            date('YmdH').
            '-'.
            substr(str_shuffle("0123456789"), 0, 5).
            '-'.
            $attachment;
        return $tracking;
    }
}
