<?php

namespace Database\Factories;

use App\Models\TrackingRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrackingRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = ['created', 'received', 'forwarded', 'on hold',
        'rejected', 'terminated', 'acknowledged', 'date changed', 'edited'];
        $from = ['docket office' , 'personal', 'email', 'others'];

        return [
           'document_id' => null,
           'action' => $status[rand(0, 8)],
           'through' => $from[rand(0,3)],
           'touched_by' => null,
           'last_touched' => Carbon::createFromTimeStamp($this->faker->dateTimeBetween('-360 days', 'now')
                ->getTimestamp()),
           'remarks' => 'generated by document creation'
        ];
    }
}
