<?php

namespace Database\Factories;

use App\Models\Church;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Church>
 */
class ChurchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company(),
            'city' => $this->faker->city(),
            'province_id' => function () {
                return Province::factory()->create()->id;
            }
        ];
    }
}


