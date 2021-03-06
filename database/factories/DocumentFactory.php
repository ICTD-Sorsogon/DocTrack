<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Traits\TrackingNumberBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    use TrackingNumberBuilder;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $source = rand(0, 1);
        $status = ['created', 'received', 'forwarded', 'on hold', 'rejected', 'terminated', 'acknowledged'];

        return [
            'subject' => strtoupper($this->faker->realText(20)),
            'is_external' => $source,
            'document_type_id' => rand(1, 7),
            'originating_office' => $source ? rand(3, 12) : $this->faker->name,
            'destination_office_id' => [ rand(1, 19)],
            'sender_name' => $source == 0 ? rand(3, 12) : $this->faker->name,
            'priority_level' => rand(1,4),
            'page_count' => rand(1, 50),
            'status' => $status[rand(0,6)],
            'remarks' => $this->faker->realText(100),
            'attachment_page_count' => rand(1, 50),
        ];
    }
}
