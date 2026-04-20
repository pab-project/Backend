<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecord\StoreMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    // POST /doctor/medical-records — dokter input rekam medis
    public function store(StoreMedicalRecordRequest $request)
    {
        $doctor      = $request->user()->doctor;
        $appointment = Appointment::findOrFail($request->appointment_id);

        // Pastikan appointment ini milik dokter yang sedang login
        if ($appointment->doctor_id !== $doctor->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses ke appointment ini.',
            ], 403);
        }

        // Pastikan appointment sudah selesai
        if ($appointment->status !== 'done') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rekam medis hanya bisa dibuat untuk appointment yang sudah selesai.',
            ], 422);
        }

        // Cek apakah sudah ada medical record untuk appointment ini
        if (MedicalRecord::where('appointment_id', $appointment->id)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rekam medis untuk appointment ini sudah ada.',
            ], 422);
        }

        $record = MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'doctor_id'      => $doctor->id,
            'diagnosis'      => $request->diagnosis,
            'treatment'      => $request->treatment,
            'medications'    => $request->medications,
            'notes'          => $request->notes,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rekam medis berhasil disimpan.',
            'data'    => new MedicalRecordResource($record->load(['patient.user', 'doctor.user'])),
        ], 201);
    }

    // GET /patient/medical-records — pasien lihat semua rekam medisnya
    public function myRecords(Request $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json(['status' => 'error', 'message' => 'Profil pasien tidak ditemukan.'], 404);
        }

        $records = MedicalRecord::with(['doctor.user', 'appointment.timeSlot'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return MedicalRecordResource::collection($records);
    }

    // GET /patient/medical-records/{id} — pasien lihat detail rekam medis
    public function show(Request $request, $id)
    {
        $patient = $request->user()->patient;

        $record = MedicalRecord::with(['doctor.user', 'patient.user', 'appointment.timeSlot'])
            ->where('patient_id', $patient->id)
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new MedicalRecordResource($record),
        ]);
    }

    // GET /doctor/medical-records — dokter lihat semua rekam medis yang pernah diinput
    public function doctorRecords(Request $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json(['status' => 'error', 'message' => 'Profil dokter tidak ditemukan.'], 404);
        }

        $records = MedicalRecord::with(['patient.user', 'appointment.timeSlot'])
            ->where('doctor_id', $doctor->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return MedicalRecordResource::collection($records);
    }

    // GET /admin/medical-records — admin lihat semua rekam medis
    public function index(Request $request)
    {
        $records = MedicalRecord::with(['patient.user', 'doctor.user', 'appointment.timeSlot'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return MedicalRecordResource::collection($records);
    }

    // GET /admin/medical-records/{id} — admin lihat detail rekam medis
    public function showAdmin($id)
    {
        $record = MedicalRecord::with(['patient.user', 'doctor.user', 'appointment.timeSlot'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new MedicalRecordResource($record),
        ]);
    }
}
