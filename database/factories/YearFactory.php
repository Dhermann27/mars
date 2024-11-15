<?php

namespace Database\Factories;

use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Year>
 */
class YearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $thisyear = $this->faker->unique()->year;
        return [
            'year' => $thisyear,
            'checkin' => Carbon::parse('first Sunday of July ' . $thisyear)->toDateString(),
            'brochure' => Carbon::parse('first day of February ' . $thisyear)->toDateString(),
            'is_current' => '1',
            'is_brochure' => '1',
            'can_register' => '1',
            'can_accept_paypal' => '1',
            'can_workshop_select' => '1',
            'can_room_select' => '1',
            'is_calendar' => '1',
            'is_workshop_proposal' => '1',
            'is_artfair' => '1',
            'is_coffeehouse' => '1'
        ];
    }
}


