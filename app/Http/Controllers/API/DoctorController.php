<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\TimeSlotResource;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    // GET /doctors — publik, lihat semua dokter
    public function index(Request $request)
    {
        $query = Doctor::with('user')
            ->whereHas('user', fn($q) => $q->where('is_active', true));

        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $doctors = $query->get();

        return DoctorResource::collection($doctors);
    }

    // GET /doctors/{id} — publik, detail dokter
    public function show($id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new DoctorResource($doctor),
        ]);
    }

    // GET /doctors/{id}/slots — publik, lihat jadwal kosong dokter
    public function slots($id)
    {
        $doctor = Doctor::findOrFail($id);

        $slots = $doctor->timeSlots()
            ->where('is_booked', false)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => TimeSlotResource::collection($slots),
        ]);
    }

    // POST /admin/doctors — admin buat dokter baru
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
            'specialization' => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'bio'            => 'nullable|string|max:1000',
        ]);

        $doctor = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'doctor',
                'is_active' => true,
            ]);

            return Doctor::create([
                'user_id'        => $user->id,
                'specialization' => $request->specialization,
                'phone'          => $request->phone,
                'bio'            => $request->bio,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Dokter berhasil ditambahkan.',
            'data'    => new DoctorResource($doctor->load('user')),
        ], 201);
    }

    // PUT /admin/doctors/{id} — admin edit data dokter
    public function update(UpdateDoctorRequest $request, $id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);

        DB::transaction(function () use ($request, $doctor) {
            $doctor->update($request->only(['specialization', 'phone', 'bio']));

            if ($request->filled('name')) {
                $doctor->user->update(['name' => $request->name]);
            }

            if ($request->filled('is_active')) {
                $doctor->user->update(['is_active' => $request->boolean('is_active')]);
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Data dokter berhasil diperbarui.',
            'data'    => new DoctorResource($doctor->fresh('user')),
        ]);
    }

    // DELETE /admin/doctors/{id} — admin hapus dokter (deactivate)
    public function destroy($id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        $doctor->user->update(['is_active' => false]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dokter berhasil dinonaktifkan.',
        ]);
    }
}
