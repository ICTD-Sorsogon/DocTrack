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
                    $persistDocument = Document::withoutEvents(function() use ($faker, $user){ 
                        $source = rand(0,1);
                        return  Document::factory()->create(
                        [
                            'is_external' => $source,
                            'originating_office' =>  $source ? $faker->company : $user->office->id,
                            'sender_name' => $source ?  $faker->name : rand(3, 12),
                            'tracking_code' => $this->buildTrackingNumber(
                                $source,
                                $user->office->office_code,
                                rand(1,50)
                            )
                        ]);
                });

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
                $persistDocument = Document::withoutEvents(function() use ($faker, $user){ 
                    $source = rand(0,1);
                    return  Document::factory()->create(
                        [
                            'is_external' => $source,
                            'originating_office' =>  $source ? $faker->company : $user->office->id,
                            'sender_name' => $source ?  $faker->name : rand(3, 12),
                            'tracking_code' => $this->buildTrackingNumber(
                                $source,
                                $user->office->office_code,
                                rand(1,50)
                            )
                        ]);
            });

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

    
    private function buildTrackingNumber($source, $office_code, $attachment)
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
