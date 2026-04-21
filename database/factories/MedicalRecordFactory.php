<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => null, // diisi dari seeder
            'patient_id' => null,     // diisi dari seeder
            'doctor_id' => null,      // diisi dari seeder
            'diagnosis' => fake()->sentence(4),
            'treatment' => fake()->sentence(6),
            'medications' => fake()->randomElements([
                ['name' => 'Paracetamol 500mg', 'dosage' => '3x sehari'],
                ['name' => 'Amoxicillin 500mg', 'dosage' => '3x sehari sesudah makan'],
                ['name' => 'Ibuprofen 400mg', 'dosage' => '3x sehari sesudah makan'],
                // ['name' => 'Amoxicillin 500mg', 'dosage' => '3x sehari sesudah makan'],
                // ['name' => 'Obat Batuk Hitam', 'dosage' => '3x sehari sesudah makan'],
                // ['name' => 'Amoxicillin 500mg', 'dosage' => '3x sehari sesudah makan'],
            ]),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
