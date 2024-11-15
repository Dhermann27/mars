<?php

namespace Database\Factories;

use App\Enums\Programname;
use App\Models\Camper;
use App\Models\Year;
use App\Models\Yearattending;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Yearattending>
 */
class YearattendingFactory extends Factory
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
            'year_id' => function () {
                return Year::factory()->create()->id;
            },
            'program_id' => Programname::Adult,
            'days' => 6,
        ];
    }
}


