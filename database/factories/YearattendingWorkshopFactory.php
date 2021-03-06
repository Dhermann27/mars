<?php

namespace Database\Factories;

use App\Models\Workshop;
use App\Models\Yearattending;
use App\Models\YearattendingWorkshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<YearattendingWorkshop>
 */
class YearattendingWorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'yearattending_id' => function () {
                return Yearattending::factory()->create()->id;
            },
            'workshop_id' => function () {
                return Workshop::factory()->create()->id;
            },
        ];
    }
}


