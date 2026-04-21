<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // GET /admin/patients — admin lihat semua pasien
    public function index(Request $request)
    {
        $query = Patient::with('user');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $patients = $query->get();

        return PatientResource::collection($patients);
    }

    // GET /admin/patients/{id} — admin lihat detail pasien
    public function show($id)
    {
        $patient = Patient::with('user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new PatientResource($patient),
        ]);
    }

    // PUT /admin/patients/{id} — admin edit pasien
    public function update(UpdatePatientRequest $request, $id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        $patient->update($request->only(['date_of_birth', 'gender', 'address', 'phone']));

        if ($request->filled('is_active')) {
            $patient->user->update(['is_active' => $request->boolean('is_active')]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data pasien berhasil diperbarui.',
            'data'    => new PatientResource($patient->fresh('user')),
        ]);
    }

    // DELETE /admin/patients/{id} — admin nonaktifkan pasien
    public function destroy($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        $patient->user->update(['is_active' => false]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pasien berhasil dinonaktifkan.',
        ]);
    }

    // GET /patient/profile — pasien lihat profil sendiri
    public function profile(Request $request)
    {
        $patient = $request->user()->patient()->with('user')->first();

        if (!$patient) {
            return response()->json(['status' => 'error', 'message' => 'Profil pasien tidak ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => new PatientResource($patient),
        ]);
    }

    // PUT /patient/profile — pasien update profil sendiri
    public function updateProfile(UpdatePatientRequest $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json(['status' => 'error', 'message' => 'Profil pasien tidak ditemukan.'], 404);
        }

        $patient->update($request->only(['date_of_birth', 'gender', 'address', 'phone']));

        if ($request->filled('name')) {
            $request->user()->update(['name' => $request->name]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => new PatientResource($patient->load('user')),
        ]);
    }
}
