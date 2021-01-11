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
        return [
           'document_id' => null,
           'actions' => 10,
           'status' => 20,
           'touched_by' => null,
           'last_touched' => Carbon::createFromTimeStamp($this->faker->dateTimeBetween('-360 days', 'now')->getTimestamp()),
           'remarks' => 'generated by document creation'
        ];
    }
}