<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Family>
 */
class FamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'address1' => $this->faker->streetAddress(),
            'address2' => $this->faker->secondaryAddress(),
            'city' => $this->faker->city(),
            'province_id' => function () {
                return factory(Province::class)->create()->id;
            },
            'zipcd' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'is_address_current' => 1,
            'is_ecomm' => rand(0, 1),
            'is_scholar' => 0
        ];
    }
}


