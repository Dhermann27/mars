<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Program;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'building_id' => function () {
                return factory(Building::class)->create()->id;
            },
            'program_id' => function () {
                return factory(Program::class)->create()->id;
            },
            'min_occupancy' => 1,
            'max_occupancy' => 999,
            'rate' => $this->faker->randomFloat(2, 34, 500)
        ];
    }
}


