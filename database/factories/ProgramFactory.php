<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'title' => $this->faker->company(),
            'order' => 1,
            'blurb' => $this->faker->paragraph(),
            'letter' => implode('<br />', $this->faker->paragraphs()),
            'covenant' => $this->faker->paragraph()
        ];
    }
}


