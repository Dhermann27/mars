<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Workshop;
use App\Models\Year;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReflectionClass;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ref = new ReflectionClass('App\Enums\Timeslotname');
        $slots = $ref->getConstants();

        return [
            'year_id' => function () {
                return Year::factory()->create()->id;
            },
            'room_id' => function () {
                return Room::factory()->create()->id;
            },
            'timeslot_id' => $slots[array_rand($slots)],
            'sequence' => $this->faker->numberBetween(2, 99),
            'name' => $this->faker->catchPhrase(),
            'led_by' => $this->faker->name(),
            'blurb' => $this->faker->paragraph(),
            'm' => $this->faker->boolean(),
            't' => $this->faker->boolean(),
            'w' => $this->faker->boolean(),
            'th' => $this->faker->boolean(),
            'f' => $this->faker->boolean(),
            'capacity' => $this->faker->randomNumber(2) + 1,
            'fee' => $this->faker->randomFloat(2, 0, 100.0)
        ];
    }
}


