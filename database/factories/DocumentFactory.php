<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
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
        $attachmentPageCount = rand(1,50);
        $source = rand(0, 1);

        return [
            'subject' => strtoupper($this->faker->realText(20)),
            'is_external' => $source == 0 ? false : true,
            'document_type_id' => rand(1,7),
            'originating_office' => $source == 0 ? rand(1, 3) : $this->faker->company,
            'current_office_id' => null,
            'sender_name' => $source == 0 ? rand(3, 12) : $this->faker->name,
            'page_count' => rand(1,50),
            'date_filed' => $dateFilled,
            'is_terminal' => $source == 0 ? false : true,
            'remarks' => $this->faker->realText(100),
            'attachment_page_count' => $attachmentPageCount,
            'tracking_code' => null
        ];
    }
}
