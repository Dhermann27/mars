<?php

namespace Database\Factories;

use App\Models\Pronoun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pronoun>
 */
class PronounFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->randomLetter(),
            'name' => $this->faker->word() . '/' . $this->faker->word(),
        ];
    }
}


