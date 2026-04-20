<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use Illuminate\Http\Request;

class DoctorProfileController extends Controller
{
    // GET /doctor/profile — dokter lihat profil sendiri
    public function show(Request $request)
    {
        $doctor = $request->user()->doctor()->with('user')->first();

        if (!$doctor) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profil dokter tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => new DoctorResource($doctor),
        ]);
    }

    // PUT /doctor/profile — dokter update profil sendiri
    public function update(UpdateDoctorRequest $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profil dokter tidak ditemukan.',
            ], 404);
        }

        $doctor->update($request->only(['specialization', 'phone', 'bio']));

        // Update nama di tabel users
        if ($request->filled('name')) {
            $request->user()->update(['name' => $request->name]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => new DoctorResource($doctor->load('user')),
        ]);
    }
}
