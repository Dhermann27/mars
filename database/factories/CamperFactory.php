<?php

namespace Database\Factories;

use App\Models\Camper;
use App\Models\Church;
use App\Models\Family;
use App\Models\Foodoption;
use App\Models\Pronoun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Camper>
 */
class CamperFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $year = date("Y") - Year::where('is_current', '1')->first()->year;
        return [
            'pronoun_id' => function () {
                return factory(Pronoun::class)->create()->id;
            },
            'family_id' => function () {
                return factory(Family::class)->create()->id;
            },
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'phonenbr' => $this->faker->regexify('[1-9]\d{9}'),
            'birthdate' => $this->faker->dateTimeBetween('-' . (100 + $year) . ' years', '-' . (19 + $year) . ' years')->format('Y-m-d'),
            'church_id' => function () {
                return factory(Church::class)->create()->id;
            },
            'is_handicap' => 0,
            'foodoption_id' => function () {
                return factory(Foodoption::class)->create()->id;
            }
        ];
    }
}


