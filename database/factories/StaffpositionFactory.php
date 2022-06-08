<?php

namespace Database\Factories;

use App\Models\Compensationlevel;
use App\Models\Program;
use App\Models\Staffposition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staffposition>
 */
class StaffpositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->colorName(),
            'compensationlevel_id' => function () {
                return Compensationlevel::factory()->create()->id;
            },
            'program_id' => function () {
                return Program::factory()->create()->id;
            }
        ];
    }
}


