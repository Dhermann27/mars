<?php

namespace Database\Factories;

use App\Models\Staffposition;
use App\Models\Yearattending;
use App\Models\YearattendingStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<YearattendingStaff>
 */
class YearattendingStaffFactory extends Factory
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
            'staffposition_id' => function () {
                return Staffposition::factory()->create()->id;
            },
        ];
    }
}


