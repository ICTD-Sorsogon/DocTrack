<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Traits\TrackingNumberBuilder;
use App\Models\User;
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
        $dateFilled = Carbon::createFromTimeStamp($this->faker->dateTimeBetween('-360 days', 'now')->getTimestamp());
        $attachmentPageCount = rand(1, 50);
        $source = rand(0, 1);
        $status = ['created', 'received', 'forwarded', 'processing', 'on hold', 'rejected', 'terminated', 'acknowledged'];

        return [
            'subject' => strtoupper($this->faker->realText(20)),
            'is_external' => $source,
            'document_type_id' => rand(1, 7),
            'originating_office' => $source ? rand(3, 12) : $this->faker->name,
            'destination_office_id' => rand(1, 26),
            'sender_name' => $source == 0 ? rand(3, 12) : $this->faker->name,
            'page_count' => rand(1, 50),
            'is_terminal' => 0,
            'status' => $status[rand(0,7)],
            'remarks' => $this->faker->realText(100),
            'attachment_page_count' => $attachmentPageCount,
        ];
    }
}
