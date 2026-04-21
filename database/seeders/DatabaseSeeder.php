<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\TimeSlot;
use App\Models\MedicalRecord;
use App\Enums\RoleEnum;
use App\Enums\AppointmentStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Default ─────────────────────────────────────────
        User::create([
            'name'      => 'Admin Healthcare',
            'email'     => 'admin@healthcare.com',
            'password'  => Hash::make('password'),
            'role'      => RoleEnum::ADMIN->value,
            'is_active' => true,
        ]);

        // ── Dokter Contoh ─────────────────────────────────────────
        $doctorUser = User::create([
            'name'      => 'Dokter Test',
            'email'     => 'dokter@healthcare.com',
            'password'  => Hash::make('password'),
            'role'      => RoleEnum::DOCTOR->value,
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
            'role'      => RoleEnum::PATIENT->value,
            'is_active' => true,
        ]);

        Patient::create([
            'user_id'       => $patientUser->id,
            'date_of_birth' => '1995-06-15',
            'gender'        => \App\Enums\GenderEnum::MALE->value,
            'address'       => 'Jl. Contoh No. 1, Jakarta',
            'phone'         => '08222000002',
        ]);

        // RANDOM DUMMY DOKTER
        $doctorUsers = User::factory()->count(25)->create([
            'role' => RoleEnum::DOCTOR->value,
            'is_active' => true,
        ]);

        foreach ($doctorUsers as $user) {
            Doctor::factory()->create([
                'user_id' => $user->id
            ]);
        }

        // RANDOM DUMMY PASIEN
        $patientUsers = User::factory()->count(50)->create([
            'role' => RoleEnum::PATIENT->value,
            'is_active' => true,
        ]);

        foreach ($patientUsers as $user) {
            Patient::factory()->create([
                'user_id' => $user->id
            ]);
        }

        // RANDOM DUMMY TIMESLOT (Untuk semua dokter)
        $allDoctors = Doctor::all();
        foreach ($allDoctors as $doc) {
            for ($i = 0; $i < 5; $i++) {
                $startHour = rand(8, 15);
                $date = now()->addDays(rand(1, 30))->format('Y-m-d');
                TimeSlot::firstOrCreate(
                    ['doctor_id' => $doc->id, 'date' => $date, 'start_time' => sprintf('%02d:00:00', $startHour)],
                    [
                        'end_time' => sprintf('%02d:00:00', $startHour + 1),
                        'is_booked' => false,
                    ]
                );
            }
        }

        // RANDOM DUMMY APPOINTMENT
        $patients = Patient::all();

        foreach ($patients as $patient) {
            $appointmentCount = rand(1, 2);
            for ($j = 0; $j < $appointmentCount; $j++) {
                // Cari timeslot yg blm dibook
                $slot = TimeSlot::where('is_booked', false)->inRandomOrder()->first();
                if (!$slot) break; // Jika habis, skip

                DB::transaction(function () use ($patient, $slot) {
                    $appointment = Appointment::factory()->create([
                        'patient_id' => $patient->id,
                        'doctor_id' => $slot->doctor_id,
                        'time_slot_id' => $slot->id,
                    ]);

                    $slot->update(['is_booked' => true]);

                    if ($appointment->status === AppointmentStatusEnum::COMPLETED) {
                         MedicalRecord::factory()->create([
                             'appointment_id' => $appointment->id,
                             'patient_id' => $patient->id,
                             'doctor_id' => $slot->doctor_id,
                         ]);
                    }
                });
            }
        }
    }
}
