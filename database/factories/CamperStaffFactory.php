<?php

namespace Database\Factories;

use App\Models\Camper;
use App\Models\CamperStaff;
use App\Models\Staffposition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CamperStaff>
 */
class CamperStaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'camper_id' => function () {
                return Camper::factory()->create()->id;
            },
            'staffposition_id' => function () {
                return Staffposition::factory()->create()->id;
            },
        ];
    }
}
