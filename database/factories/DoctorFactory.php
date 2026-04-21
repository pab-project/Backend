<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'specialization' => fake()->randomElement([
                'Dokter Umum',
                'Dokter Gigi',
                'Dokter Anak',
                'Dokter Kandungan',
                'Dokter Spesialis Penyakit Dalam',
                'Dokter Spesialis Bedah',
                'Dokter Spesialis Saraf',
                'Dokter Spesialis Mata',
                'Dokter Spesialis THT',
                'Dokter Spesialis Kulit dan Kelamin',
                'Dokter Spesialis Jantung',
                'Dokter Spesialis Paru',
                'Dokter Spesialis Orthopedi',
                'Dokter Spesialis Urologi',
                'Dokter Spesialis Rehabilitasi Medik',
                'Dokter Spesialis Kedokteran Jiwa',
                'Dokter Spesialis Radiologi',
                'Dokter Spesialis Patologi Klinik',
                'Dokter Spesialis Patologi Anatomi',
                'Dokter Spesialis Anestesiologi',
                'Dokter Spesialis Forensik',
                'Dokter Spesialis Kedokteran Olahraga',
                'Dokter Spesialis Kedokteran Tropik Infeksi',
                'Dokter Spesialis Kedokteran Forensik dan Medikolegal',
                'Dokter Spesialis Kedokteran Forensik dan Medikolegal',
            ]),
            'phone'=> fake()->phoneNumber(),
            'bio' => fake()->sentence(),
        ];
    }
}
