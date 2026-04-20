<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // POST /api/forgot-password
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem kami.'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Tautan reset password telah dikirim ke email Anda. Silakan cek Inbox atau Spam.'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal mengirim email reset password. Coba lagi nanti.'
        ], 422);
    }
}
