<?php

use App\Models\Room;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'room_id' => function () {
                return factory(Room::class)->create()->id;
            },
            'timeslot_id' => $slots[array_rand($slots)],
            'order' => $this->faker->randomNumber(2),
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


