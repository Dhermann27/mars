<?php

namespace Database\Factories;

use App\Models\Contactbox;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contactbox>
 */
class ContactboxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'emails' => $this->faker->safeEmail()
        ];
    }
}


