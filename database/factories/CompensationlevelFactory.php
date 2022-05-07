<?php

namespace Database\Factories;

use App\Models\Compensationlevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Compensationlevel>
 */
class CompensationlevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'max_compensation' => $this->faker->randomNumber(3)
        ];
    }
}


