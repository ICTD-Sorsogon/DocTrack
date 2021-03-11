<?php

namespace Database\Factories;

use App\Models\Log;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Log::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = ['create', 'edit', 'delete'];
        $remarks = ['Create New User', 'Edit User', 'Delete User',
            'Create Document', 'Delete Document', 'Edit Document'];

        return [
           'user_id' => null,
           'action' => $status[rand(0, 2)],
           'remarks' => $remarks[rand(0, 5)],
        ];
    }
}
