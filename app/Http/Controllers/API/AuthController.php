<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER — hanya untuk pasien (pasien daftar sendiri)
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'patient',
        ]);

        // Otomatis buat profile pasien kosong
        Patient::create(['user_id' => $user->id]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user'   => new UserResource($user),
            'token'  => $token,
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Account is deactivated. Please contact admin.',
            ], 403);
        }

        // Revoke semua token lama
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user'   => new UserResource($user),
            'role'   => $user->role,
            'token'  => $token,
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully',
        ]);
    }

    // GET /me — profil user yang sedang login
    public function me(Request $request)
    {
        $user = $request->user()->load($request->user()->role === 'doctor' ? 'doctor' : 'patient');

        return response()->json([
            'status' => 'success',
            'data'   => new UserResource($user),
        ]);
    }

    // PUT /change-password
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Password saat ini tidak sesuai.',
            ], 422);
        }

        // Update password baru & revoke token
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        $user->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diubah. Silakan login kembali.',
        ]);
    }
}