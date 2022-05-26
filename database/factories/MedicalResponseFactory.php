<?php

namespace Database\Factories;

use App\Models\Medicalresponse;
use App\Models\Year;
use App\Models\Yearattending;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalResponse>
 */
class MedicalResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $year = date("Y") - Year::where('is_current', '1')->first()->year;
        return [
            'yearattending_id' => function () {
                return Yearattending::factory()->create()->id;
            },
            'parent_name' => $this->faker->name,
            'youth_sponsor' => $this->faker->name,
            'mobile_phone' => $this->faker->phoneNumber,
            'concerns' => $this->faker->paragraph,
            'doctor_name' => $this->faker->name,
            'doctor_nbr' => $this->faker->phoneNumber,
            'is_insured' => 1,
            'holder_name' => $this->faker->name,
            'holder_birthday' => $this->faker->dateTimeBetween('-' . (100 + $year) . ' years', '-' . (19 + $year) . ' years')->format('Y-m-d'),
            'carrier' => $this->faker->company,
            'carrier_nbr' => $this->faker->phoneNumber,
            'carrier_id' => $this->faker->randomNumber(8),
            'carrier_group' => $this->faker->randomNumber(6),
            'is_epilepsy' => $this->faker->boolean(),
            'is_diabetes' => $this->faker->boolean(),
            'is_add' => $this->faker->boolean(),
            'is_adhd' => $this->faker->boolean()
        ];
    }
}
