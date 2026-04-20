<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Default ─────────────────────────────────────────
        User::create([
            'name'      => 'Admin Healthcare',
            'email'     => 'admin@healthcare.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ── Dokter Contoh ─────────────────────────────────────────
        $doctorUser = User::create([
            'name'      => 'Dr. Budi Santoso',
            'email'     => 'dokter@healthcare.com',
            'password'  => Hash::make('password'),
            'role'      => 'doctor',
            'is_active' => true,
        ]);

        Doctor::create([
            'user_id'        => $doctorUser->id,
            'specialization' => 'Dokter Umum',
            'phone'          => '08111000001',
            'bio'            => 'Dokter umum berpengalaman dengan 10 tahun pengalaman.',
        ]);

        // ── Pasien Contoh ─────────────────────────────────────────
        $patientUser = User::create([
            'name'      => 'Pasien Test',
            'email'     => 'pasien@healthcare.com',
            'password'  => Hash::make('password'),
            'role'      => 'patient',
            'is_active' => true,
        ]);

        Patient::create([
            'user_id'       => $patientUser->id,
            'date_of_birth' => '1995-06-15',
            'gender'        => 'Laki-laki',
            'address'       => 'Jl. Contoh No. 1, Jakarta',
            'phone'         => '08222000002',
        ]);
    }
}
