<?php

namespace Database\Factories;

use App\Models\Camper;
use App\Models\Charge;
use App\Models\Chargetype;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Charge>
 */
class ChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'camper_id' => function () {
                return Camper::factory()->create()->id;
            },
            'amount' => $this->faker->randomNumber(4),
            'memo' => $this->faker->sentence(),
            'chargetype_id' => function () {
                return Chargetype::factory()->create()->id;
            },
            'deposited_date' => $this->faker->date(),
            'timestamp' => $this->faker->date()
        ];
    }
}


