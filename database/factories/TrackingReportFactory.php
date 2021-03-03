<?php

namespace Database\Factories;

use App\Models\TrackingReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrackingReport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           'office_id' => rand(1,37)
        ];
    }
}
