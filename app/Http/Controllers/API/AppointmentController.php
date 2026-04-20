<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    // GET /admin/appointments — admin lihat semua appointment
    public function index(Request $request)
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'timeSlot'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return AppointmentResource::collection($appointments);
    }

    // PATCH /admin/appointments/{id}/approve — admin acc appointment
    public function approve($id)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user', 'timeSlot'])->findOrFail($id);

        if ($appointment->status !== 'pending') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya appointment dengan status pending yang bisa di-approve.',
            ], 422);
        }

        $appointment->update(['status' => 'approved']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment berhasil di-approve.',
            'data'    => new AppointmentResource($appointment),
        ]);
    }

    // PATCH /admin/appointments/{id}/reject — admin tolak appointment
    public function reject(Request $request, $id)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user', 'timeSlot'])->findOrFail($id);

        if ($appointment->status !== 'pending') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya appointment dengan status pending yang bisa ditolak.',
            ], 422);
        }

        // Kembalikan slot ke tidak-dipesan
        DB::transaction(function () use ($appointment, $request) {
            $appointment->update([
                'status' => 'rejected',
                'notes'  => $request->filled('reason') ? $appointment->notes . ' [Alasan Penolakan: ' . $request->reason . ']' : $appointment->notes,
            ]);
            $appointment->timeSlot->update(['is_booked' => false]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment berhasil ditolak.',
            'data'    => new AppointmentResource($appointment->fresh(['patient.user', 'doctor.user', 'timeSlot'])),
        ]);
    }

    // GET /patient/appointments — pasien lihat appointment miliknya
    public function myAppointments(Request $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json(['status' => 'error', 'message' => 'Profil pasien tidak ditemukan.'], 404);
        }

        $appointments = Appointment::with(['doctor.user', 'timeSlot'])
            ->where('patient_id', $patient->id)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(10);

        return AppointmentResource::collection($appointments);
    }

    // POST /patient/appointments — pasien buat appointment
    public function store(StoreAppointmentRequest $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json(['status' => 'error', 'message' => 'Profil pasien tidak ditemukan.'], 404);
        }

        $appointment = DB::transaction(function () use ($request, $patient) {
            $slot = TimeSlot::lockForUpdate()->findOrFail($request->time_slot_id);

            if ($slot->is_booked) {
                abort(422, 'Jadwal ini sudah dipesan oleh pasien lain.');
            }

            $slot->update(['is_booked' => true]);

            return Appointment::create([
                'patient_id'   => $patient->id,
                'doctor_id'    => $slot->doctor_id,
                'time_slot_id' => $slot->id,
                'status'       => 'pending',
                'notes'        => $request->notes,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment berhasil dibuat, menunggu konfirmasi admin.',
            'data'    => new AppointmentResource($appointment->load(['patient.user', 'doctor.user', 'timeSlot'])),
        ], 201);
    }

    // PATCH /patient/appointments/{id}/cancel — pasien batalkan appointment
    public function cancel(Request $request, $id)
    {
        $patient     = $request->user()->patient;
        $appointment = Appointment::where('patient_id', $patient->id)->findOrFail($id);

        if (!in_array($appointment->status, ['pending', 'approved'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Appointment tidak dapat dibatalkan.',
            ], 422);
        }

        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => 'cancelled']);
            $appointment->timeSlot->update(['is_booked' => false]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment berhasil dibatalkan.',
        ]);
    }

    // GET /doctor/appointments — dokter lihat jadwal appointment-nya
    public function doctorSchedule(Request $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json(['status' => 'error', 'message' => 'Profil dokter tidak ditemukan.'], 404);
        }

        $appointments = Appointment::with(['patient.user', 'timeSlot'])
            ->where('doctor_id', $doctor->id)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(10);

        return AppointmentResource::collection($appointments);
    }

    // PATCH /doctor/appointments/{id}/done — dokter tandai appointment selesai
    public function markDone(Request $request, $id)
    {
        $doctor      = $request->user()->doctor;
        $appointment = Appointment::where('doctor_id', $doctor->id)->findOrFail($id);

        if ($appointment->status !== 'approved') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya appointment yang sudah di-approve yang bisa ditandai selesai.',
            ], 422);
        }

        $appointment->update(['status' => 'done']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment ditandai selesai.',
            'data'    => new AppointmentResource($appointment->load(['patient.user', 'timeSlot'])),
        ]);
    }
}
