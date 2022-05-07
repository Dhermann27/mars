<?php

namespace Database\Factories;

use App\Models\Foodoption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Foodoption>
 */
class FoodoptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->title()
        ];
    }
}


