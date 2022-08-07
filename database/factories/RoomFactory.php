<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'building_id' => function () {
                return Building::factory()->create()->id;
            },
            'room_number' => $this->faker->randomNumber(5),
            'capacity' => $this->faker->randomNumber(1),
            'is_workshop' => 0,
            'is_handicap' => 0,
            'xcoord' => $this->faker->unique()->randomNumber(2) * 6 + 1,
            'ycoord' => $this->faker->unique()->randomNumber(2) * 6 + 1,
            'pixelsize' => 5
        ];
    }
}


