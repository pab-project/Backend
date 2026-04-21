<?php

namespace Database\Factories;

use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeSlot>
 */
class TimeSlotFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 15);
        
        return [
            'doctor_id' => null, // diisi dari seeder
            'date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $startHour + 1),
            'is_booked' => false,
        ];
    }
}
