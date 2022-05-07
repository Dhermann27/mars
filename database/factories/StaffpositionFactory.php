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
                return factory(Compensationlevel::class)->create()->id;
            },
            'program_id' => function () {
                return factory(Program::class)->create()->id;
            },
            'pctype' => 0
        ];
    }
}


