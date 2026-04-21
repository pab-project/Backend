<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeSlot\StoreTimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    // GET /doctor/slots — dokter lihat semua jadwal miliknya
    public function index(Request $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json(['status' => 'error', 'message' => 'Profil dokter tidak ditemukan.'], 404);
        }

        $slots = $doctor->timeSlots()
            ->when($request->filled('date'), fn($q) => $q->whereDate('date', $request->date))
            ->when($request->filled('is_booked'), fn($q) => $q->where('is_booked', filter_var($request->is_booked, FILTER_VALIDATE_BOOLEAN)))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return TimeSlotResource::collection($slots);
    }

    // POST /doctor/slots — dokter tambah jadwal baru
    public function store(StoreTimeSlotRequest $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json(['status' => 'error', 'message' => 'Profil dokter tidak ditemukan.'], 404);
        }

        // Cek apakah slot sudah ada (unique constraint sudah di DB, ini untuk pesan yang lebih ramah)
        $exists = TimeSlot::where('doctor_id', $doctor->id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Jadwal pada waktu tersebut sudah ada.',
            ], 422);
        }

        $slot = TimeSlot::create([
            'doctor_id'  => $doctor->id,
            'date'       => $request->date,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'is_booked'  => false,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Jadwal berhasil ditambahkan.',
            'data'    => new TimeSlotResource($slot),
        ], 201);
    }

    // PUT /doctor/slots/{id} — dokter edit jadwal (hanya yang belum dipesan)
    public function update(StoreTimeSlotRequest $request, $id)
    {
        $doctor = $request->user()->doctor;
        $slot   = TimeSlot::where('doctor_id', $doctor->id)->findOrFail($id);

        if ($slot->is_booked) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Jadwal yang sudah dipesan tidak dapat diubah.',
            ], 422);
        }

        $slot->update($request->only(['date', 'start_time', 'end_time']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Jadwal berhasil diperbarui.',
            'data'    => new TimeSlotResource($slot),
        ]);
    }

    // DELETE /doctor/slots/{id} — dokter hapus jadwal (hanya yang belum dipesan)
    public function destroy(Request $request, $id)
    {
        $doctor = $request->user()->doctor;
        $slot   = TimeSlot::where('doctor_id', $doctor->id)->findOrFail($id);

        if ($slot->is_booked) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Jadwal yang sudah dipesan tidak dapat dihapus.',
            ], 422);
        }

        $slot->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Jadwal berhasil dihapus.',
        ]);
    }
}
