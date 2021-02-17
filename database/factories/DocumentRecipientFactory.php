<?php

namespace Database\Factories;

use App\Models\DocumentRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRecipientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentRecipient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'document_id' => rand(1,10),
            'destination_office' => rand(1,37),
            'acknowledged' => rand(0,1),
            'received' => rand(0,1),
            'forwarded' => rand(0,1),
            'rejected' => rand(0,1),
            'hold' => rand(0,1)
        ];
    }
}
