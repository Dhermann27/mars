<?php

namespace Database\Factories;

use App\Models\Chargetype;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chargetype>
 */
class ChargetypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company()
        ];
    }
}


