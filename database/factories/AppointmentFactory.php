<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'time_slot_id' => null, // akan diisi dari seeder
            'status' => fake()->randomElement([
                \App\Enums\AppointmentStatusEnum::PENDING->value,
                \App\Enums\AppointmentStatusEnum::CONFIRMED->value,
                \App\Enums\AppointmentStatusEnum::COMPLETED->value,
                \App\Enums\AppointmentStatusEnum::CANCELLED->value
            ]),
            'notes' => fake()->sentence(),
        ];
    }
}
